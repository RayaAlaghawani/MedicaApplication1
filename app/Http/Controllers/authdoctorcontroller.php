<?php

namespace App\Http\Controllers;

use App\Mail\SendEmailVervication;
use App\Models\doctor;
use App\Models\doctorPending;
use App\Models\emailverfication;
use App\Models\joinRequest;
use App\Models\specialization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class authdoctorcontroller extends Controller
{
    public function register_doctor(Request $request)
    {
        $data = $request->validate([
            'specialization_id'            => 'required|exists:specializations,id',
            'first_name'                   => 'required|string|max:255',
            'last_name'                    => 'required|string|max:255',
            'device_token'                 => 'nullable|string',
            'email'                        => 'required|email|unique:doctors,email',
            'phone'                        => 'required|digits:10|unique:doctors,phone',
            'password'                     => 'required|string|min:6|confirmed',
            'image'                        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'DateOfBirth'                  => 'required|date',
            'CurriculumVitae'              => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'Nationality'                  => 'required|string|max:255',
            'ClinicAddress'                => 'required|string|max:500',
            'ProfessionalAssociationPhoto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'CertificateCopy'              => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'consultation_fee'             => 'required|numeric|min:0',
        ], [
            'email.unique' => 'email already exists!!',
            'phone.unique' => 'phone already exists!!'
        ]);

        if (doctorPending::where('email', $data['email'])->exists()) {
            return response()->json(['message' => 'email already exists!!'], 403);
        }

        if ($request->hasFile('CurriculumVitae')) {
            $data['CurriculumVitae'] = $request->file('CurriculumVitae')->store('cv_files', 'public');
        }

        if ($request->hasFile('CertificateCopy')) {
            $data['CertificateCopy'] = $request->file('CertificateCopy')->store('certificates', 'public');
        }

        if ($request->hasFile('ProfessionalAssociationPhoto')) {
            $data['ProfessionalAssociationPhoto'] = $request->file('ProfessionalAssociationPhoto')->store('association_photos', 'public');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('doctor_images', 'public');
        }

        $data['password'] = bcrypt($request->password);

        $user = doctorPending::create($data);

        emailverfication::where('email', $data['email'])->delete();

        $code = mt_rand(100000, 999999);
        emailverfication::create([
            'email' => $data['email'],
            'code'  => $code,
        ]);

        Mail::to($data['email'])->send(new SendEmailVervication($code));

        return response()->json([
            'message' => 'Registration success. Please check your email to verify your account',
        ], 201);
    }
////////////////////////////////////////////////
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'code' => 'required|string'
        ]);
        $emailverfication = emailverfication::where('email', $request->email)
            ->where('code', $request->code)
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->first();
        if (!$emailverfication) {
            return response()->json(['message' => 'Invalid or expired Verification code'], 400);
        }
        $pendingDoctor = doctorPending::where('email', $request->email)->first();

        if (!$pendingDoctor) {
            return response()->json(['message' => 'Doctor not found in pending list'], 404);
        }
        $pendingDoctor->email_verified_at = Carbon::now();
        $pendingDoctor->save();
        $f = joinRequest::create([
            'specialization_id'=>$pendingDoctor->specialization_id,
            'email' => $pendingDoctor->email,
            'doctor_id' =>null,
            'status' => 'pending',
            'first_name' => $pendingDoctor->first_name,
            'last_name' => $pendingDoctor->last_name,
            'phone' => $pendingDoctor->phone,
            'device_token' => $pendingDoctor->device_token,
            'image' => $pendingDoctor->image,
            'DateOfBirth' => $pendingDoctor->DateOfBirth,
            'CurriculumVitae' => $pendingDoctor->CurriculumVitae,
            'Nationality' => $pendingDoctor->Nationality,
            'ClinicAddress' => $pendingDoctor->ClinicAddress,
            'ProfessionalAssociationPhoto' => $pendingDoctor->ProfessionalAssociationPhoto,
            'CertificateCopy' => $pendingDoctor->CertificateCopy,
            'consultation_fee' => $pendingDoctor->consultation_fee,
            'password' => $pendingDoctor->password,
        ]);
        $emailverfication->delete();
        return response()->json([
            'message' => 'Email verified successfully. Please wait for admin approval.',
        ], 200);
    }
    //////////////////////login
    public function login_user(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:doctors,email'],
            'password' => ['required']
        ]);

        $credentials = $request->only('email', 'password');
        if (!Auth()->guard('doctor')->attempt($request->only(['email','password']))) {

            return response()->json(['message' => 'Invalid login credentials.'], 401);
        }


        $user = doctor::where('email', $request->email)->first();

        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Email is not verified. Please check your email inbox.'
            ], 401);
        }

        $joinrequest = joinRequest::where('doctor_id', $user->id)->first();

        if (!$joinrequest || $joinrequest->status !== 'accepted') {
            return response()->json([
                'message' => 'You cannot log in because your join request has not been approved by the administration.'
            ], 401);
        }

        if ($request->filled('device_token')) {
            $user->device_token = $request->device_token;
            $user->save();
        }

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        // تنسيق البيانات وإضافة روابط الصور والملفات
        $userData = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'specialization_id' => $user->specialization_id,
            'DateOfBirth' => $user->DateOfBirth,
            'Nationality' => $user->Nationality,
            'ClinicAddress' => $user->ClinicAddress,
            'consultation_fee' => $user->consultation_fee,
            'email_verified_at' => $user->email_verified_at,
            'image_url' => $user->image ? asset('storage/' . $user->image) : null,
            'CertificateCopy_url' => $user->CertificateCopy ? asset('storage/' . $user->CertificateCopy) : null,
            'CurriculumVitae_url' => $user->CurriculumVitae ?
                asset('storage/' . $user->CurriculumVitae) : null,
            'ProfessionalAssociationPhoto_url' => $user->ProfessionalAssociationPhoto ? asset('storage/' . $user->ProfessionalAssociationPhoto) : null,
        ];

        return response()->json([
            'message' => 'Welcome!',
            'token' => $token,
            'user' => $userData
        ], 200);
    }
///logout
    public function logout_user()
    {
        Auth::User()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'goodbuy!!!'], 200);

    }
}
