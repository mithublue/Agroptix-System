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
        Schema::create('trace_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->string('event_type');
            $table->foreignId('actor_id')->constrained('users');
            $table->string('location')->nullable();
            $table->string('reference_document')->nullable();
            $table->json('data')->nullable();
            $table->string('previous_event_hash')->nullable();
            $table->string('current_hash');
            $table->string('digital_signature')->nullable();
            $table->string('device_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->boolean('is_corrective_action')->default(false);
            $table->foreignId('parent_event_id')->nullable()->constrained('trace_events');
            $table->json('custom_fields')->nullable();
            $table->timestamps();
            
            // Add indexes for performance
            $table->index(['batch_id', 'created_at']);
            $table->index('event_type');
            $table->index('current_hash');
            $table->index('actor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trace_events');
    }
};
