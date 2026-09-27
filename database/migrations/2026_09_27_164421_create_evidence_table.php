<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table name is 'evidence', not 'evidences'.
        Schema::create('evidence', function (Blueprint $table) {
            $table->id();

            $table->foreignId('investigation_case_id')
                ->constrained('investigation_cases')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('evidence_number')->unique();  // EV-2026-0001
            $table->enum('kind', ['digital', 'physical']);

            // Seizure metadata (FR2)
            $table->string('device_type');
            $table->string('make_model')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('description');
            $table->string('source');
            $table->dateTime('seized_at');
            $table->string('seizure_location');

            $table->foreignId('seizing_officer_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('witness_name')->nullable();

            // Digital evidence only (FR3)
            $table->string('original_filename')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->char('sha256_hash', 64)->nullable();
            $table->dateTime('hashed_at')->nullable();

            // Physical evidence only
            $table->string('storage_locker')->nullable();

            $table->enum('status', [
                'registered',
                'in_analysis',
                'archived',
                'disposed',
            ])->default('registered');

            $table->foreignId('registered_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Cyber Security Act 2026, Section 36.
            // Preservation 90 days, extendable by the Tribunal to 180.
            $table->date('preservation_expires_at')->nullable();

            $table->timestamps();

            $table->index('sha256_hash');
            $table->index('status');
            $table->index('seized_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence');
    }
};
