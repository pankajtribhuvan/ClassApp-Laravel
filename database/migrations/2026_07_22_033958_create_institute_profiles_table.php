<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institute_profiles', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            $table->string('short_name')->nullable();

            $table->string('phone')->nullable();

            $table->string('email')->nullable();

            $table->string('website')->nullable();

            $table->string('gst_no')->nullable();

            $table->string('registration_no')->nullable();

            $table->text('address_line1')->nullable();

            $table->text('address_line2')->nullable();

            $table->string('city')->nullable();

            $table->string('state')->nullable();

            $table->string('pincode')->nullable();

            $table->text('receipt_footer')->nullable();

            $table->string('principal_name')->nullable();

            $table->string('logo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institute_profiles');
    }
};