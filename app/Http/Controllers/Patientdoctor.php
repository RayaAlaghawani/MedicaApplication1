<?php

namespace App\Http\Controllers;

use App\Http\Resources\patientResource;
use App\Models\doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Patientdoctor extends Controller
{
    //عرض المرضى الين لديهم حجوزات
public function showPatient(){
    $doctor_id=Auth::user()->id;
    $doctor=doctor::with('patientss')->find($doctor_id);
    if (!$doctor || $doctor->patientss->isEmpty()) {
        return response()->json([
            'message' => 'No patients have booked appointments with you',
            'data' => null,
        ], 404);
}
    $patients=$doctor->patientss;
    return response()->json([
        'message' => 'success',
        'data' => patientResource::collection($patients),
    ], 200);
}






    //بحث عن مريض
    public function searchPatient(Request $request)
    {
        $doctor_id = Auth::id();
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|string|in:Male,Female',
        ]);
        $name=$request->name;
        $age=$request->age;
        $gender=$request->gender;

        $query = Patient::whereHas('doctor_appointments', function($q) use ($doctor_id) {
            $q->where('doctor_id', $doctor_id);
        });

if($name){
    $query->where('name','like','%'.$name.'%')  ;
}
        if($age){
            $query->where('age',$age);
        }
        if($gender){
            $query->where('gender',$gender);
        }
$result=$query->get();
        if($result->isEmpty()){
            return response()->json([
                'message' => 'No patients were found matching the given criteria.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Patients retrieved successfully.',
            'data' => patientResource::collection($result),
        ], 200);







    }
}
