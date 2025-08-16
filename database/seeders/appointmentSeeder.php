<?php

namespace Database\Seeders;

use App\Models\appointments;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class appointmentSeeder extends Seeder
{
    public function run()
    {
        $appointments = [];

        $doctors = [1, 2];
        $patients = [1, 2];
        $reason_types = ['First_Visit', 'Follow_up'];

        $currentYear = now()->year;

        // 1. حجوزات لكل أشهر السنة الحالية
        for ($month = 1; $month <= 12; $month++) {
            $day = rand(1, Carbon::create($currentYear, $month, 1)->daysInMonth);
            $appointments[] = [
                'doctor_id' => $doctors[array_rand($doctors)],
                'patient_id' => $patients[array_rand($patients)],
                'appointment_date' => Carbon::create($currentYear, $month, $day)->format('Y-m-d'),
                'appointment_time' => sprintf("%02d:00:00", rand(9, 17)),
                'status' => 'confirmed',
                'reason_reservation' => $reason_types[array_rand($reason_types)],
                'created_at' => Carbon::create($currentYear, $month, $day, rand(8, 20)),
                'updated_at' => Carbon::now(),
            ];
        }

        // 2. حجوزات لكل أيام الأسبوع الحالي
        $startOfWeek = Carbon::now()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $appointments[] = [
                'doctor_id' => $doctors[array_rand($doctors)],
                'patient_id' => $patients[array_rand($patients)],
                'appointment_date' => $day->format('Y-m-d'),
                'appointment_time' => sprintf("%02d:00:00", rand(9, 17)),
                'status' => 'confirmed',
                'reason_reservation' => $reason_types[array_rand($reason_types)],
                'created_at' => $day->copy()->setHour(rand(8, 20)),
                'updated_at' => Carbon::now(),
            ];
        }

        // 3. حجوزات للسنة السابقة، الحالية، واللاحقة
        for ($year = $currentYear - 1; $year <= $currentYear + 1; $year++) {
            for ($i = 0; $i < 5; $i++) { // 5 حجوزات لكل سنة
                $month = rand(1, 12);
                $day = rand(1, Carbon::create($year, $month, 1)->daysInMonth);
                $appointments[] = [
                    'doctor_id' => $doctors[array_rand($doctors)],
                    'patient_id' => $patients[array_rand($patients)],
                    'appointment_date' => Carbon::create($year, $month, $day)->format('Y-m-d'),
                    'appointment_time' => sprintf("%02d:00:00", rand(9, 17)),
                    'status' => 'confirmed',
                    'reason_reservation' => $reason_types[array_rand($reason_types)],
                    'created_at' => Carbon::create($year, $month, $day, rand(8, 20)),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        appointments::insert($appointments);
    }
}
