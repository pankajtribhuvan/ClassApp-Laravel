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
        Schema::table('students', function (Blueprint $table) {

            $table->uuid('course_uuid')->nullable();

            $table->string('course_name')
                ->nullable();

            $table->decimal(
                'total_fees',
                10,
                2
            )->default(0);

            $table->decimal(
                'paid_fees',
                10,
                2
            )->default(0);

            $table->decimal(
                'balance_fees',
                10,
                2
            )->default(0);

            $table->integer(
                'installments'
            )->default(1);

            $table->string(
                'admission_no'
            )->nullable();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            //
        });
    }
};
