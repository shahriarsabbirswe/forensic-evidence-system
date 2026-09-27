<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('investigation_case_id')
                ->constrained('investigation_cases')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('role_in_case');   // lead investigator, analyst, custody officer
            $table->dateTime('assigned_at');

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // One person holds one role on a case.
            $table->unique(['investigation_case_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_assignments');
    }
};
