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
     Schema::create('courses', function (Blueprint $table) {

    $table->id();

    $table->uuid('uuid')->unique();

    $table->string('course_name');

    $table->string('duration');

    $table->decimal('fees',10,2);

    $table->integer('installments');

    $table->enum('status',['active','inactive'])
          ->default('active');

    $table->timestamps();
});
    }

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
