<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {

         
            $table->date('next_due_date')
                  ->nullable()
                  ->after('payment_date');
        });

        Schema::table('students', function (Blueprint $table) {

            $table->date('next_due_date')
                  ->nullable()
                  ->after('balance_fees');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->dropColumn([
                'next_due_date',
            ]);
        });

        Schema::table('students', function (Blueprint $table) {

            $table->dropColumn([
                'next_due_date',
            ]);
        });
    }
};