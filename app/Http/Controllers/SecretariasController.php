<?php

namespace App\Http\Controllers;

use App\Http\Resources\DoctorResource;
use App\Http\Resources\SecretaryResource;
use App\Mail\SecretaryWelcomeMail;
use App\Models\appointments;
use App\Models\doctor;
use App\Models\secretary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Psy\Util\Str;

class SecretariasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //عرض السكرتاريا
    public function indexallSecretary()
    {
        $Sercretarias = secretary::all();

        if ($Sercretarias->isEmpty()) {
            return response()->json([
                'message' => 'لا يوجد سكرتاريا مسجلين في التطبيق.',
                'data' => [],
            ], 404);
        }
        return response()->json([
            'message' => 'تم جلب كل السكرتاريا الموجودين بالتطبيق.',
            'data' => SecretaryResource::collection($Sercretarias),
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */


    public function addSecretarias(Request $request)
    {
        $doctor_id = Auth::guard('api-doctor')->id();

        if (!$doctor_id) {
            return response()->json([
                'message' => 'Only doctors can create secretaries.'
            ], 403);
        }

        $validatedData = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:secretaries,email',
            'phone'         => 'required|string|max:10',
            'address'       => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cv'            => 'nullable|mimes:pdf,doc,docx|max:4096',
            'password'      => 'required|string|min:6',
        ]);

        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('secretaries/images', 'public');
        }

        if ($request->hasFile('cv')) {
            $validatedData['cv'] = $request->file('cv')->store('secretaries/cv', 'public');
        }

        $doctorName = doctor::find($doctor_id)->name;

        $secretary = Secretary::create([
            'name'          => $validatedData['name'],
            'email'         => $validatedData['email'],
            'phone'         => $validatedData['phone'],
            'address'       => $validatedData['address'],
            'date_of_birth' => $validatedData['date_of_birth'],
            'image'         => $validatedData['image'] ?? null,
            'cv'            => $validatedData['cv'] ?? null,
            'password'      => bcrypt($validatedData['password']),
            'doctor_id'     => $doctor_id,
        ]);

        Mail::to($validatedData['email'])->send(
            new SecretaryWelcomeMail(
                $validatedData['email'],
                $validatedData['password'],
                $doctorName
            )
        );

        return response()->json([
            'message'   => 'Secretary created successfully.',
            'secretary' => [
                'id'          => $secretary->id,
                'name'        => $secretary->name,
                'email'       => $secretary->email,
                'phone'       => $secretary->phone,
                'address'     => $secretary->address,
                'date_of_birth' => $secretary->date_of_birth,
                'imageUrl'    => $secretary->image ? asset('storage/' . $secretary->image) : null,
                'cvUrl'       => $secretary->cv ? asset('storage/' . $secretary->cv) : null,
                'doctor_id'   => $secretary->doctor_id,
                'created_at'  => $secretary->created_at,
                'updated_at'  => $secretary->updated_at,
            ]
        ], 201);
    }
//////////////عرض سكرنريا طبيب
    public function indexallSecretaryForDoctor()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }

        $doctor_id = $user->id;
        $doctor = doctor::where('id', $doctor_id)->first();
        $secretaries = $doctor->secretaries;

        if ($secretaries->isEmpty()) {
            return response()->json([
                'message' => 'No secretaries are registered in the application.',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'message' => 'All secretaries have been retrieved successfully.',
            'data' => SecretaryResource::collection($secretaries),
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }


///////loginSecretary
    public function loginSecretary(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // البحث عن السكرتيرة بواسطة الإيميل
        $secretary = secretary::with('doctor')->where('email', $credentials['email'])->first();

        if (! $secretary) {
            return response()->json([
                'message' => 'The email address is incorrect.'
            ], 401);
        }

        // التحقق من كلمة المرور
        if (! Hash::check($credentials['password'], $secretary->password)) {
            return response()->json([
                'message' => 'The password is incorrect.'
            ], 401);
        }

        // إنشاء التوكن
        $token = $secretary->createToken('secretary-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'secretary' => [
                'id' => $secretary->id,
                'name' => $secretary->name,
                'email' => $secretary->email,
                'phone' => $secretary->phone,
                'address' => $secretary->address,
                'date_of_birth' => $secretary->date_of_birth,
                'imageUrl' => $secretary->image ? asset('storage/' . $secretary->image) : null,
                'cvUrl' => $secretary->cv ? asset('storage/' . $secretary->cv) : null,
                'doctor_id' => $secretary->doctor_id,
                'doctor_name' => $secretary->doctor ? $secretary->doctor->first_name : null,
                'created_at' => $secretary->created_at,
                'updated_at' => $secretary->updated_at,
            ]
        ], 200);
    }

    //// logoutSecretary
    public function logoutSecretary(Request $request)
    {
        $secretary = auth('api-secretary')->user();

        if ($secretary) {
            $secretary->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Secretary logged out successfully.',
                'status' => 'success'
            ], 200);
        }

        return response()->json([
            'message' => 'No secretary found.',
            'status' => 'error'
        ], 401);
    }



    public function allDoctorAppointments()
    {
        /** @var \App\Models\Secretary $secretary */
        $secretary = Auth::guard('api-secretary')->user();

        if (!$secretary) {
            return response()->json(['message' => 'السكرتيرة غير مسجلة الدخول'], 401);
        }

        // معرف الطبيب المرتبطة فيه السكرتيرة
        $doctor_id = $secretary->doctor_id;

        // جلب كل المواعيد مع بيانات المريض
        $appointments = \App\Models\appointments::with('patient')
            ->where('doctor_id', $doctor_id)
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        if ($appointments->isEmpty()) {
            return response()->json([
                'message'   => 'لا يوجد مواعيد لهذا الطبيب',
                'doctor_id' => $doctor_id,
            ], 404);
        }

        return response()->json([
            'message'   => 'تم جلب جميع حجوزات الطبيب بنجاح',
            'doctor_id' => $doctor_id,
            'count'     => $appointments->count(),
            'data'      => $appointments
        ], 200);
    }

// داخل SecretariasController
    public function doctorAppointmentsByDate(Request $request)
    {
        /** @var \App\Models\Secretary $secretary */
        $secretary = Auth::guard('api-secretary')->user();

        if (!$secretary) {
            return response()->json(['message' => 'السكرتيرة غير مسجلة الدخول'], 401);
        }

        // التحقق من إدخال التاريخ
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $doctor_id = $secretary->doctor_id;
        $date      = $validated['date'];

        // جلب المواعيد حسب التاريخ مع المريض
        $appointments = \App\Models\appointments::with('patient')
            ->where('doctor_id', $doctor_id)
            ->whereDate('appointment_date', $date)
            ->orderBy('appointment_time', 'asc')
            ->get();

        if ($appointments->isEmpty()) {
            return response()->json([
                'message'   => "لا يوجد مواعيد في هذا اليوم: {$date}",
                'doctor_id' => $doctor_id,
                'date'      => $date,
            ], 404);
        }

        // تصنيف المواعيد حسب الحالة
        $grouped = $appointments->groupBy('status');

        return response()->json([
            'message'   => 'تم جلب المواعيد لهذا اليوم بنجاح',
            'doctor_id' => $doctor_id,
            'date'      => $date,
            'counts'    => [
                'pending'   => $grouped->get('pending', collect())->count(),
                'confirmed' => $grouped->get('confirmed', collect())->count(),
                'cancelled' => $grouped->get('cancelled', collect())->count(),
            ],
            'data'      => $grouped
        ], 200);
    }


// داخل SecretariasController
    public function getUpcomingAppointments()
    {
        /** @var \App\Models\Secretary $secretary */
        $secretary = Auth::guard('api-secretary')->user();

        if (! $secretary) {
            return response()->json(['message' => 'السكرتيرة غير مسجلة الدخول'], 401);
        }

        if (! $secretary->doctor_id) {
            return response()->json(['message' => 'لا يوجد طبيب مرتبط بهذه السكرتيرة'], 404);
        }

        $now = Carbon::now();

        // جلب كل المواعيد القادمة للطبيب المرتبط بالسكرتيرة
        $appointments = \App\Models\appointments::with('patient')
            ->where('doctor_id', $secretary->doctor_id)
            ->whereDate('appointment_date', '>=', $now->toDateString())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        if ($appointments->isEmpty()) {
            return response()->json([
                'message' => 'لا يوجد مواعيد قادمة لهذا الطبيب',
                'doctor_id' => $secretary->doctor_id,
            ], 404);
        }

        // تجميع المواعيد حسب تاريخ اليوم
        $grouped = $appointments->groupBy(function($item) {
            return Carbon::parse($item->appointment_date)->toDateString();
        });

        return response()->json([
            'message'      => 'المواعيد القادمة للطبيب',
            'doctor_id'    => $secretary->doctor_id,
            'appointments' => $grouped
        ], 200);
    }


    // داخل SecretariasController
    public function updateAppointmentStatus(Request $request, $appointmentId)
    {
        /** @var \App\Models\Secretary $secretary */
        $secretary = Auth::guard('api-secretary')->user();

        if (!$secretary) {
            return response()->json(['message' => 'السكرتيرة غير مسجلة الدخول'], 401);
        }

        // تحقق من البيانات المرسلة
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        // نجيب الحجز
        $appointment = \App\Models\appointments::where('id', $appointmentId)
            ->where('doctor_id', $secretary->doctor_id) // تأكد أن الحجز تابع لطبيب السكرتيرة
            ->first();

        if (!$appointment) {
            return response()->json(['message' => 'الحجز غير موجود أو لا يخص هذا الطبيب'], 404);
        }

        // تحديث الحالة
        $appointment->status = $validated['status'];
        $appointment->save();

        return response()->json([
            'message'     => 'تم تحديث حالة الحجز بنجاح',
            'appointment' => $appointment,
        ], 200);
    }



}

