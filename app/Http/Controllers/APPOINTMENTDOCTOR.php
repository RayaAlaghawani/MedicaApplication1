<?php

namespace App\Http\Controllers;

use App\Http\Resources\appointment;
use App\Http\Resources\patientResource;
use App\Models\appointments;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class APPOINTMENTDOCTOR extends Controller
{
    //عرض حجوزات كلية للطبيب
    public function indexAppoitmentsList()
    {
        $doctor_id = Auth::user()->id;

        $AppoitmentsList =appointments::where('doctor_id',$doctor_id)->get();
        if($AppoitmentsList->isEmpty()){
            return response()->json([
                'message' => 'لا يوجد لديك حجوزات في التطبيق.',
                'data' => [],
            ], 404);

        }

        return response()->json([
            'message' => 'success.',
            'data' => appointment::collection($AppoitmentsList),
        ], 200);
    }
//البحث عن حجز
    public function searchforPatient(Request $request)
    {
        $validated = $request->validate([
            'appointment_date' => 'date',
            'status' => 'nullable|string|in:pending,confirmed,cancelled',
            'patient_name' => 'nullable|string',
        ]);
        $appointment_date = $request->appointment_date;
        $status = $request->status;
        $patient_name = $request->patient_name;
        $query = appointments::query();
        if ($appointment_date) {
            $query->where('appointment_date', $appointment_date);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($patient_name) {
            $query->whereHas('patient', function ($Q) use ($patient_name) {
                $Q->where('name','like' ,'%'.$patient_name.'%');

            });}

            $appointment = $query->get();
            if ($appointment->isEmpty()) {
                return response()->json([
                    'message' => 'لم يتم العثور على أي حجوزات بالمواصفات المطلوبة.',
                    'data' => [],
                ], 404);


            }
            return response()->json([
                'message' => 'تم جلب الحجوزات بنجاح.',
                'data' => appointment::collection($appointment),
            ], 200);
        }

    }

