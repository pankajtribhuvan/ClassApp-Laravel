<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@codingwale.com'],
            [
                'uuid'          => (string) Str::uuid(),
                'name'          => 'Super Admin',
                'password'      => Hash::make('Admin@123'),
                'role'          => 'admin',
                'status'        => 'active',
                'last_login_at' => null,
            ]
        );
    }
}