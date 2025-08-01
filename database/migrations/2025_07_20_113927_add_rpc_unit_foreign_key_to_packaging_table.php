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
        Schema::table('packaging', function (Blueprint $table) {
            $table->foreign('rpc_unit_id')
                  ->references('id')
                  ->on('rpc_units')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packaging', function (Blueprint $table) {
            $table->dropForeign(['rpc_unit_id']);
        });
    }
};
