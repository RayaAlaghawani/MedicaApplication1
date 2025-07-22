<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class doctor_schedules extends Seeder
{
    public function run(): void
    {
        // الطبيب 1: الأحد، الثلاثاء، الخميس
        DB::table('doctor_schedules')->insert([
            [
                'doctor_id' => 1,
                'day_of_week' => 0, // الأحد
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'slot_duration' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => 1,
                'day_of_week' => 2, // الثلاثاء
                'start_time' => '10:00:00',
                'end_time' => '13:00:00',
                'slot_duration' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => 1,
                'day_of_week' => 4, // الخميس
                'start_time' => '11:00:00',
                'end_time' => '14:00:00',
                'slot_duration' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // الطبيب 2: الإثنين، الأربعاء، الجمعة
        DB::table('doctor_schedules')->insert([
            [
                'doctor_id' => 2,
                'day_of_week' => 1, // الإثنين
                'start_time' => '08:00:00',
                'end_time' => '11:00:00',
                'slot_duration' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => 2,
                'day_of_week' => 3, // الأربعاء
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'slot_duration' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => 2,
                'day_of_week' => 5, // الجمعة
                'start_time' => '10:00:00',
                'end_time' => '13:00:00',
                'slot_duration' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
