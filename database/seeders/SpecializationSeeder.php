<?php

namespace Database\Seeders;

use App\Models\specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $specializations = [
            'General Medicine',
            'Family Medicine',
            'Pediatrics',
            'Internal Medicine',
            'General Surgery',
            'Orthopedic Surgery',
            'Neurosurgery',
            'Cardiac Surgery',
            'Plastic Surgery',
            'Ophthalmology',
            'Otolaryngology (ENT)',
            'Dentistry',
            'Dermatology',
            'Obstetrics and Gynecology',
            'Urology',
            'Psychiatry',
            'Emergency Medicine',
            'Radiology',
            'Anesthesiology',
            'Cardiology',
            'Gastroenterology',
            'Hematology',
            'Nephrology',
            'Pulmonology',
            'Endocrinology',
            'Infectious Diseases',
            'Forensic Medicine',
            'Physical Therapy',
            'Nuclear Medicine',
            'Preventive Medicine',
        ];

        foreach ($specializations as $name) {
            specialization::create([
                'name' => $name,
            ]);
        }
    }
}
