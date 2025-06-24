<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::truncate();
        Admin::query()->create([
            'admin_name' => 'SuperAdmin',
            'email' => 'nour123481122@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'super_admin',
        ]);

    }
}
