<?php

namespace App\Http\Controllers;

use App\Models\favourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function addToFavourite($doctor_id)
    {
        $patient = Auth::guard('api-patient')->user();

        $exists = Favourite::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor_id)
            ->first();

        if ($exists) {
            return response()->json([
                'message' => 'الطبيب موجود بالفعل في المفضلة.',
                'doctor_id' => $exists->doctor_id,
                'patient_id' => $exists->patient_id,
                'is_favorite' => $exists->is_favorite,
                'doctor' => $exists->doctor, // تأكد أن العلاقة معرفة في موديل Favourite
            ], 200);
        }

        $favourite = Favourite::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor_id,
            'is_favorite' => true
        ]);

        // تحميل بيانات الطبيب من العلاقة
        $favourite->load('doctor');

        return response()->json([
            'message' => 'تمت إضافة الطبيب إلى المفضلة.',
            'doctor_id' => $favourite->doctor_id,
            'patient_id' => $favourite->patient_id,
            'is_favorite' => $favourite->is_favorite,
            'doctor' => $favourite->doctor
        ], 201);
    }



//    public function removeFromFavourite($doctor_id)
//    {
//        $patient = Auth::guard('api-patient')->user();
//
//        $favourite = Favourite::where('patient_id', $patient->id)
//            ->where('doctor_id', $doctor_id)
//            ->first();
//
//        if (!$favourite) {
//            return response()->json(['message' => 'الطبيب غير موجود في المفضلة.'], 404);
//        }
//
//        $favourite->delete();
//
//        return response()->json(['message' => 'تمت إزالة الطبيب من المفضلة.'], 200);
//    }


    public function removeFromFavourite($doctor_id)
    {
        $patient = Auth::guard('api-patient')->user();

        $favourite = Favourite::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor_id)
            ->with('doctor') // تحميل علاقة الطبيب
            ->first();

        if (!$favourite) {
            return response()->json(['message' => 'الطبيب غير موجود في المفضلة.'], 404);
        }

        $doctorInfo = $favourite->doctor;
        $favourite->delete();

        return response()->json([
            'message' => 'تمت إزالة الطبيب من المفضلة.',
            'doctor_id' => $doctor_id,
            'patient_id' => $patient->id,
            'is_favorite' => false,
            'doctor' => $doctorInfo
        ], 200);
    }


    public function getFavourite()
    {
        $patient = Auth::guard('api-patient')->user();

        $favourites = Favourite::with('doctor')
            ->where('patient_id', $patient->id)
            ->get();

        return response()->json([
            'message' => 'قائمة المفضلة',
            'favourites' => $favourites
        ]);
    }




}
