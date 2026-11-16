<?php

namespace App\Services;

use App\Models\CustodyEntry;
use App\Models\Evidence;
use App\Models\EvidenceTransfer;
use Carbon\CarbonInterface;

class CustodyLedgerService
{
    /**
     * Record the initial custody intake entry when evidence is registered (FR2 -> FR5).
     */
    public function recordRegistration(Evidence $evidence, int $actorId): CustodyEntry
    {
        return CustodyEntry::create([
            'evidence_id'            => $evidence->id,
            'evidence_transfer_id'   => null,
            'action'                 => 'registered',
            'from_user_id'           => null,
            'to_user_id'             => $evidence->current_holder_id ?? $actorId,
            'recorded_by'            => $actorId,
            'release_seal_condition' => null,
            'receipt_seal_condition' => 'Sealed at intake',
            'reason'                 => 'Initial evidence seizure and registration (' . $evidence->evidence_number . ')',
            'occurred_at'            => $evidence->seized_at ?? now(),
        ]);
    }

    /**
     * Record a completed dual-sign-off transfer into the immutable ledger (FR6 -> FR5).
     */
    public function recordTransferCompleted(EvidenceTransfer $transfer, int $actorId): CustodyEntry
    {
        return CustodyEntry::create([
            'evidence_id'            => $transfer->evidence_id,
            'evidence_transfer_id'   => $transfer->id,
            'action'                 => 'transferred',
            'from_user_id'           => $transfer->from_user_id,
            'to_user_id'             => $transfer->to_user_id,
            'recorded_by'            => $actorId,
            'release_seal_condition' => $transfer->release_seal_condition,
            'receipt_seal_condition' => $transfer->receipt_seal_condition,
            'reason'                 => $transfer->purpose,
            'occurred_at'            => $transfer->received_at ?? now(),
        ]);
    }

    /**
     * Generic append method for any future custody events (e.g., accessed, archived).
     */
    public function recordCustomEvent(
        Evidence $evidence,
        string $action,
        int $recordedBy,
        ?int $fromUserId = null,
        ?int $toUserId = null,
        ?string $reason = null,
        ?CarbonInterface $occurredAt = null
    ): CustodyEntry {
        return CustodyEntry::create([
            'evidence_id'   => $evidence->id,
            'action'        => $action,
            'from_user_id'  => $fromUserId,
            'to_user_id'    => $toUserId,
            'recorded_by'   => $recordedBy,
            'reason'        => $reason,
            'occurred_at'   => $occurredAt ?? now(),
        ]);
    }
}