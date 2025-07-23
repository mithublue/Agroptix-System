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
        Schema::table('batches', function (Blueprint $table) {
            // Check if status column exists and update it if necessary
            if (!Schema::hasColumn('batches', 'status')) {
                $table->string('status', 50)->default('created')->after('id');
            } else {
                // Update existing status column to ensure it has the correct properties
                $table->string('status', 50)->default('created')->change();
            }
            
            // Add trace_code column if it doesn't exist
            if (!Schema::hasColumn('batches', 'trace_code')) {
                $table->string('trace_code', 50)->unique()->after('status');
            }
            
            // Add indexes for performance
            $table->index('status');
            $table->index('trace_code');
        });
        
        // Generate trace codes for existing batches
        \App\Models\Batch::whereNull('trace_code')->each(function ($batch) {
            $batch->update([
                'trace_code' => 'BATCH-' . strtoupper(\Illuminate\Support\Str::random(8))
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            // Remove indexes first
            $table->dropIndex(['status']);
            
            // Only drop the trace_code column if it exists
            if (Schema::hasColumn('batches', 'trace_code')) {
                // Drop the index first to avoid errors
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('batches');
                
                foreach ($indexes as $index) {
                    if (in_array('trace_code', $index->getColumns())) {
                        $table->dropIndex($index->getName());
                        break;
                    }
                }
                
                // Now drop the column
                $table->dropColumn('trace_code');
            }
        });
    }
};
