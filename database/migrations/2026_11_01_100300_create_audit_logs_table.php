<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FR8 (Jarif Hossain). Proposed columns: agree them with him before running.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('action', 40);                    // created, updated, deleted
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->json('changes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->char('previous_hash', 64)->nullable();   // hash of the row before this one
            $table->char('row_hash', 64);                    // hash of this row
            $table->timestamp('created_at')->useCurrent();   // no updated_at: rows are never edited

            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
