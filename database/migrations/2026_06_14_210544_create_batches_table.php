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
        Schema::create('batches', function (Blueprint $table) {

            $table->id();

            $table->uuid('uuid')->unique();

            $table->string('batch_name');

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->string('course_name');

            $table->time('start_time');

            $table->time('end_time');

            $table->string('days');

            $table->string('room_no')
                ->nullable();

            $table->enum(
                'status',
                ['active', 'inactive']
            )->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
