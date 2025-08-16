<?php

namespace Database\Factories;

use App\Http\Resources\appointment;
use App\Models\appointments;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = appointments::class;

    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(), // ينشئ طبيب جديد إذا لم يوجد
            'patient_id' => Patient::factory(), // ينشئ مريض جديد إذا لم يوجد
            'appointment_date' => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'appointment_time' => $this->faker->time('H:i:s'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'reason_reservation' => $this->faker->randomElement(['First_Visit', 'Follow_up']),
        ];
    }
}
