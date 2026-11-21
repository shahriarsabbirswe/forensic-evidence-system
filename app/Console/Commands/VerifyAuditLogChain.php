<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;
use JsonException;

class VerifyAuditLogChain extends Command
{
    protected $signature = 'app:verify-audit-log-chain';

    protected $description = 'Verify the integrity of the hash-chained audit logs';

    public function handle(): int
    {
        $this->info('Starting audit log chain verification...');

        try {
            $logs = AuditLog::query()
                ->orderBy('id')
                ->get();

            $previousHash = null;

            foreach ($logs as $log) {

                // Verify the connection with the previous log
                if ($log->previous_hash !== $previousHash) {
                    $this->error(
                        "CHAIN BROKEN at Audit Log ID: {$log->id}"
                    );

                    return self::FAILURE;
                }

                // Read original JSON data from database
                $rawChanges = $log->getRawOriginal('changes');

                $changes = $rawChanges
                    ? json_decode($rawChanges, true, 512, JSON_THROW_ON_ERROR)
                    : [];

                // Normalize JSON keys before hash calculation
                $changes = $this->canonicalize($changes);

                // Reconstruct the original payload
                $payload = [
                    'user_id' => $log->user_id === null
                        ? null
                        : (int) $log->user_id,

                    'action' => $log->action,

                    'auditable_type' => $log->auditable_type,

                    'auditable_id' => (int) $log->auditable_id,

                    'changes' => $changes,

                    'ip_address' => $log->ip_address,

                    'previous_hash' => $log->previous_hash,

                    'created_at' => $log->getRawOriginal('created_at'),
                ];

                // Generate SHA-256 hash
                $jsonPayload = json_encode(
                    $payload,
                    JSON_UNESCAPED_SLASHES |
                    JSON_UNESCAPED_UNICODE |
                    JSON_THROW_ON_ERROR
                );

                $calculatedHash = hash('sha256', $jsonPayload);

                // Compare calculated hash with stored hash
                if (!hash_equals($log->row_hash, $calculatedHash)) {
                    $this->error(
                        "HASH MISMATCH at Audit Log ID: {$log->id}"
                    );

                    return self::FAILURE;
                }

                // Move to the next log
                $previousHash = $log->row_hash;
            }

            $this->info('All audit logs are valid!');
            $this->info(
                "Total logs verified: {$logs->count()}"
            );

            return self::SUCCESS;

        } catch (JsonException $exception) {
            $this->error(
                'Invalid JSON data found in audit logs: ' .
                $exception->getMessage()
            );

            return self::FAILURE;
        }
    }

    /**
     * Recursively sort associative array keys.
     * Preserve normal list array order.
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
