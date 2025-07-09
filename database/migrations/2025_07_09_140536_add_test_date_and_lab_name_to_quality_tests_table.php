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
        Schema::table('quality_tests', function (Blueprint $table) {
            $table->date('test_date')->nullable()->after('batch_id');
            $table->string('lab_name')->nullable()->after('test_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quality_tests', function (Blueprint $table) {
            $table->dropColumn(['test_date', 'lab_name']);
        });
    }
};
