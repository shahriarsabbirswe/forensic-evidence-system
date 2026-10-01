<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Acquisition detail and integrity fields, taken from the CS 422
 * acquisition worksheet (sections 8A and 8B).
 *
 * Everything is nullable, because physical exhibits are not acquired
 * with a tool and existing rows must stay valid.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evidence', function (Blueprint $table) {

            // --- Source condition at the time of acquisition ---
            // e.g. powered off, powered on and unlocked, encrypted, PIN locked
            $table->string('source_state')->nullable()->after('serial_number');

            // --- Write protection (the examiner must be able to name it) ---
            $table->string('write_blocker')->nullable()->after('source_state');

            // --- How it was acquired ---
            // Kept as two columns because the two choices are independent:
            // a dead acquisition can be physical or logical.
            $table->enum('acquisition_method', ['live', 'dead'])->nullable()->after('write_blocker');
            $table->enum('acquisition_scope', ['physical', 'logical'])->nullable()->after('acquisition_method');
            $table->enum('image_format', ['raw', 'e01', 'aff4', 'ad1', 'other'])->nullable()->after('acquisition_scope');

            // --- Tool and examiner, for repeatability ---
            $table->string('acquisition_tool')->nullable()->after('image_format');
            $table->string('acquisition_tool_version')->nullable()->after('acquisition_tool');
            $table->foreignId('acquired_by')->nullable()->after('acquisition_tool_version')
                  ->constrained('users')->nullOnDelete();

            // --- Timing. The time zone is recorded explicitly, never assumed. ---
            $table->dateTime('acquisition_started_at')->nullable()->after('acquired_by');
            $table->dateTime('acquisition_completed_at')->nullable()->after('acquisition_started_at');
            $table->string('acquisition_timezone', 64)->default('Asia/Dhaka')->after('acquisition_completed_at');

            // --- Integrity record (section 8B) ---
            // The algorithm is stored rather than assumed, so the record
            // still reads correctly if anything other than SHA-256 is ever used.
            $table->string('hash_algorithm', 20)->default('sha256')->after('sha256_hash');

            // --- Errors, limitations and unavoidable changes ---
            $table->text('acquisition_notes')->nullable()->after('acquisition_timezone');
        });
    }

    public function down(): void
    {
        Schema::table('evidence', function (Blueprint $table) {
            $table->dropConstrainedForeignId('acquired_by');

            $table->dropColumn([
                'source_state',
                'write_blocker',
                'acquisition_method',
                'acquisition_scope',
                'image_format',
                'acquisition_tool',
                'acquisition_tool_version',
                'acquisition_started_at',
                'acquisition_completed_at',
                'acquisition_timezone',
                'hash_algorithm',
                'acquisition_notes',
            ]);
        });
    }
};
