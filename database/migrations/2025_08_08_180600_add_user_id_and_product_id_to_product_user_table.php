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
        Schema::table('product_user', function (Blueprint $table) {
            if (!Schema::hasColumn('product_user', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained('users')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('product_user', 'product_id')) {
                $table->foreignId('product_id')->after('user_id')->constrained('products')->cascadeOnDelete();
            }

            // Optional: ensure a user cannot have duplicate product entries
            // Commented out to avoid issues if index already exists
            // $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_user', function (Blueprint $table) {
            if (Schema::hasColumn('product_user', 'product_id')) {
                try { $table->dropForeign(['product_id']); } catch (\Throwable $e) {}
                $table->dropColumn('product_id');
            }
            if (Schema::hasColumn('product_user', 'user_id')) {
                try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}
                $table->dropColumn('user_id');
            }
        });
    }
};
