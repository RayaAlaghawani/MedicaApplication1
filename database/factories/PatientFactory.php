<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PatientFactory extends Factory
{
    protected $model = \App\Models\Patient::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'), // كلمة مرور افتراضية مشفرة
            'is_banned' => $this->faker->boolean(10), // 10% احتمال أن يكون محظور
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Other']),
            'email_verified' => $this->faker->boolean(80), // 80% احتمال أن يكون البريد موثق
            'email_verification_code' => $this->faker->regexify('[A-Z0-9]{6}'),
         //   'profile_image' => null, // يمكن إضافة رابط صورة عشوائية إذا أحببت
            'age' => $this->faker->numberBetween(1, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
