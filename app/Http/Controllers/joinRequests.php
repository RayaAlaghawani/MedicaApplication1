<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;  // ← أضف هذا السطر
use App\Mail\SendapproveJoinRequest;
use App\Mail\SendEmailVervication;
use App\Mail\SendrejectJoinRequest;
use App\Models\doctor;
use App\Models\joinRequest;
use App\Models\specialization;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
class joinRequests extends Controller
{
    public function getAllJoinRequests()
    {
        // جلب جميع طلبات الانضمام التي حالتها معلقة
        $joinRequests = joinRequest::where('status', 'pending')->get();

        if ($joinRequests->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد طلبات انضمام معلقة حالياً.',
                'data' => [],
            ], 404);
        }

        $data = [];
        foreach ($joinRequests as $joinRequest) {
            // جلب التخصص المرتبط بالطلب
            $specialization = specialization::find($joinRequest->specialization_id);

            // تنظيف البريد من أي رموز غير مرغوبة
            $cleanEmail = filter_var($joinRequest->email, FILTER_SANITIZE_EMAIL);

            $data[] = [
                'id' => $joinRequest->id,
                'status' => $joinRequest->status,
                'specializationName' => $specialization ? $specialization->name : null,
                'firstName' => $joinRequest->first_name,
                'lastName' => $joinRequest->last_name,
                'email' => $cleanEmail,
                'phone' => $joinRequest->phone,
                'specializationId' => $joinRequest->specialization_id,
                'dateOfBirth' => date('Y-m-d', strtotime($joinRequest->DateOfBirth)),
                'nationality' => $joinRequest->Nationality,
                'clinicAddress' => $joinRequest->ClinicAddress,
                'consultationFee' => floatval($joinRequest->consultation_fee),
                'emailVerifiedAt' => $joinRequest->email_verified_at ? date('Y-m-d H:i:s', strtotime($joinRequest->email_verified_at)) : null,
                'imageUrl' => $joinRequest->image ? asset('storage/' . $joinRequest->image) : null,
                'certificateCopyUrl' => $joinRequest->CertificateCopy ? asset('storage/' . $joinRequest->CertificateCopy) : null,
                'CurriculumVitae_url' => $joinRequest->CurriculumVitae ? asset('storage/' . $joinRequest->CurriculumVitae) : null,
                'professionalAssociationPhotoUrl' => $joinRequest->ProfessionalAssociationPhoto ? asset('storage/' . $joinRequest->ProfessionalAssociationPhoto) : null,
            ];
        }

        return response()->json([
            'message' => 'تم جلب طلبات الانضمام بنجاح.',
            'data' => $data,
        ], 200);
    }

////////////////////////////////////////////////////
    public function approveJoinRequest($id)
    {
        $joinRequest = joinRequest::find($id);

        if (!$joinRequest) {
            return response()->json(['message' => 'طلب الانضمام غير موجود'], 404);
        }

        if ($joinRequest->status === 'accepted') {
            return response()->json(['message' => 'الطلب تمت الموافقة عليه مسبقًا'], 409);
        }

        $doctor = doctor::create([
            'email' => $joinRequest->email,
            'first_name' => $joinRequest->first_name,
            'last_name' => $joinRequest->last_name,
            'phone' => $joinRequest->phone,
            'device_token' => $joinRequest->device_token,
            'image' => $joinRequest->image,
            'DateOfBirth' => $joinRequest->DateOfBirth,
            'CurriculumVitae' => $joinRequest->CurriculumVitae,
            'Nationality' => $joinRequest->Nationality,
            'ClinicAddress' => $joinRequest->ClinicAddress,
            'ProfessionalAssociationPhoto' => $joinRequest->ProfessionalAssociationPhoto,
            'CertificateCopy' => $joinRequest->CertificateCopy,
            'consultation_fee' => $joinRequest->consultation_fee,
            'password' => $joinRequest->password,
            'specialization_id' => $joinRequest->specialization_id,
        ]);

        $joinRequest->update([
            'status' => 'accepted',
            'doctor_id' => $doctor->id,
        ]);
        $message = 'تمت الموافقة على طلب انضمامك إلى المنصة بنجاح. يمكنك الآن تسجيل الدخول باستخدام بريدك الإلكتروني وكلمة المرور.';

        Mail::to($joinRequest->email)->send(new SendapproveJoinRequest($message, $doctor->first_name));

        $specializationName = $doctor->specialization ? $doctor->specialization->name : null;

        return response()->json([
            'message' => 'تمت الموافقة على طلب الانضمام بنجاح.',
            'doctor' => [
                'id' => $doctor->id,
                'firstName' => $doctor->first_name,
                'lastName' => $doctor->last_name,
                'email' => $doctor->email,
                'phone' => $doctor->phone,
                'specialization' => $specializationName,
                'clinicAddress' => $doctor->ClinicAddress,
                'consultationFee' => $doctor->consultation_fee,
                'nationality' => $doctor->Nationality,
                'dateOfBirth' => $doctor->DateOfBirth,
                'imageUrl' => $doctor->image ? asset('storage/' . $doctor->image) : null,
                'certificateCopyUrl' => $doctor->CertificateCopy ? asset('storage/' . $doctor->CertificateCopy) : null,
                'CurriculumVitae_url' => $doctor->CurriculumVitae ? asset('storage/' . $doctor->CurriculumVitae) : null,
                'professionalAssociationPhotoUrl' => $doctor->ProfessionalAssociationPhoto ? asset('storage/' . $doctor->ProfessionalAssociationPhoto) : null,
            ],
        ], 200);
    }

///////////////////////////////
    public function rejectJoinRequest(Request $request, $id)
    {
        $request->validate([
            'rejection_message' => 'required|string|max:1000',
        ]);

        $joinRequest = joinRequest::find($id);

        if (!$joinRequest) {
            return response()->json(['message' => 'طلب الانضمام غير موجود'], 404);
        }

        if ($joinRequest->status === 'rejected') {
            return response()->json(['message' => 'الطلب تم رفضه مسبقًا'], 409);
        }

        $joinRequest->update([
            'status' => 'rejected',
        ]);

        $message = $request->input('rejection_message');
        $doctorName = $joinRequest->first_name . ' ' . $joinRequest->last_name;

        Mail::to($joinRequest->email)->send(new SendrejectJoinRequest($doctorName, $message));

        return response()->json([
            'message' => 'تم رفض طلب الانضمام بنجاح مع ارسال رسالة الرفض.',
            'rejection_message' => $message,
        ], 200);
    }
    // جلب جميع طلبات الانضمام التي حالتها مقبولة
    public function getAllJoinRequestsAprove()
    {
        $joinRequests = joinRequest::where('status', 'accepted')->get();

        if ($joinRequests->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد طلبات انضمام مقبولة حالياً.',
                'data' => [],
            ], 404);
        }

        $data = [];
        foreach ($joinRequests as $joinRequest) {
            // جلب التخصص المرتبط بالطلب
            $specialization = specialization::find($joinRequest->specialization_id);

            // تنظيف البريد من أي رموز غير مرغوبة
            $cleanEmail = filter_var($joinRequest->email, FILTER_SANITIZE_EMAIL);

            $data[] = [
                'id' => $joinRequest->id,
                'status' => $joinRequest->status,
                'specializationName' => $specialization ? $specialization->name : null,
                'firstName' => $joinRequest->first_name,
                'lastName' => $joinRequest->last_name,
                'email' => $cleanEmail,
                'phone' => $joinRequest->phone,
                'specializationId' => $joinRequest->specialization_id,
                'dateOfBirth' => date('Y-m-d', strtotime($joinRequest->DateOfBirth)),
                'nationality' => $joinRequest->Nationality,
                'clinicAddress' => $joinRequest->ClinicAddress,
                'consultationFee' => floatval($joinRequest->consultation_fee),
                'emailVerifiedAt' => $joinRequest->email_verified_at ? date('Y-m-d H:i:s', strtotime($joinRequest->email_verified_at)) : null,
                'imageUrl' => $joinRequest->image ? asset('storage/' . $joinRequest->image) : null,
                'certificateCopyUrl' => $joinRequest->CertificateCopy ? asset('storage/' . $joinRequest->CertificateCopy) : null,
                'CurriculumVitae_url' => $joinRequest->CurriculumVitae ? asset('storage/' . $joinRequest->CurriculumVitae) : null,
                'professionalAssociationPhotoUrl' => $joinRequest->ProfessionalAssociationPhoto ? asset('storage/' . $joinRequest->ProfessionalAssociationPhoto) : null,
            ];
        }

        return response()->json([
            'message' => 'تم جلب طلبات الانضمام بنجاح.',
            'data' => $data,
        ], 200);
    }

}
