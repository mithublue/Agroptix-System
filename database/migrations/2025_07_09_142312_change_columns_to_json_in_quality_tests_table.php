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
        // First, ensure the columns exist before trying to modify them
        Schema::table('quality_tests', function (Blueprint $table) {
            // Change parameter_tested to JSON
            if (Schema::hasColumn('quality_tests', 'parameter_tested')) {
                $table->json('parameter_tested')->nullable()->change();
            }
            
            // Change result_status to JSON
            if (Schema::hasColumn('quality_tests', 'result_status')) {
                $table->json('result_status')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the changes if needed
        Schema::table('quality_tests', function (Blueprint $table) {
            if (Schema::hasColumn('quality_tests', 'parameter_tested')) {
                $table->text('parameter_tested')->nullable()->change();
            }
            
            if (Schema::hasColumn('quality_tests', 'result_status')) {
                $table->string('result_status')->nullable()->change();
            }
        });
    }
};
