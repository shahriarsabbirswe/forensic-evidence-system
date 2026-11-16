<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Safe to run even if an older version of this table already exists.
        if (Schema::hasTable('evidence_transfers')) {
            return;
        }

        Schema::create('evidence_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evidence_id')->constrained('evidence');
            $table->foreignId('from_user_id')->constrained('users');
            $table->foreignId('to_user_id')->constrained('users');
            $table->foreignId('requested_by')->constrained('users');
            $table->text('purpose');

            // requested -> approved -> released -> completed
            // a transfer can also end as rejected or cancelled
            $table->string('status', 20)->default('requested');

            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->string('approval_note', 500)->nullable();

            $table->timestamp('released_at')->nullable();
            $table->string('release_seal_condition')->nullable();

            $table->timestamp('received_at')->nullable();
            $table->string('receipt_seal_condition')->nullable();

            $table->string('closed_reason', 500)->nullable();
            $table->timestamps();

            $table->index(['evidence_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence_transfers');
    }
};
