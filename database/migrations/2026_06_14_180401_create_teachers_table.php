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
        Schema::create('teachers', function (Blueprint $table) {

            $table->id();

            $table->uuid('uuid')->unique();

            $table->string('full_name');

            $table->string('mobile');

            $table->string('whatsapp')
                ->nullable();

            $table->string('email')
                ->nullable();

            $table->string('qualification')
                ->nullable();

            $table->string('specialization')
                ->nullable();

            $table->date('joining_date')
                ->nullable();

            $table->decimal(
                'salary',
                10,
                2
            )->default(0);

            $table->string('photo')
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->enum(
                'status',
                [
                    'active',
                    'inactive'
                ]
            )->default('active');

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
