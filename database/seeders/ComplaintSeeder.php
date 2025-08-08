<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Complaint;
use App\Models\Doctor;
use App\Models\Secretary;
use App\Models\Patient;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Complaint::create([
            'complaintable_type' => 'doctor',
            'complaintable_id' => 1,
            'subject' => 'Delay in updating patient records',
            'message' => 'I am experiencing a delay in syncing patient data with the system.',
            'status' => 'Pending',
            'admin_response' => null,
        ]);

        Complaint::create([
            'complaintable_type' => 'secretary',
            'complaintable_id' => 1,
            'subject' => 'Issue with appointment scheduling',
            'message' => 'I am unable to modify some patient appointments after saving them.',
            'status' => 'Pending',
            'admin_response' => null,
        ]);
        Complaint::create([
            'complaintable_type' => 'Patient',
            'complaintable_id' => 1,
            'subject' => 'Error in medical invoice',
            'message' => 'A service that I did not use was charged in my bill.',
            'status' => 'Pending',
            'admin_response' => null,
        ]);
    }
}
