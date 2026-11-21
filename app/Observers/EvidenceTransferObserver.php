<?php

namespace App\Observers;

use App\Models\EvidenceTransfer;
use App\Services\AuditLogService;

class EvidenceTransferObserver
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function created(EvidenceTransfer $evidenceTransfer): void
    {
        $this->auditLogService->createLog(
            'created',
            EvidenceTransfer::class,
            $evidenceTransfer->id,
            [
                'evidence_id' => $evidenceTransfer->evidence_id,
                'from_user_id' => $evidenceTransfer->from_user_id,
                'to_user_id' => $evidenceTransfer->to_user_id,
                'requested_by' => $evidenceTransfer->requested_by,
                'status' => $evidenceTransfer->status,
            ],
            auth()->id(),
            app()->runningInConsole() ? null : request()->ip()
        );
    }

    public function updated(EvidenceTransfer $evidenceTransfer): void
    {
        $this->auditLogService->createLog(
            'updated',
            EvidenceTransfer::class,
            $evidenceTransfer->id,
            [
                'changed_fields' => $evidenceTransfer->getChanges(),
            ],
            auth()->id(),
            app()->runningInConsole() ? null : request()->ip()
        );
    }

    public function deleted(EvidenceTransfer $evidenceTransfer): void
    {
        $this->auditLogService->createLog(
            'deleted',
            EvidenceTransfer::class,
            $evidenceTransfer->id,
            [
                'evidence_id' => $evidenceTransfer->evidence_id,
                'status' => $evidenceTransfer->status,
            ],
            auth()->id(),
            app()->runningInConsole() ? null : request()->ip()
        );
    }
}
