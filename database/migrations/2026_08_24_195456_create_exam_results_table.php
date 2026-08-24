<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->uuid('teacher_uuid');

            $table->uuid('batch_uuid');

            $table->string('exam_name');

            $table->date('exam_date');

            $table->json('results');

            $table->timestamps();

            $table->index('teacher_uuid');
            $table->index('batch_uuid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};