<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('supplier_id')->constrained('users')->cascadeOnDelete();
                $table->nullableMorphs('subject'); // creates subject_type, subject_id and index
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamp('last_message_at')->nullable();
                $table->boolean('is_closed')->default(false);
                $table->foreignId('closed_by_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('closed_at')->nullable();
                $table->timestamps();

                $table->index(['customer_id', 'supplier_id']);
                // no need to add index for subject_type/subject_id; nullableMorphs already adds it
                $table->index('last_message_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
