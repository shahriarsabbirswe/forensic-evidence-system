<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investigation_cases', function (Blueprint $table) {
            $table->id();

            $table->string('case_number')->unique();      // DFC-2026-0001
            $table->string('title');
            $table->string('crime_type');
            $table->string('jurisdiction');
            $table->text('description')->nullable();

            $table->enum('status', [
                'open',
                'under_investigation',
                'closed',
                'archived',
            ])->default('open');

            $table->foreignId('opened_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->dateTime('opened_at');

            // Cyber Security Act 2026, Section 32.
            // 90 days, +15 with the controlling officer, +30 with the Tribunal.
            $table->date('investigation_deadline')->nullable();
            $table->unsignedTinyInteger('extension_stage')->default(0);

            $table->timestamps();

            $table->index('status');
            $table->index('crime_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investigation_cases');
    }
};
