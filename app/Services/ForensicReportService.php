<?php

namespace App\Services;

use App\Models\InvestigationCase;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ForensicReportService
{
    /**
     * Build a complete, court-ready forensic report payload for an investigation case.
     */
    public function buildCaseReportData(InvestigationCase $case, User $generator): array
    {
        $case->load([
            'openedBy',
            'assignedUsers',
            'evidence' => function ($query) {
                $query->orderBy('id')->with([
                    'currentHolder',
                    'seizingOfficer',
                    'registeredBy',
                    'acquiredBy',
                    'custodyEntries.fromUser',
                    'custodyEntries.toUser',
                    'custodyEntries.recorder',
                ]);
            },
        ]);

        $evidenceItems = $case->evidence->map(function ($item) {
            $integrityStatus = 'PHYSICAL / NO DIGITAL FILE';

            if ($item->isDigital() && $item->file_path && $item->sha256_hash) {
                $disk = Storage::disk('local');
                if ($disk->exists($item->file_path)) {
                    $liveHash = hash_file('sha256', $disk->path($item->file_path));
                    $integrityStatus = hash_equals(strtolower($item->sha256_hash), strtolower($liveHash))
                        ? 'VERIFIED (HASH MATCHED)'
                        : 'WARNING: HASH MISMATCH';
                } else {
                    $integrityStatus = 'MISSING FILE IN STORAGE';
                }
            }

            return [
                'model' => $item,
                'integrity_status' => $integrityStatus,
            ];
        });

        $generatedAt = now();
        $reportReference = sprintf(
            'DFEMS-RPT-%s-%s',
            $case->case_number ?? $case->id,
            $generatedAt->format('YmdHis')
        );

        // Create a deterministic SHA-256 fingerprint of the report contents for court verification
        $fingerprintSource = json_encode([
            'report_reference' => $reportReference,
            'case_id' => $case->id,
            'case_number' => $case->case_number,
            'generated_at' => $generatedAt->toIso8601String(),
            'generated_by' => $generator->id,
            'evidence_hashes' => $case->evidence->pluck('sha256_hash', 'evidence_number')->toArray(),
            'custody_count' => $case->evidence->sum(fn ($e) => $e->custodyEntries->count()),
        ]);

        return [
            'reportReference' => $reportReference,
            'reportChecksum' => hash('sha256', (string) $fingerprintSource),
            'generatedAt' => $generatedAt,
            'generator' => $generator,
            'case' => $case,
            'evidenceItems' => $evidenceItems,
            'totalEvidence' => $case->evidence->count(),
            'digitalCount' => $case->evidence->where('kind', 'digital')->count(),
            'physicalCount' => $case->evidence->where('kind', '!=', 'digital')->count(),
            'documentId' => Str::uuid()->toString(),
        ];
    }
}