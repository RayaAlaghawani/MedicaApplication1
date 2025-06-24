<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class doctorPendingSeeder extends Seeder
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
                'image' => 'doctor_images/BdLKq10i6ZKLfZNXjrcJcQw7jhxPDymoQpXyxlT2.jpg',
                'device_token' => 'token1',
                'DateOfBirth' => '1980-05-01',
                'phone' => '0100000001',
                'password' => Hash::make('password123'),
                'CurriculumVitae' => 'Ahmed CV',
                'Nationality' => 'Egyptian',
                'ClinicAddress' => 'Cairo Street 1',
                'ProfessionalAssociationPhoto' => 'association_photos/VuB2CTqvhkNAjILKM93dz3MkMF79M4RetRd0Oghy.jpg',
                'CertificateCopy' => 'certificates/YU6dedisiCUd9PHVXgr494xuIChkeWcTbfo5KvtX.jpg',
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
                'image' => 'doctor_images/BdLKq10i6ZKLfZNXjrcJcQw7jhxPDymoQpXyxlT2.jpg',
                'device_token' => 'token2',
                'DateOfBirth' => '1990-03-15',
                'phone' => '0100000002',
                'password' => Hash::make('sara2024'),
                'CurriculumVitae' => 'Sara CV',
                'Nationality' => 'Jordanian',
                'ClinicAddress' => 'Amman Clinic',
                'ProfessionalAssociationPhoto' => 'association_photos/8B0BZRBppMPq3eL4UynK4IYnenrWo64wdjSvJLic.jpg',
                'CertificateCopy' => 'certificates/YU6dedisiCUd9PHVXgr494xuIChkeWcTbfo5KvtX.jpg',
                'consultation_fee' => 300.00,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('doctor_pendings')->insert($doctors);
    }
}
