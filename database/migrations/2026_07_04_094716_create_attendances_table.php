<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {

            $table->id();

            $table->uuid('uuid')
                ->unique();

            $table->uuid('batch_uuid');

            $table->date('attendance_date');

            $table->json(
                'present_student_uuids'
            );

            $table->enum(
                'marked_by_type',
                [
                    'admin',
                    'teacher',
                ]
            );

            $table->uuid(
                'marked_by_uuid'
            )->nullable();

            $table->timestamps();

            // Only one attendance record
            // per batch per date.

            $table->unique([
                'batch_uuid',
                'attendance_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'attendances'
        );
    }
};