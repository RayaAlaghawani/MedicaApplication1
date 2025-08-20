<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerificationMail;
use App\Models\Patient;
use App\Models\RecordMedical;
use App\Models\specialization;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Nette\Schema\ValidationException;


class PatientController extends Controller
{
    //regester
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:patients,email',
            'password'=>'required|string|confirmed',
            'gender' => 'nullable|in:Male,Female,Other',
            'age'    => 'nullable|integer|min:0|max:150',

        ]);

        $verificationCode = rand(100000, 999999);

        $patient = Patient::create([
            'name'  => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'gender' => $request->gender,
            'age'    => $request->age,
            'email_verified' => false,
            'email_verification_code' => $verificationCode,

        ]);

        Mail::to($patient->email)->send(new EmailVerificationMail($verificationCode));

        return response()->json([
            'message' => 'تم إنشاء الحساب، يرجى التحقق من البريد الإلكتروني بإدخال كود التفعيل',
            'user'=> $patient,
            'user_id' => $patient->id,
        ], 201);
    }
    ///////////verifyEmail
    public function verifyEmail(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|numeric'
        ]);

        $patient = Patient::findOrFail($id);

        if ($patient->email_verified) {
            return response()->json(['message' => 'تم التحقق من البريد الإلكتروني مسبقًا.'], 200);
        }

        if ($patient->email_verification_code == $request->code) {
            $patient->email_verified = true;
            $patient->email_verification_code = null;
            $patient->save();
            $token = $patient->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'تم التحقق من البريد الإلكتروني بنجاح.',
                'token' => $token,
                'user' => $patient
            ], 200);
        }

        return response()->json(['message' => 'رمز التحقق غير صحيح.'], 400);
    }

    //////login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $patient = Patient::where('email', $request->email)->first();

        if (!$patient || !Hash::check($request->password, $patient->password)) {
            return response()->json([
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
            ], 401);
        }

        if (!$patient->email_verified) {
            return response()->json([
                'message' => 'يرجى تفعيل البريد الإلكتروني أولاً'
            ], 403);
        }

        $token = $patient->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'email' => $patient->email,
                'gender' => $patient->gender,
                'age' => $patient->age,
                //'email_verified' => $patient->email_verified,
            ],
            'token' => $token,
        ], 201);

    }
    ////forget password
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:patients,email',
        ]);

        $resetCode = rand(100000, 999999);

        $patient = Patient::where('email', $request->email)->first();
        $patient->email_verification_code = $resetCode;
        $patient->save();

        Mail::to($patient->email)->send(new EmailVerificationMail($resetCode));

        return response()->json([

            'id' => $patient->id,
            'message' => 'تم إرسال رمز إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.',
            'code' =>$resetCode,


        ]);
    }
////////resett password
    public function resetPassword(Request $request,$id)
    {
        $request->validate([
            'verification_code' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $patient = Patient::findOrFail($id);

        if ($patient->email_verification_code !== $request->verification_code) {
            return response()->json(['message' => 'رمز التحقق غير صحيح'], 400);
        }

        $patient->password = bcrypt($request->password);
        $patient->email_verification_code = null;
        $patient->save();

        return response()->json([
            'message' => 'تم تعيين كلمة المرور الجديدة بنجاح. يمكنك الآن تسجيل الدخول.',
        ]);
    }
    /////showProfile
    public function showProfile()
    {
        /** @var \App\Models\Patient $user */
        $user = auth('api-patient')->user();
        $user->profile_image_url = $user->profile_image ? asset('storage/' . $user->profile_image) : null;

        return response()->json([
            'message' => 'تم عرض الملف الشخصي بنجاح',
            'patient' => $user,
        ]);
    }
    /////////update    profile
    public function updateProfile(Request $request)

    {

        /** @var \App\Models\Patient $user */
        $user = auth('api-patient')->user();

        $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:patients,email,' . $user->id,
            'gender' => 'sometimes|in:Male,Female,Other',
            'age' => 'sometimes|integer|min:0|max:150',
            'password' => 'sometimes|string|confirmed',
            'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->fill($request->except('password', 'password_confirmation', 'profile_image'))->save();

        $user->profile_image_url = $user->profile_image ? asset('storage/' . $user->profile_image) : null;

        return response()->json([
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'patient' => $user,
        ]);
    }

//    //////////////log out
    public function logout()
    {
      //  Auth::User()->currentAccessToken()->delete();

        $patient = auth('api-patient')->user();

        if ($patient && $patient->currentAccessToken()) {
            $patient->currentAccessToken()->delete();


            return response()->json([
                'message' => 'Patient logged out successfully.',
                'status' => 'success'
            ], 200);
        }

        return response()->json([
            'message' => 'No patient found.',
            'status' => 'error'
        ], 401);

    }



    ///////add specialization

    public function addspecialization(Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $validated = $request->validate([
                'name' => 'required|string|unique:specializations,name',
            ]);

            // إنشاء التخصص
            $specialization = Specialization::create([
                'name' => $validated['name'],
            ]);

            return response()->json([
                'message' => 'تم إضافة التخصص بنجاح.',
                'specialization' => $specialization
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'فشل في التحقق من البيانات.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء إضافة التخصص.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //////////AvailableSpecializations

    public function getAvailableSpecializations()
    {
        $specializations = Specialization::has('doctors')->pluck('name');

        return response()->json([
            'specializations' => $specializations
        ]);
    }

    //////////////////AllSpecializations
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

///////السجل الطبي

    public function storeChildMedicalRecord(Request $request)
    {
        try {
            /** @var \App\Models\Patient|null $user */
            $user = auth('api-patient')->user();

            if (!$user) {
                return response()->json(['message' => 'المستخدم غير مسجل الدخول'], 401);
            }

            if ($user->age >= 18) {
                return response()->json(['message' => 'هذا التابع مخصص للأطفال فقط.'], 403);
            }

            $validated = $request->validate([
                'guardian_name' => 'required|string',
                'guardian_phone' => 'required|string',
                'residence' => 'required|string',
                'child_sleeps_well' => 'required|boolean',
                'has_chronic_disease' => 'required|boolean',
                'takes_medications' => 'required|boolean',
                'has_allergies' => 'required|boolean',
            ]);

            $record = RecordMedical::updateOrCreate(
                ['patient_id' => $user->id],
                $validated
            );

            return response()->json([
                'message' => 'تم حفظ السجل الطبي للطفل.',
                'medical_record' => $record
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'فشل التحقق من البيانات.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ.', 'error' => $e->getMessage()], 500);
        }
    }


    public function storeAdultMedicalRecord(Request $request)
    {
        try {
            /** @var \App\Models\Patient|null $user */
            $user = auth('api-patient')->user();

            if (!$user) {
                return response()->json(['message' => 'المستخدم غير مسجل الدخول'], 401);
            }


            if ($user->age < 18) {
                return response()->json(['message' => 'هذا التابع مخصص للبالغين فقط.'], 403);
            }

            $validated = $request->validate([
                'phone_number' => 'required|string',
                'residence' => 'required|string',
                'marital_status' => 'required|in:Single,Married',
                'profession' => 'required|string',
                'education' => 'required|string',
                'insomnia' => 'required|boolean',
                'has_chronic_disease' => 'required|boolean',
                'takes_medications' => 'required|boolean',
                'has_allergies' => 'required|boolean',
            ]);

            $record = RecordMedical::updateOrCreate(
                ['patient_id' => $user->id],
                $validated
            );

            return response()->json([
                'message' => 'تم حفظ المعلومات للمريض بنجاح .',
                'medical_record' => $record
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'فشل التحقق من البيانات.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ.', 'error' => $e->getMessage()], 500);
        }
    }







}


