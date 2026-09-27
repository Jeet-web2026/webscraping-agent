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
        Schema::create('productdetails_responses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('research_request_id')
                ->constrained('research_requests')
                ->cascadeOnDelete();

            $table->json('recent_photo')->nullable();
            $table->longText('video_link')->nullable();
            $table->longText('product_rate')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->longText('seller')->nullable();
            $table->longText('seller_address')->nullable();
            $table->longText('seller_contact_details')->nullable();
            $table->longText('website')->nullable();
            $table->longText('availability')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productdetails_responses');
    }
};
