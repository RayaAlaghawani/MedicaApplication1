<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        $this->call([UserSeeder::class,AdminnSeeder::class,SpecializationSeeder::class,
            join_requestsSedder::class,
            doctorPendingSeeder::class,
            doctorSeeder::class,
            patientSeeder::class,
            doctor_schedules::class,
            secretarySeeder::class,
            appointmentSeeder::class,
        ]);

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
