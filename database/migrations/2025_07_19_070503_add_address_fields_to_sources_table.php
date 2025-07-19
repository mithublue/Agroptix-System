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
        Schema::table('sources', function (Blueprint $table) {
            $table->string('address_line1')->nullable()->after('area');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('country_code', 2)->nullable()->after('address_line2');
            $table->string('state')->nullable()->after('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sources', function (Blueprint $table) {
            $table->dropColumn([
                'address_line1',
                'address_line2',
                'country_code',
                'state'
            ]);
        });
    }
};
