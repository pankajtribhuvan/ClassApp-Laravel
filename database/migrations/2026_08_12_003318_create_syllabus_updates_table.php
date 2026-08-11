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
        Schema::create('syllabus_updates', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->uuid('batch_uuid')->index();

            $table->unsignedInteger('step_no');

            $table->string('title');

            $table->text('description')->nullable();

            $table->json('topics')->nullable();

            $table->enum('status', [
                'planned',
                'in_progress',
                'completed',
            ])->default('planned');

            $table->timestamp('completed_at')->nullable();

            $table->string('created_by_type')->nullable();

            $table->uuid('created_by_uuid')->nullable();

            $table->timestamps();

            $table->index([
                'batch_uuid',
                'step_no',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syllabus_updates');
    }
};
