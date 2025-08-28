<?php

namespace App\Http\Controllers;

use App\Models\allergies;
use App\Models\medications;
use App\Models\Patient;
use App\Models\RecordMedical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class medical_visits extends Controller
{
//اضافة معاينة
    public function Addmedical_visit(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;

        $patient_id = Patient::find($id);
        if (!$patient_id) {
            return response()->json([
                'message' => ' the patient_id not found.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'main_complaint'         => 'required|string',
            'main_complaint_details' => 'required|string',
            'surgical_symptoms'      => 'required|string',
            'other_systems_review'   => 'required|string',
            'clinical_exam'          => 'required|string',
            'clinical_direction'     => 'required|string',
            'final_diagnosis'        => 'required|string',
            'treatment'              => 'required|string',
            'recommendations'        => 'required|string',
        ]);
        $medical_visit = \App\Models\medical_visits::create([
            'doctor_id'              => $doctor_id,
            'patient_id'             => $patient_id->id,
            'main_complaint'         => $data['main_complaint'],
            'main_complaint_details' => $data['main_complaint_details'],
            'surgical_symptoms'      => $data['surgical_symptoms'],
            'other_systems_review'   => $data['other_systems_review'],
            'clinical_exam'          => $data['clinical_exam'],
            'clinical_direction'     => $data['clinical_direction'],
            'final_diagnosis'        => $data['final_diagnosis'],
            'treatment'              => $data['treatment'],
            'recommendations'        => $data['recommendations'],
        ]);
        return response()->json([
            'message' => 'medical_visit  added successfully.',
            'data' => new \App\Http\Resources\medical_visits($medical_visit),
        ], 200);
    }
    //////////////////////////عرض  معاينات المريض
    public function showeMedical_visit(string $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'],
                401);
        }
        $doctor_id = $user->id;

        $patient= Patient::find($id);
        if (!$patient) {
            return response()->json([
                'message' => ' the patient_id not found.',
                'data' => [],
            ], 404);
        }
        $Medical_visits= $patient->MedicalVisits;


        $alloweMedical_visits = [];
        foreach ($Medical_visits as $Medical_visit) {
            if ($Medical_visit->doctor_id == $doctor_id) {
                $alloweMedical_visits[] = $Medical_visit;
            }
        }
        if (empty($alloweMedical_visits)) {
            return response()->json([
                'message' => 'These Medical_visit are private and can only be viewed by the doctor who added them.',
                'data' => null,
            ], 403);
        }

        return response()->json([
            'message' => 'Medical_visits retrieved successfully.',
            'data' => \App\Http\Resources\medical_visits::collection(collect($alloweMedical_visits)),
        ], 200);
    }


    //تعديل  معاينة//////////////////////////
    public function editmedical_visit(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;

        $medical_visit= \App\Models\medical_visits::where('id', $id)->first();
        if (!$medical_visit) {
            return response()->json([
                'message' => 'The medical_visit you want to edit does not exist.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'main_complaint'         => 'nullable|string',
            'main_complaint_details' => 'nullable|string',
            'surgical_symptoms'      => 'nullable|string',
            'other_systems_review'   => 'nullable|string',
            'clinical_exam'          => 'nullable|string',
            'clinical_direction'     => 'nullable|string',
            'final_diagnosis'        => 'nullable|string',
            'treatment'              => 'nullable|string',
            'recommendations'        => 'nullable|string',
        ]);
if ($medical_visit->doctor_id == $doctor_id) {
$medical_visit->update($data);

return response()->json([
'message' => 'medical_visit updated successfully.',
'data' => new \App\Http\Resources\medical_visits($medical_visit),
], 200);
}
return response()->json([
    'message' => 'These medical_visit  are private and can only be viewed by the doctor who added them.',
    'data' => new \App\Http\Resources\medical_visits($medical_visit),
], 403);
}


}
