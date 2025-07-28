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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->dateTime('delivery_date');
            $table->text('delivery_notes')->nullable();
            $table->string('delivery_person');
            $table->string('delivery_contact');
            $table->text('delivery_address');
            $table->string('delivery_status');
            $table->string('signature_recipient_name')->nullable();
            $table->text('signature_data')->nullable();
            $table->boolean('delivery_confirmation')->default(false);
            $table->boolean('temperature_check')->default(false);
            $table->boolean('quality_check')->default(false);
            $table->text('additional_notes')->nullable();
            $table->integer('customer_rating')->nullable()->comment('Rating from 1 to 5');
            $table->text('customer_comments')->nullable();
            $table->text('customer_complaints')->nullable();
            $table->json('delivery_photos')->nullable();
            $table->json('feedback_photos')->nullable()->comment('Photos uploaded by customer as feedback');
            $table->timestamp('feedback_submitted_at')->nullable();
            $table->string('feedback_status')->default('pending')->comment('pending, submitted, reviewed, resolved');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('batch_id');
            $table->index('delivery_date');
            $table->index('delivery_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
