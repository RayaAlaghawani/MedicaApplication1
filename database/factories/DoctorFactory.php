<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Specialization;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        // محاولة الحصول على specialization_id موجود مسبقًا
        $specializationId = Specialization::query()->inRandomOrder()->value('id');

        if (!$specializationId) {
            throw new \RuntimeException(
                'لا توجد تخصصات في جدول specializations. يرجى إضافة بيانات للتخصصات أولاً.'
            );
        }

        return [
            'specialization_id' => $specializationId,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'image' => null,
            'device_token' => null,
            'DateOfBirth' => $this->faker->date(),
            'phone' => $this->faker->phoneNumber(),
            'password' => bcrypt('password'),
            'CurriculumVitae' => null,
            'Nationality' => $this->faker->country(),
            'ClinicAddress' => $this->faker->address(),
            'ProfessionalAssociationPhoto' => null,
            'CertificateCopy' => null,
            'consultation_fee' => $this->faker->randomFloat(2, 50, 500),
            'remember_token' => null,
        ];
    }
}
