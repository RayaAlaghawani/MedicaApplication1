<?php

namespace App\Http\Controllers;

use App\Models\doctor;
use App\Models\favourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function addToFavourite($doctor_id)
    {
        $patient = Auth::guard('api-patient')->user();

        // تحقق من وجود الطبيب
        $doctor = Doctor::with('specialization')->find($doctor_id);
        if (!$doctor) {
            return response()->json([
                'message' => 'الطبيب غير موجود.'
            ], 404);
        }

        // تحقق من وجود المفضلة سابقاً
        $exists = Favourite::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor_id)
            ->first();

        if ($exists) {
            return response()->json([
                'message' => 'الطبيب موجود بالفعل في المفضلة.',
                'doctor_id' => $exists->doctor_id,
                'patient_id' => $exists->patient_id,
                'is_favorite' => $exists->is_favorite,
                'doctor' => [
                    'id' => $doctor->id,
                    'first_name' => $doctor->first_name,
                    'last_name' => $doctor->last_name,
                    'email' => $doctor->email,
                    'phone' => $doctor->phone,
                    'DateOfBirth' => $doctor->DateOfBirth,
                    'Nationality' => $doctor->Nationality,
                    'ClinicAddress' => $doctor->ClinicAddress,
                    'imageUrl' => $doctor->image ? asset('storage/' . $doctor->image) : null,
                    'certificateCopyUrl' => $doctor->CertificateCopy ? asset('storage/' . $doctor->CertificateCopy) : null,
                    'curriculumVitae' => $doctor->CurriculumVitae,
                    'professionalAssociationPhotoUrl' => $doctor->ProfessionalAssociationPhoto ? asset('storage/' . $doctor->ProfessionalAssociationPhoto) : null,
                    'consultation_fee' => $doctor->consultation_fee,
                    'specialization_name' => $doctor->specialization->name ?? null,
                    'created_at' => $doctor->created_at,
                    'updated_at' => $doctor->updated_at,
                ],
            ], 200);
        }

        // إضافة للمفضلة
        $favourite = Favourite::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor_id,
            'is_favorite' => true
        ]);

        return response()->json([
            'message' => 'تمت إضافة الطبيب إلى المفضلة.',
            'doctor_id' => $favourite->doctor_id,
            'patient_id' => $favourite->patient_id,
            'is_favorite' => $favourite->is_favorite,
            'doctor' => [
                'id' => $doctor->id,
                'first_name' => $doctor->first_name,
                'last_name' => $doctor->last_name,
                'email' => $doctor->email,
                'phone' => $doctor->phone,
                'DateOfBirth' => $doctor->DateOfBirth,
                'Nationality' => $doctor->Nationality,
                'ClinicAddress' => $doctor->ClinicAddress,
                'imageUrl' => $doctor->image ? asset('storage/' . $doctor->image) : null,
                'certificateCopyUrl' => $doctor->CertificateCopy ? asset('storage/' . $doctor->CertificateCopy) : null,
                'curriculumVitae' => $doctor->CurriculumVitae,
                'professionalAssociationPhotoUrl' => $doctor->ProfessionalAssociationPhoto ? asset('storage/' . $doctor->ProfessionalAssociationPhoto) : null,
                'consultation_fee' => $doctor->consultation_fee,
                'specialization_name' => $doctor->specialization->name ?? null,
                'created_at' => $doctor->created_at,
                'updated_at' => $doctor->updated_at,
            ],
        ], 201);
    }




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
        try {
            $patient = Auth::guard('api-patient')->user();

            $favourites = Favourite::with('doctor')
                ->where('patient_id', $patient->id)
                ->get();

            if ($favourites->isEmpty()) {
                return response()->json([
                    'message' => 'لا يوجد أطباء في قائمة المفضلة.',
                    'favourites' => []
                ], 404);
            }

            $data = [];
            foreach ($favourites as $fav) {
                $doctor = $fav->doctor;
                if ($doctor) {
                    $specialization = \App\Models\Specialization::find($doctor->specialization_id);

                    $data[] = [
                        'id' => $doctor->id,
                        'specializationName' => $specialization ? $specialization->name : null,
                        'firstName' => $doctor->first_name,
                        'lastName' => $doctor->last_name,
                        'email' => $doctor->email,
                        'phone' => $doctor->phone,
                        'specializationId' => $doctor->specialization_id,
                        'DateOfBirth' => $doctor->DateOfBirth,
                        'nationality' => $doctor->Nationality,
                        'clinicAddress' => $doctor->ClinicAddress,
                        'consultationFee' => floatval($doctor->consultation_fee),
                        'imageUrl' => $doctor->image ? asset('storage/' . $doctor->image) : null,
                        'certificateCopyUrl' => $doctor->CertificateCopy ? asset('storage/' . $doctor->CertificateCopy) : null,
                        'curriculumVitae' => $doctor->CurriculumVitae,
                        'professionalAssociationPhotoUrl' => $doctor->ProfessionalAssociationPhoto ? asset('storage/' . $doctor->ProfessionalAssociationPhoto) : null,
                    ];
                }
            }

            return response()->json([
                'message' => 'تم جلب قائمة المفضلة بنجاح.',
                'favourites' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب قائمة المفضلة.',
                'error' => $e->getMessage()
            ], 500);
        }
    }





}
