<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class patientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Patient::factory()->count(10)->create();

        DB::table('patients')->insert([
            [
                'name' => 'Mohammed Al-Mutairi',
                'email' => 'mohammed@example.com',
                'password' => Hash::make('password123'),
                'gender' => 'Male',
                'email_verified' => true,
                'email_verification_code' => null,
                'profile_image' => null,
                'age' => 28,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Khalid Al-Qahtani',
                'email' => 'khalid@example.com',
                'password' => Hash::make('secret456'),
                'gender' => 'Male',
                'email_verified' => false,
                'email_verification_code' => 'ABC123',
                'profile_image' => 'khalid.png',
                'age' => 35,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Faisal Al-Dosari',
                'email' => 'faisal@example.com',
                'password' => Hash::make('faisal789'),
                'gender' => 'Male',
                'email_verified' => true,
                'email_verification_code' => null,
                'profile_image' => 'faisal.jpg',
                'age' => 42,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
