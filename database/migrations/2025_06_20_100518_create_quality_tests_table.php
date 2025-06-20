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
        Schema::disableForeignKeyConstraints();

        Schema::create('quality_tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('parameter_tested', 50, )->nullable();
            $table->string('result', 100, )->nullable();
            $table->string('result_status', 10, )->nullable();

            $table->foreign('batch_id')
                  ->references('id')
                  ->on('batches')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_tests');
    }
};
