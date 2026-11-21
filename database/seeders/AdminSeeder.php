<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@mybantu.id'],
            [
                'name'         => 'Super Administrator',
                'password'     => Hash::make('admin123'),
                'phone_number' => '081234567890',
                'role'         => 'Super Admin',
                'status'       => 'AKTIF',
            ]
        );
    }
}

