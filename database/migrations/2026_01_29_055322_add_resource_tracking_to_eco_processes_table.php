<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('eco_processes', function (Blueprint $table) {
            $table->decimal('water_usage', 10, 2)->default(0)->after('stage');
            $table->decimal('energy_usage', 10, 2)->default(0)->after('water_usage');
            $table->decimal('waste_generated', 10, 2)->default(0)->after('energy_usage');
        });

        // Backfill data from JSON
        $processes = DB::table('eco_processes')->get();
        foreach ($processes as $process) {
            $data = json_decode($process->data, true) ?? [];
            $updates = [];

            if (isset($data['washing_water_usage'])) {
                $updates['water_usage'] = $data['washing_water_usage'];
            } elseif (isset($data['washwater_amount'])) {
                $updates['water_usage'] = $data['washwater_amount'];
            }

            if (isset($data['rejection_weight'])) {
                $updates['waste_generated'] = $data['rejection_weight'];
            }

            if (!empty($updates)) {
                DB::table('eco_processes')
                    ->where('id', $process->id)
                    ->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eco_processes', function (Blueprint $table) {
            $table->dropColumn(['water_usage', 'energy_usage', 'waste_generated']);
        });
    }
};
