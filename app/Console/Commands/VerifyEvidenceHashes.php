<?php

namespace App\Console\Commands;

use App\Models\Evidence;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('app:verify-evidence-hashes')]
#[Description('Verify SHA-256 hashes of stored digital evidence')]
class VerifyEvidenceHashes extends Command
{
    public function handle()
    {
        $this->info('Starting digital evidence hash verification...');

        $checked = 0;
        $matched = 0;
        $mismatched = 0;
        $missingFiles = 0;
        $missingHashes = 0;

        $evidences = Evidence::where('kind', 'digital')
            ->orderBy('id')
            ->get();

        foreach ($evidences as $evidence) {

            // Check whether the original hash exists
            if (empty($evidence->sha256_hash)) {
                $missingHashes++;

                $this->warn(
                    "Evidence {$evidence->evidence_number}: Original hash is missing."
                );

                continue;
            }

            // Check whether the stored file path exists
            $disk = Storage::disk('local');

            if (empty($evidence->file_path) ||
                !$disk->exists($evidence->file_path)) {

                $missingFiles++;

                $this->error(
                    "Evidence {$evidence->evidence_number}: File is missing."
                );

                continue;
            }

            // Generate the current SHA-256 hash
            $filePath = $disk->path($evidence->file_path);
            $currentHash = hash_file('sha256', $filePath);

            if ($currentHash === false) {
                $missingFiles++;

                $this->error(
                    "Evidence {$evidence->evidence_number}: Could not read file."
                );

                continue;
            }

            $checked++;

            // Compare current hash with the original database hash
            if (hash_equals(
                strtolower($evidence->sha256_hash),
                strtolower($currentHash)
            )) {
                $matched++;

                $this->info(
                    "Evidence {$evidence->evidence_number}: Hash MATCHED."
                );
            } else {
                $mismatched++;

                $this->error(
                    "Evidence {$evidence->evidence_number}: HASH MISMATCH! Possible tampering detected."
                );
            }
        }

        $this->newLine();
        $this->info('Verification Summary');
        $this->line("Total digital evidence: " . $evidences->count());
        $this->line("Files checked: {$checked}");
        $this->info("Hash matched: {$matched}");
        $this->error("Hash mismatched: {$mismatched}");
        $this->warn("Missing files/unreadable files: {$missingFiles}");
        $this->warn("Missing original hashes: {$missingHashes}");

        if ($mismatched > 0 || $missingFiles > 0) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
