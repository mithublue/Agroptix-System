<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, ensure the column exists and is text type
        if (Schema::hasColumn('quality_tests', 'parameter_tested')) {
            // First, fix any invalid JSON data
            $tests = DB::table('quality_tests')->get();
            
            foreach ($tests as $test) {
                $updateData = [];
                
                // Fix parameter_tested
                if (!empty($test->parameter_tested)) {
                    try {
                        json_decode($test->parameter_tested, true, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $e) {
                        // If not valid JSON, wrap it in an array
                        $updateData['parameter_tested'] = json_encode([$test->parameter_tested]);
                    }
                } else {
                    $updateData['parameter_tested'] = '[]';
                }
                
                // Fix result_status
                if (!empty($test->result_status)) {
                    try {
                        json_decode($test->result_status, true, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $e) {
                        // If not valid JSON, create a simple object
                        $updateData['result_status'] = json_encode(['status' => $test->result_status]);
                    }
                } else {
                    $updateData['result_status'] = '{}';
                }
                
                if (!empty($updateData)) {
                    DB::table('quality_tests')
                        ->where('id', $test->id)
                        ->update($updateData);
                }
            }
            
            // Now alter the column types
            DB::statement("ALTER TABLE quality_tests MODIFY parameter_tested JSON NOT NULL DEFAULT (JSON_ARRAY())");
            DB::statement("ALTER TABLE quality_tests MODIFY result_status JSON NOT NULL DEFAULT (JSON_OBJECT())");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to text type if needed
        if (Schema::hasColumn('quality_tests', 'parameter_tested')) {
            DB::statement("ALTER TABLE quality_tests MODIFY parameter_tested TEXT NULL");
        }
        
        if (Schema::hasColumn('quality_tests', 'result_status')) {
            DB::statement("ALTER TABLE quality_tests MODIFY result_status TEXT NULL");
        }
    }
};
