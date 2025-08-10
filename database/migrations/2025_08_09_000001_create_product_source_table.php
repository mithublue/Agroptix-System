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
        Schema::create('product_source', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('source_id');
            $table->timestamps();

            $table->unique(['product_id', 'source_id']);

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('source_id')
                  ->references('id')->on('sources')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_source');
    }
};
