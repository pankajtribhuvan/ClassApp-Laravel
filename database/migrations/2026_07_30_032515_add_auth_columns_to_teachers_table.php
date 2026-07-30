<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {

            // $table->string('username')->unique()->after('email');

            $table->string('password')->nullable()->after('email');

            $table->timestamp('last_login_at')->nullable()->after('status');

            $table->rememberToken();

        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {

            $table->dropColumn([
                // 'username',
                'password',
                'last_login_at',
                'remember_token',
            ]);

        });
    }
};