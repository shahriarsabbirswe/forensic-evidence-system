<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvidenceTransferRequest;
use App\Models\Evidence;
use App\Models\EvidenceTransfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * FR6: evidence transfer with supervisor approval and dual sign off.
 *
 * Flow:  requested -> approved (supervisor) -> released (current holder)
 *        -> completed (receiver). Custody only changes at "completed".
 */
class EvidenceTransferController extends Controller
{
    /* ---------------------------------------------------------------
     | Screens
     |--------------------------------------------------------------- */

    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user->can('transfer.view'), 403);

        $transfers = EvidenceTransfer::with(['evidence', 'fromUser', 'toUser'])
            // People without "view_all" only see transfers they take part in.
            ->when(! $user->can('transfer.view_all'), function ($q) use ($user) {
                $q->where(function ($w) use ($user) {
                    $w->where('from_user_id', $user->id)
                        ->orWhere('to_user_id', $user->id)
                        ->orWhere('requested_by', $user->id);
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('transfers.index', compact('transfers'));
    }

    public function create(Evidence $evidence)
    {
        $this->authorizeRequest(auth()->user(), $evidence);

        if ($evidence->openTransfer()) {
            return redirect($evidence->showUrl())
                ->with('error', 'A transfer for this item is already in progress.');
        }

        $recipients = User::where('id', '!=', $evidence->current_holder_id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('transfers.create', compact('evidence', 'recipients'));
    }

    public function show(EvidenceTransfer $transfer)
    {
        $user = auth()->user();
        abort_unless($user->can('transfer.view'), 403);
        abort_unless($this->canSee($user, $transfer), 403);

        $transfer->load(['evidence.currentHolder', 'fromUser', 'toUser', 'requester', 'approver']);

        return view('transfers.show', compact('transfer'));
    }

    /* ---------------------------------------------------------------
     | Step 1: request (current holder)
     |--------------------------------------------------------------- */

    public function store(StoreEvidenceTransferRequest $request, Evidence $evidence)
    {
        $user = $request->user();
        $this->authorizeRequest($user, $evidence);

        $transfer = DB::transaction(function () use ($request, $evidence, $user) {
            // Lock the evidence row so two requests made at the same moment
            // cannot both pass the "no transfer in progress" check.
            $locked = Evidence::whereKey($evidence->id)->lockForUpdate()->first();

            if ($locked->openTransfer()) {
                return null;
            }

            return EvidenceTransfer::create([
                'evidence_id'  => $locked->id,
                'from_user_id' => $locked->current_holder_id,
                'to_user_id'   => $request->integer('to_user_id'),
                'requested_by' => $user->id,
                'purpose'      => $request->input('purpose'),
                'status'       => EvidenceTransfer::REQUESTED,
            ]);
        });

        if (! $transfer) {
            return redirect($evidence->showUrl())
                ->with('error', 'A transfer for this item is already in progress.');
        }

        return redirect()->route('transfers.show', $transfer)
            ->with('success', 'Transfer requested. Waiting for supervisor approval.');
    }

    /* ---------------------------------------------------------------
     | Step 2: supervisor decision
     |--------------------------------------------------------------- */

    public function approve(Request $request, EvidenceTransfer $transfer)
    {
        $user = $request->user();
        $this->authorizeApprover($user, $transfer);

        $data = $request->validate(['approval_note' => ['nullable', 'string', 'max:500']]);

        $ok = $this->transition($transfer, EvidenceTransfer::REQUESTED, [
            'status'        => EvidenceTransfer::APPROVED,
            'approved_by'   => $user->id,
            'approved_at'   => now(),
            'approval_note' => $data['approval_note'] ?? null,
        ]);

        return $this->result($transfer, $ok, 'Transfer approved. Waiting for the holder to confirm release.');
    }

    public function reject(Request $request, EvidenceTransfer $transfer)
    {
        $user = $request->user();
        $this->authorizeApprover($user, $transfer);

        $data = $request->validate(['closed_reason' => ['required', 'string', 'min:5', 'max:500']]);

        $ok = $this->transition($transfer, EvidenceTransfer::REQUESTED, [
            'status'        => EvidenceTransfer::REJECTED,
            'approved_by'   => $user->id,
            'approved_at'   => now(),
            'closed_reason' => $data['closed_reason'],
        ]);

        return $this->result($transfer, $ok, 'Transfer rejected.');
    }

    /* ---------------------------------------------------------------
     | Step 3: release (only the person who holds the item now)
     |--------------------------------------------------------------- */

    public function confirmRelease(Request $request, EvidenceTransfer $transfer)
    {
        $user = $request->user();

        // Permission is not enough here: it must be THIS person.
        abort_unless($user->id === (int) $transfer->from_user_id, 403);

        $data = $request->validate(['seal_condition' => ['required', 'string', 'min:3', 'max:255']]);

        $ok = $this->transition($transfer, EvidenceTransfer::APPROVED, [
            'status'                 => EvidenceTransfer::RELEASED,
            'released_at'            => now(),
            'release_seal_condition' => $data['seal_condition'],
        ]);

        return $this->result($transfer, $ok, 'Release confirmed. Waiting for the receiver to confirm receipt.');
    }

    /* ---------------------------------------------------------------
     | Step 4: receipt (only the person receiving the item)
     |--------------------------------------------------------------- */

    public function confirmReceipt(Request $request, EvidenceTransfer $transfer)
    {
        $user = $request->user();

        abort_unless($user->id === (int) $transfer->to_user_id, 403);

        $data = $request->validate(['seal_condition' => ['required', 'string', 'min:3', 'max:255']]);

        $ok = $this->transition(
            $transfer,
            EvidenceTransfer::RELEASED,
            [
                'status'                 => EvidenceTransfer::COMPLETED,
                'received_at'            => now(),
                'receipt_seal_condition' => $data['seal_condition'],
            ],
            // Runs inside the same database transaction, so the transfer and
            // the change of holder are saved together or not at all.
            function (EvidenceTransfer $done) {
                $evidence = $done->evidence;
                $evidence->current_holder_id = $done->to_user_id;
                $evidence->save();

                // ============================================================
                // FR5 BELONGS HERE (Sabbir Ahmed)
                // The transfer is complete. Write one entry into the custody
                // ledger (custody_entries) using $done and $evidence.
                // Useful values: $done->from_user_id, $done->to_user_id,
                // $done->release_seal_condition, $done->receipt_seal_condition,
                // $done->purpose, $done->received_at.
                // ============================================================
            }
        );

        return $this->result($transfer, $ok, 'Receipt confirmed. Custody has moved to the receiver.');
    }

    /* ---------------------------------------------------------------
     | Cancel (before release)
     |--------------------------------------------------------------- */

    public function cancel(Request $request, EvidenceTransfer $transfer)
    {
        $user = $request->user();

        abort_unless(
            in_array($user->id, [(int) $transfer->from_user_id, (int) $transfer->requested_by], true),
            403
        );

        $expected = $transfer->status;
        abort_unless(in_array($expected, [EvidenceTransfer::REQUESTED, EvidenceTransfer::APPROVED], true), 403);

        $ok = $this->transition($transfer, $expected, [
            'status'        => EvidenceTransfer::CANCELLED,
            'closed_reason' => 'Cancelled by ' . $user->name . ' before release.',
        ]);

        return $this->result($transfer, $ok, 'Transfer cancelled.');
    }

    /* ---------------------------------------------------------------
     | Helpers
     |--------------------------------------------------------------- */

    /** Permission AND identity: only the current holder may start a transfer. */
    private function authorizeRequest(User $user, Evidence $evidence): void
    {
        abort_unless($user->can('transfer.request'), 403);
        abort_unless((int) $evidence->current_holder_id === $user->id, 403, 'Only the person holding this item can request its transfer.');
    }

    /** Approver needs the permission and must not be one of the two parties. */
    private function authorizeApprover(User $user, EvidenceTransfer $transfer): void
    {
        abort_unless($user->can('transfer.approve'), 403);
        abort_if(
            in_array($user->id, [(int) $transfer->from_user_id, (int) $transfer->to_user_id], true),
            403,
            'An approver cannot be a party to the transfer.'
        );
    }

    private function canSee(User $user, EvidenceTransfer $transfer): bool
    {
        return $user->can('transfer.view_all')
            || in_array($user->id, [
                (int) $transfer->from_user_id,
                (int) $transfer->to_user_id,
                (int) $transfer->requested_by,
            ], true);
    }

    /**
     * Move a transfer from the status it must currently have to a new one.
     * The row is locked and the status is checked again inside a transaction,
     * so a double click or two people clicking together cannot repeat a step.
     * Using save() (not a raw update) keeps model events working for the audit log.
     */
    private function transition(EvidenceTransfer $transfer, string $expected, array $changes, ?callable $after = null): bool
    {
        return DB::transaction(function () use ($transfer, $expected, $changes, $after) {
            $fresh = EvidenceTransfer::whereKey($transfer->id)->lockForUpdate()->first();

            if (! $fresh || $fresh->status !== $expected) {
                return false;
            }

            $fresh->update($changes);

            if ($after) {
                $after($fresh);
            }

            return true;
        });
    }

    private function result(EvidenceTransfer $transfer, bool $ok, string $message)
    {
        return redirect()->route('transfers.show', $transfer)->with(
            $ok ? 'success' : 'error',
            $ok ? $message : 'This step was already done or is not allowed at this stage.'
        );
    }
}
