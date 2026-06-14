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
        Schema::create('payments', function (Blueprint $table) {

        $table->id();

        $table->uuid('uuid')->unique();

        $table->string('student_uuid');

        $table->string('admission_no');

        $table->string('receipt_no');

        $table->decimal(
            'amount',
            10,
            2
        );

        $table->string(
            'payment_mode'
        )->default('Cash');

        $table->text(
            'remarks'
        )->nullable();

        $table->date(
            'payment_date'
        );

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
