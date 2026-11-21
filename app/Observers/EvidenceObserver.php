<?php

namespace App\Observers;

use App\Models\Evidence;
use App\Services\AuditLogService;

class EvidenceObserver
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function created(Evidence $evidence): void
    {
        $this->auditLogService->createLog(
            'created',
            Evidence::class,
            $evidence->id,
            [
                'evidence_number' => $evidence->evidence_number,
                'kind' => $evidence->kind,
                'file_path' => $evidence->file_path,
                'sha256_hash' => $evidence->sha256_hash,
            ],
            auth()->id(),
            app()->runningInConsole() ? null : request()->ip()
        );
    }

    public function updated(Evidence $evidence): void
    {
        $this->auditLogService->createLog(
            'updated',
            Evidence::class,
            $evidence->id,
            [
                'changed_fields' => $evidence->getChanges(),
            ],
            auth()->id(),
            app()->runningInConsole() ? null : request()->ip()
        );
    }

    public function deleted(Evidence $evidence): void
    {
        $this->auditLogService->createLog(
            'deleted',
            Evidence::class,
            $evidence->id,
            [
                'evidence_number' => $evidence->evidence_number,
                'kind' => $evidence->kind,
            ],
            auth()->id(),
            app()->runningInConsole() ? null : request()->ip()
        );
    }
}
