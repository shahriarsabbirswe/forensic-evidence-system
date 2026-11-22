<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('evidence', function (Blueprint $table) {
            $table->string('lifecycle_status')->default('active'); // active, archived, disposed
            $table->date('retention_until')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('disposed_at')->nullable();
            $table->foreignId('disposal_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('disposal_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evidence', function (Blueprint $table) {
            $table->dropForeign(['disposal_approved_by']);
            $table->dropColumn([
                'lifecycle_status',
                'retention_until',
                'archived_at',
                'disposed_at',
                'disposal_approved_by',
                'disposal_reason',
            ]);
        });
    }
};