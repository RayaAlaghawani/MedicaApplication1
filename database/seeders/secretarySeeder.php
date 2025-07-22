<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class secretarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('secretaries')->insert([
            [
                'name' => 'Amina Saleh',
                'email' => 'amina@example.com',
                'phone' => '07701234567',
                'address' => 'Baghdad, Iraq',
                'date_of_brith' => '1995-06-01',
                'image' => 'doctor_images/BdLKq10i6ZKLfZNXjrcJcQw7jhxPDymoQpXyxlT2.jpg',
                'cv' => 'certificates/YU6dedisiCUd9PHVXgr494xuIChkeWcTbfo5KvtX.jpg',
                'doctor_id' => 1, // تأكد أن هذا الطبيب موجود في جدول doctors
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sara Khalid',
                'email' => 'sara@example.com',
                'phone' => '07702345678',
                'address' => 'Basra, Iraq',
                'date_of_brith' => '1993-08-15',
                'image' => 'doctor_images/BdLKq10i6ZKLfZNXjrcJcQw7jhxPDymoQpXyxlT2.jpg',
                'cv' => 'cv_files/3fzjDpdarDDfW75M7LLnaEWqpXqk6fkRMXiFJU2e.pdf',
                'doctor_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
