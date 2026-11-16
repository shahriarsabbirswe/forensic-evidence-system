<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('evidence', 'current_holder_id')) {
            Schema::table('evidence', function (Blueprint $table) {
                $table->foreignId('current_holder_id')
                    ->nullable()
                    ->after('registered_by')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        // Evidence that already exists is held by the person who registered it.
        DB::table('evidence')
            ->whereNull('current_holder_id')
            ->update(['current_holder_id' => DB::raw('registered_by')]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('evidence', 'current_holder_id')) {
            Schema::table('evidence', function (Blueprint $table) {
                $table->dropConstrainedForeignId('current_holder_id');
            });
        }
    }
};
