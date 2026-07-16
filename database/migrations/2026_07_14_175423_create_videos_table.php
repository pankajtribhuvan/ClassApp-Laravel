<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->string('course_name');

            $table->string('title');

            $table->string('original_video');

            $table->string('hls_url')->nullable();

            $table->integer('duration')->nullable();

            $table->string('status')->default('uploaded');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
