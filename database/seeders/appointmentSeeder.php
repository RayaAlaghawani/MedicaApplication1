<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class appointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('appointments')->insert([
            [
                'id' =>'1',
                'doctor_id'=>'1',
                'patient_id' =>'1',
                'appointment_date' =>'2025-07-29',
                'appointment_time' => '14:00',
                'status' => 'pending',
                'reason_reservation'=>'Follow_up'
            ],
            [
                'id' =>'2',
                'doctor_id'=>'2',
                'patient_id' =>'2',
                'appointment_date' =>'2025-07-31',
                'appointment_time' => '13:00',
                'status' => 'confirmed',
                'reason_reservation'=>'First_Visit'
            ],
            [
                'id' =>'3',
                'doctor_id'=>'1',
                'patient_id' =>'3',
                'appointment_date' =>'2025-07-30',
                'appointment_time' => '12:00',
                'status' => 'cancelled',
                'reason_reservation'=>'First_Visit'
            ]        ]);
    }


}
