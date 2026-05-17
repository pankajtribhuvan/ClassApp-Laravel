<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('mobile');
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('college_school')->nullable();
            $table->string('current_class')->nullable();
            $table->json('interested_courses'); // Stores the multi-dropdown array cleanly
            $table->string('referred_by')->nullable();
            $table->string('inquiry_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};