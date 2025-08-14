<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Idempotent backfill: set batches.producer_id from sources.owner_id where null
        if (Schema::hasTable('batches') && Schema::hasTable('sources')) {
            DB::statement('UPDATE batches b JOIN sources s ON s.id = b.source_id SET b.producer_id = s.owner_id WHERE b.producer_id IS NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: cannot reliably undo data backfill
    }
};
