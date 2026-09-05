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
        Schema::create('research_requests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id');
            $t->string('subject');
            $t->json('filters')->nullable();
            $t->text('user_prompt');
            $t->string('status')->default('pending');
            $t->string('model_used')->nullable();
            $t->json('result')->nullable();
            $t->string('generated_file_path')->nullable();
            $t->text('error')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_requests');
    }
};
