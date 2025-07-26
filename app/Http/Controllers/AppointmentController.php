<?php

namespace App\Http\Controllers;


use App\Models\doctor_schedules;
use App\Models\appointments;
use Carbon\Carbon;
use App\Models\doctor;



use App\Http\Controllers\Controller;

use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function getAllSpecializations()
    {
        try {
            $specializations = Specialization::all();

            return response()->json([
                'message' => 'تم جلب جميع التخصصات.',
                'specializations' => $specializations
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب التخصصات.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /////عرض الاطباء داخل تخصص
//    public function getDoctorsBySpecialization($specialization_id)
//    {
//
//        $specializationExists = Specialization::where('id', $specialization_id)->exists();
//        if (!$specializationExists) {
//            return response()->json([
//                'message' => 'التخصص غير موجود.',
//                'doctors' => []
//            ], 404);
//        }
//
//        $doctors = \App\Models\doctor::where('specialization_id', $specialization_id)->get();
//
//        if ($doctors->isEmpty()) {
//            return response()->json([
//                'message' => 'لا يوجد أطباء لهذا التخصص.',
//                'doctors' => []
//            ], 404);
//        }
//
//        $data = $doctors->map(function($doctor) {
//            return [
//                'id' => $doctor->id,
//                'status' => $doctor->status,
//                'firstName' => $doctor->first_name,
//                'lastName' => $doctor->last_name,
//                'email' => $doctor->email,
//                'phone' => $doctor->phone,
//                'specializationId' => $doctor->specialization_id,
//                'dateOfBirth' => optional($doctor->DateOfBirth)->format('Y-m-d'),
//                'nationality' => $doctor->Nationality,
//                'clinicAddress' => $doctor->ClinicAddress,
//                'consultationFee' => (float) $doctor->consultation_fee,
//                'emailVerifiedAt' => $doctor->email_verified_at ? $doctor->email_verified_at->format('Y-m-d H:i:s') : null,
//                'imageUrl' => $doctor->image ? asset('storage/' . $doctor->image) : null,
//                'certificateCopyUrl' => $doctor->CertificateCopy ? asset('storage/' . $doctor->CertificateCopy) : null,
//                'curriculumVitae' => $doctor->CurriculumVitae,
//                'professionalAssociationPhotoUrl' => $doctor->ProfessionalAssociationPhoto ? asset('storage/' . $doctor->ProfessionalAssociationPhoto) : null,
//            ];
//        });
//
//        return response()->json([
//            'message' => 'تم جلب الأطباء بنجاح.',
//            'doctors' => $data
//        ]);
//    }

    public function getDoctorsBySpecialization($specialization_id)
    {
        $specialization = \App\Models\Specialization::find($specialization_id);
        if ($specialization) {
            $doctors = \App\Models\Doctor::where('specialization_id', $specialization_id)->get();

            $data = [];
            foreach ($doctors as $doctor) {
                $data[] = [
                    'id' => $doctor->id,
                    'status' => $doctor->status,
                    'specializationName' => $specialization->name,
                    'firstName' => $doctor->first_name,
                    'lastName' => $doctor->last_name,
                    'email' => $doctor->email,
                    'phone' => $doctor->phone,
                    'specializationId' => $doctor->specialization_id,
                    'dateOfBirth' => date('Y-m-d', strtotime($doctor->DateOfBirth)),
                    'nationality' => $doctor->Nationality,
                    'clinicAddress' => $doctor->ClinicAddress,
                    'consultationFee' => floatval($doctor->consultation_fee),
                    'emailVerifiedAt' => $doctor->email_verified_at ? date('Y-m-d H:i:s', strtotime($doctor->email_verified_at)) : null,
                    'imageUrl' => $doctor->image ? asset('storage/' . $doctor->image) : null,
                    'certificateCopyUrl' => $doctor->CertificateCopy ? asset('storage/' . $doctor->CertificateCopy) : null,
                    'curriculumVitae' => $doctor->CurriculumVitae,
                    'professionalAssociationPhotoUrl' => $doctor->ProfessionalAssociationPhoto ? asset('storage/' . $doctor->ProfessionalAssociationPhoto) : null,
                ];
            }

            return response()->json([
                'message' => 'تم جلب الأطباء ضمن هذا الاختصاص بنجاح.',
                'data' => $data,
            ], 200);
        }

        return response()->json([
            'message' => 'لا يوجد أطباء لهذا التخصص.',
            'doctors' => []
        ], 404);
    }


///doctor infoooooooooooooooooooooooooooo

    public function getDoctorById($id)
    {
        try {
            $doctor = \App\Models\doctor::with('specialization')->find($id);

            if (!$doctor) {
                return response()->json([
                    'message' => 'لم يتم العثور على الطبيب المطلوب.',
                ], 404);
            }

            return response()->json([
                'message' => 'تم جلب بيانات الطبيب بنجاح.',
                'doctor' => [
                    'id' => $doctor->id,
                    'first_name' => $doctor->first_name,
                    'last_name' => $doctor->last_name,
                    'email' => $doctor->email,
                    'phone' => $doctor->phone,
                    'DateOfBirth' => $doctor->DateOfBirth,
                    'Nationality' => $doctor->Nationality,
                    'image_url' => $doctor->image ? asset('storage/' .$doctor->image) : null,
                    'ClinicAddress' => $doctor->ClinicAddress,
                    'CurriculumVitae' => $doctor->CurriculumVitae,
                    'ProfessionalAssociationPhoto' => $doctor->ProfessionalAssociationPhoto,
                    'CertificateCopy' => $doctor->CertificateCopy,
                    'consultation_fee' => $doctor->consultation_fee,
                    'specialization_name' => $doctor->specialization->name ,
                    'created_at' => $doctor->created_at,
                    'updated_at' => $doctor->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب بيانات الطبيب.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function availableSlots(Request $request, $doctor_id)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        // تحقق من وجود الطبيب
        $doctorExists = \App\Models\doctor::where('id', $doctor_id)->exists();
        if (!$doctorExists) {
            return response()->json(['message' => 'الطبيب غير موجود'], 404);
        }

        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeekIso % 7; // Laravel: 1 = Mon, 7 = Sun → 0 = Sun, 6 = Sat
        $dayName = $date->translatedFormat('l');

        $schedules = doctor_schedules::where('doctor_id', $doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->get();

        if ($schedules->isEmpty()) {
            return response()->json(['message' => 'لا يوجد دوام للطبيب في هذا اليوم'], 404);
        }

        $allSlots = [];

        foreach ($schedules as $schedule) {
            $start = Carbon::parse($schedule->start_time);
            $end = Carbon::parse($schedule->end_time);
            $slot = $schedule->slot_duration;

            while ($start->lt($end)) {
                $slotTime = $start->format('H:i:s');

                // تحقق إذا محجوز
                $isBooked = appointments::where('doctor_id', $doctor_id)
                    ->whereDate('appointment_date', $date)
                    ->whereTime('appointment_time', $slotTime)
                    ->exists();

                $allSlots[] = [
                    'time' => $slotTime,
                    'status' => $isBooked ? 'booked' : 'available'
                ];

                $start->addMinutes($slot);
            }
        }

        return response()->json([
            'date' => $date->toDateString(),
            'day_name' => $dayName,
            'slots' => $allSlots
        ]);
    }

    public function store(Request $request, $doctor_id)
    {
        $patient = Auth::guard('api-patient')->user(); // guard المخصص للمرضى

        if (!$patient) {
            return response()->json(['message' => 'المريض غير مسجل الدخول'], 401);
        }

        // تحقق من صحة البيانات
        $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        // احصل على اليوم من تاريخ الحجز
        $dayOfWeek = Carbon::parse($request->appointment_date)->dayOfWeekIso % 7;

// تحقق هل لدى الطبيب دوام في هذا اليوم ويشمل هذا الوقت
        $hasValidSchedule = \App\Models\doctor_schedules::where('doctor_id', $doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->whereTime('start_time', '<=', $request->appointment_time)
            ->whereTime('end_time', '>', $request->appointment_time)
            ->exists();

        if (!$hasValidSchedule) {
            return response()->json(['message' => 'الطبيب لا يعمل في هذا الوقت'], 400);
        }


        // تحقق إذا الوقت محجوز
        $isBooked = appointments::where('doctor_id', $doctor_id)
            ->whereDate('appointment_date', $request->appointment_date)
            ->whereTime('appointment_time', $request->appointment_time)
            ->exists();

        if ($isBooked) {
            return response()->json(['message' => 'الوقت محجوز مسبقاً'], 409);
        }

        // أنشئ الموعد
        $appointment = appointments::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'تم حجز الموعد بنجاح',
            'data' => $appointment
        ], 201);
    }

    public function update(Request $request, $appointment_id)
    {
        $patient = Auth::guard('api-patient')->user();

        if (!$patient) {
            return response()->json(['message' => 'المريض غير مسجل الدخول'], 401);
        }

        // العثور على الموعد وتأكد أنه يعود للمريض
        $appointment = appointments::where('id', $appointment_id)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$appointment) {
            return response()->json(['message' => 'لم يتم العثور على الموعد أو لا يخص هذا المريض'], 404);
        }

        // التحقق من الوقت المتبقي للموعد
        $appointmentDateTime = Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time);
        $now = Carbon::now();

        if ($appointmentDateTime->diffInMinutes($now, false) > -60) {
            return response()->json(['message' => 'لا يمكنك تعديل الموعد قبل أقل من ساعة من موعده'], 403);
        }


        // تحقق هل الطبيب لديه دوام في التاريخ والوقت الجديد
        $dayOfWeek = Carbon::parse($request->appointment_date)->dayOfWeekIso % 7;

        $hasValidSchedule = \App\Models\doctor_schedules::where('doctor_id', $appointment->doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->whereTime('start_time', '<=', $request->appointment_time)
            ->whereTime('end_time', '>', $request->appointment_time)
            ->exists();

        if (!$hasValidSchedule) {
            return response()->json(['message' => 'الطبيب لا يعمل في هذا الوقت الجديد المحدد'], 400);
        }

        // تحقق من صحة البيانات الجديدة
        $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        // تحقق إذا الموعد الجديد محجوز مسبقاً
        $isBooked = appointments::where('doctor_id', $appointment->doctor_id)
            ->whereDate('appointment_date', $request->appointment_date)
            ->whereTime('appointment_time', $request->appointment_time)
            ->where('id', '!=', $appointment->id) // لا نحتسب الموعد الحالي
            ->exists();

        if ($isBooked) {
            return response()->json(['message' => 'الوقت الجديد محجوز مسبقاً'], 409);
        }

        // تعديل بيانات الموعد
        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'pending', // يمكن إعادة الحالة إلى "قيد الانتظار"
        ]);

        return response()->json([
            'message' => 'تم تعديل الموعد بنجاح',
            'data' => $appointment
        ], 200);
    }


    public function cancelAppointment($appointment_id)
    {
        $patient = Auth::guard('api-patient')->user();

        if (!$patient) {
            return response()->json(['message' => 'المريض غير مسجل الدخول'], 401);
        }

        $appointment = appointments::find($appointment_id);

        if (!$appointment) {
            return response()->json(['message' => 'الموعد غير موجود'], 404);
        }

        // تحقق من ملكية الموعد
        if ($appointment->patient_id !== $patient->id) {
            return response()->json(['message' => 'لا تملك صلاحية إلغاء هذا الموعد'], 403);
        }

        $appointmentDateTime = Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time);
        $now = Carbon::now();

        // تحقق إن الموعد مضى بالفعل
        if ($appointmentDateTime->isPast()) {
            return response()->json(['message' => 'لا يمكن إلغاء موعد مضى وقته'], 400);
        }

        // تحقق إن الموعد لم يتبق عليه أقل من ساعة
        if ($now->diffInMinutes($appointmentDateTime, false) < 60) {
            return response()->json(['message' => 'لا يمكن إلغاء الموعد قبل أقل من ساعة من الموعد'], 400);
        }

        // إلغاء الموعد
        $appointment->status = 'cancelled';
        $appointment->save();

        return response()->json(['message' => 'تم إلغاء الموعد بنجاح'], 200);
    }



    public function myAppointments()
    {
        $patient = Auth::guard('api-patient')->user();

        if (!$patient) {
            return response()->json(['message' => 'المريض غير مسجل الدخول'], 401);
        }

        $appointments = appointments::with('doctor') // لو حابة تجيب بيانات الطبيب مع المواعيد
        ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        return response()->json([
            'message' => 'تم جلب مواعيد المريض بنجاح',
            'data' => $appointments
        ]);
    }

















    public function searchDoctorByName(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $search = $request->name;

        $doctors = Doctor::with('specialization')
            ->where('first_name', 'LIKE', "%$search%")
            ->orWhere('last_name', 'LIKE', "%$search%")
            ->get();

        if ($doctors->isEmpty()) {
            return response()->json([
                'message' => 'لا يوجد أطباء بالاسم المطلوب.'
            ], 404);
        }

        $results = $doctors->map(function ($doctor) {
            return [
                'id' => $doctor->id,
                'first_name' => $doctor->first_name,
                'last_name' => $doctor->last_name,
                'email' => $doctor->email,
                'phone' => $doctor->phone,
                'DateOfBirth' => $doctor->DateOfBirth,
                'Nationality' => $doctor->Nationality,
                'image_url' => $doctor->image ? asset('storage/' . $doctor->image) : null,
                'ClinicAddress' => $doctor->ClinicAddress,
                'CurriculumVitae' => $doctor->CurriculumVitae,
                'ProfessionalAssociationPhoto' => $doctor->ProfessionalAssociationPhoto,
                'CertificateCopy' => $doctor->CertificateCopy,
                'consultation_fee' => $doctor->consultation_fee,
                'specialization_name' => optional($doctor->specialization)->name,
                'created_at' => $doctor->created_at,
                'updated_at' => $doctor->updated_at,
            ];
        });

        return response()->json([
            'message' => 'تم العثور على الأطباء.',
            'doctors' => $results,
        ], 200);
    }

}
