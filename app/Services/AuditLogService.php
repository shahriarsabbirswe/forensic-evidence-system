<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class AuditLogService
{
    public function createLog(
        string $action,
        string $auditableType,
        int $auditableId,
        array $changes = [],
        ?int $userId = null,
        ?string $ipAddress = null
    ): AuditLog {
        return DB::transaction(function () use (
            $action,
            $auditableType,
            $auditableId,
            $changes,
            $userId,
            $ipAddress
        ) {
            $lastLog = AuditLog::query()
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $previousHash = $lastLog?->row_hash;

            $createdAt = now()->toDateTimeString();

            // Normalize JSON keys before hashing
            $changes = $this->canonicalize($changes);

            $payload = [
                'user_id' => $userId,
                'action' => $action,
                'auditable_type' => $auditableType,
                'auditable_id' => $auditableId,
                'changes' => $changes,
                'ip_address' => $ipAddress,
                'previous_hash' => $previousHash,
                'created_at' => $createdAt,
            ];

            $jsonPayload = json_encode(
                $payload,
                JSON_UNESCAPED_SLASHES |
                JSON_UNESCAPED_UNICODE |
                JSON_THROW_ON_ERROR
            );

            $rowHash = hash('sha256', $jsonPayload);

            return AuditLog::create([
                ...$payload,
                'row_hash' => $rowHash,
            ]);
        });
    }

    /**
     * Recursively sort associative array keys.
     * Preserve the order of normal list arrays.
     */
    private function canonicalize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->canonicalize($value);
            }
        }

        if (!array_is_list($data)) {
            ksort($data);
        }

        return $data;
    }
}
