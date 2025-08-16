<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ComplaintController extends Controller
{



    public function addComplaint(Request $request)
        /** @var \App\Models\Patient $patient */
    {
        $patient = Auth::guard('api-patient')->user();

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $complaint = Complaint::create([
            'complaintable_type'=>'Patient',
            'complaintable_id'=>$patient->id,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

//        return response()->json([
//            'message' => 'تم إرسال الشكوى بنجاح.',
//            'complaint' => $complaint
//        ], 201);
        return response()->json([
            'message' => 'تم إرسال الشكوى بنجاح.',
            'complaint' => [
                'id' => $complaint->id,
                'complaintable_id' => $patient->id,
                'subject' => $complaint->subject,
                'message' => $complaint->message,
                'patient_name' => $patient->name,  // نصل للاسم من الكائن Auth
                'created_at' => $complaint->created_at,
            ]
        ], 201);
    }

    public function getComplaint()
    {

        /** @var \App\Models\Patient $patient */
        $patient = Auth::guard('api-patient')->user();

        if (!$patient) {
            return response()->json(['message' => 'المريض غير مسجل الدخول'], 401);
        }

        $complaints = $patient->complaintpatient()->latest()->get();
        return response()->json([
            'message' =>'success',
            'data'=>$complaints,
        ], 200);
    }


////////Complaint







}
