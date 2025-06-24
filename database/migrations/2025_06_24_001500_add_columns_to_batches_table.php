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
            $table->decimal('weight', 10, 2)->nullable()->after('harvest_time');
            $table->string('grade', 20)->nullable()->after('weight');
            $table->boolean('has_defect')->default(false)->after('grade');
            $table->string('remark', 50)->nullable()->after('has_defect');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn([
                'weight',
                'grade',
                'has_defect',
                'remark'
            ]);
        });
    }
};
