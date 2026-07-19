<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE students
            MODIFY COLUMN status
            ENUM(
                'active',
                'completed',
                'archived',
                'cancelled'
            )
            NOT NULL DEFAULT 'active'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE students
            MODIFY COLUMN status
            ENUM(
                'active',
                'inactive',
                'completed'
            )
            NOT NULL DEFAULT 'active'
        ");
    }
};