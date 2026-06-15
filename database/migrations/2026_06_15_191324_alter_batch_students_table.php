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
    Schema::table('batch_students', function (Blueprint $table) {

        $table->uuid('batch_uuid')
              ->after('id');

        $table->uuid('student_uuid')
              ->after('batch_uuid');
    });
}

public function down(): void
{
    Schema::table('batch_students', function (Blueprint $table) {

        $table->dropColumn([
            'batch_uuid',
            'student_uuid'
        ]);
    });
}
};
