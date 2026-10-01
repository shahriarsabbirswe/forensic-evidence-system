<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvidenceRequest;
use App\Http\Requests\UpdateEvidenceRequest;
use App\Models\Evidence;
use App\Models\InvestigationCase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EvidenceController extends Controller
{
    public function create(InvestigationCase $case)
    {
        return view('evidence.create', [
            'case'     => $case,
            'evidence' => new Evidence(),
            'officers' => User::orderBy('name')->get(),
        ]);
    }

    public function store(StoreEvidenceRequest $request, InvestigationCase $case)
    {
        $evidence = DB::transaction(function () use ($request, $case) {

            $data = $request->validated();

            $data['investigation_case_id'] = $case->id;
            $data['evidence_number']       = $this->nextEvidenceNumber();
            $data['registered_by']         = $request->user()->id;
            $data['status']                = 'registered';

            // Cyber Security Act 2026, Section 36: 90 day preservation
            // window, counted from the moment of seizure.
            $data['preservation_expires_at'] = Carbon::parse($data['seized_at'])
                ->addDays(90)
                ->toDateString();

            if ($request->hasFile('evidence_file')) {
                $file = $request->file('evidence_file');

                $data['original_filename'] = $file->getClientOriginalName();
                $data['file_size']         = $file->getSize();

                // Stored privately under storage/app/evidence,
                // never in the public folder.
                $data['file_path'] = $file->store('evidence', 'local');

                /* ------------------------------------------------------------
                 | FR3 BELONGS HERE. Jarif Hossain.
                 |
                 | At this point the file is on disk and $data['file_path']
                 | holds its path relative to the 'local' disk.
                 |
                 | Your code must, before this closure returns:
                 |   - calculate a SHA-256 hash of the file AS STORED,
                 |     not of the upload before it was moved
                 |   - put the value in $data['sha256_hash']
                 |   - set $data['hash_algorithm'] and $data['hashed_at']
                 |
                 | It has to happen inside this transaction. If the record
                 | saves and the hash does not, that item has no baseline
                 | and can never be verified again.
                 |
                 | Storage::disk('local')->path() converts the stored path
                 | into an absolute path on disk.
                 * ---------------------------------------------------------- */
            }

            unset($data['evidence_file']);

            return Evidence::create($data);
        });

        return redirect()
            ->route('evidence.show', $evidence)
            ->with('status', "Evidence {$evidence->evidence_number} registered.");
    }

    public function show(Evidence $evidence)
    {
        $evidence->load(['investigationCase', 'seizingOfficer', 'registeredBy', 'acquiredBy']);

        return view('evidence.show', compact('evidence'));
    }

    public function edit(Evidence $evidence)
    {
        return view('evidence.edit', [
            'evidence' => $evidence,
            'case'     => $evidence->investigationCase,
            'officers' => User::orderBy('name')->get(),
        ]);
    }

    /**
     * Metadata can be corrected. The file, its hash and the evidence
     * number cannot be replaced through this route.
     */
    public function update(UpdateEvidenceRequest $request, Evidence $evidence)
    {
        $evidence->update($request->validated());

        return redirect()
            ->route('evidence.show', $evidence)
            ->with('status', 'Evidence record updated.');
    }

    public function download(Evidence $evidence)
    {
        abort_if(! $evidence->file_path, 404, 'This item has no stored file.');

        return Storage::disk('local')->download(
            $evidence->file_path,
            $evidence->original_filename ?? basename($evidence->file_path)
        );
    }

    private function nextEvidenceNumber(): string
    {
        $year = now()->year;

        $last = Evidence::where('evidence_number', 'like', "EV-{$year}-%")
            ->lockForUpdate()
            ->orderByDesc('evidence_number')
            ->value('evidence_number');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return sprintf('EV-%d-%04d', $year, $next);
    }
}
