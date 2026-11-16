<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FR5 (Sabbir Ahmed). Proposed columns: agree them with him before running.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('custody_entries')) {
            return;
        }

        Schema::create('custody_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evidence_id')->constrained('evidence');
            $table->foreignId('evidence_transfer_id')->nullable()->constrained('evidence_transfers');
            $table->string('action', 40);                    // registered, transferred, received, accessed
            $table->foreignId('from_user_id')->nullable()->constrained('users');
            $table->foreignId('to_user_id')->nullable()->constrained('users');
            $table->foreignId('recorded_by')->constrained('users');
            $table->string('release_seal_condition')->nullable();
            $table->string('receipt_seal_condition')->nullable();
            $table->string('reason', 500)->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['evidence_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custody_entries');
    }
};
