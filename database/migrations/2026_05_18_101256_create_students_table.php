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
        Schema::create('students', function (Blueprint $table) {

            // UUID PRIMARY KEY
            $table->uuid('uuid')->primary();

            /*
            |--------------------------------------------------------------------------
            | INQUIRY DATA (COPIED)
            |--------------------------------------------------------------------------
            */

            $table->string('full_name');

            $table->string('mobile');

            $table->string('whatsapp')
                ->nullable();

            $table->string('email')
                ->nullable();

            $table->string('college_school')
                ->nullable();

            $table->string('current_class')
                ->nullable();

            $table->json('interested_courses')
                ->nullable();

            $table->string('referred_by')
                ->nullable();

            $table->string('inquiry_date')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | EXTRA STUDENT DETAILS
            |--------------------------------------------------------------------------
            */

            $table->string('parent_phone')
                ->nullable();

            $table->string('father_occupation')
                ->nullable();

            $table->string('student_photo')
                ->nullable();

            $table->string('aadhar_photo')
                ->nullable();

            $table->date('admission_date');

            $table->enum('status', [
                'active',
                'inactive',
                'completed'
            ])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
