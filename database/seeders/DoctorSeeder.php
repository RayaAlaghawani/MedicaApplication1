<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'specialization_id' => 1,
                'first_name' => 'Ahmed',
                'last_name' => 'Ali',
                'email' => 'ahmed@example.com',
                'email_verified_at' => Carbon::now(),
                'image' => 'doctor_images/FV5NRmD1ihbqXabiJysXdLK5SLhMQ4VOWNwGk3yJ.jpg',
                'device_token' => 'token1',
                'DateOfBirth' => '1980-05-01',
                'phone' => '0100000001',
                'password' => Hash::make('password123'),
                'CurriculumVitae' => 'cv_files/3fzjDpdarDDfW75M7LLnaEWqpXqk6fkRMXiFJU2e.pdf',
                'Nationality' => 'Egyptian',
                'ClinicAddress' => 'Cairo Street 1',
                'ProfessionalAssociationPhoto' => 'association_photos/2BCWRrDv5HQWhy5EInKHSkaF7yYhsafCRfRSQQmy.jpg',
                'CertificateCopy' => 'certificates/0gSLt1mWyD9iVYkk0l60hfu0c7wjGCHeCcJWj9LK.jpg',
                'consultation_fee' => 200.00,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'specialization_id' => 2,
                'first_name' => 'Sara',
                'last_name' => 'Hassan',
                'email' => 'sara@example.com',
                'email_verified_at' => Carbon::now(),
                'image' => 'doctor_images/3swEaUeRpbav1HTC0aiCdevX10O85s9o0WDSbknT.jpg',
                'device_token' => 'token2',
                'DateOfBirth' => '1990-03-15',
                'phone' => '0100000002',
                'password' => Hash::make('sara2024'),
                'CurriculumVitae' => 'cv_files/3fzjDpdarDDfW75M7LLnaEWqpXqk6fkRMXiFJU2e.pdf',
                'Nationality' => 'Jordanian',
                'ClinicAddress' => 'Amman Clinic',
                'ProfessionalAssociationPhoto' => 'association_photos/2BCWRrDv5HQWhy5EInKHSkaF7yYhsafCRfRSQQmy.jpg',
                'CertificateCopy' => 'certificates/0gSLt1mWyD9iVYkk0l60hfu0c7wjGCHeCcJWj9LK.jpg',
                'consultation_fee' => 300.00,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('doctors')->insert($doctors);
    }
}

