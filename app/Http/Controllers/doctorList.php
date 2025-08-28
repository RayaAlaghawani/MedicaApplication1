<?php

namespace App\Http\Controllers;

use App\Http\Resources\DoctorResource;
use App\Mail\SendEmailVervication;
use App\Models\doctor;
use App\Models\doctor_schedules;
use App\Models\doctorPending;
use App\Models\emailverfication;
use App\Models\Patient;
use App\Models\specialization;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class doctorList extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //بحث عن اطباء من اجل طبيب
    public function searchForDoctor(Request $request)
    {
        $request->validate = ([
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'specialization_name' => 'nullable|string',
        ]);
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $specialization_name = $request->specialization_name;

        $query = doctor::query();
        if ($first_name) {
            $query->where('first_name', 'like', '%' . $first_name . '%');
            if ($last_name) {
                $query->where('last_name', 'like', '%' . $last_name . '%');
            }
            if ($specialization_name) {
                $query->whereHas('specialization', function ($q) use ($specialization_name) {
                    $q->where('name', $specialization_name);

                });
            }
            $data = $query->get();
            if ($data->isEmpty()) {
                return response()->json([
                    'message' => 'لم يتم العثور على أي اطباء بالمواصفات المطلوبة.',
                    'data' => [],
                ], 404);

            }
            return response()->json([
                'message' => 'success',
                'data' => DoctorResource::collection($data),
            ], 200);
        }
    }

    //اضافة  صور وسيرة ذاتية طبيب
    public function addInformation(Request  $request,$id)
    {
        $data = $request->validate([
            'image' => 'required|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'CurriculumVitae' => 'required|nullable|file|mimes:pdf,doc,docx|max:5120',
            'ProfessionalAssociationPhoto' => 'required|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'CertificateCopy' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

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
        $doctors = doctor::where('id', $id)->first();
        if ($doctors) {
            $doctors->update($data);
            return response()->json([
                'message' => 'Doctor created successfully',
                'data' => new DoctorResource($doctors),
            ], 201);
        }
        return response()->json([
            'message' => 'Doctor not found',
            'data' => null,
        ], 404);
    }



    //عرض// معلومات كل الاطياء//////
    public function indexDoctors()
    {
        $doctors = doctor::all();


        if ($doctors->isEmpty()) {
            return response()->json([
                'message' => 'لا يوجد اطباء مسجلين في التطبيق.',
                'data' => [],
            ], 404);
        }
        return response()->json([
            'message' => 'تم جلب كل الاطباء الموجودين بالتطبيق.',
            'data' => DoctorResource::collection($doctors),
        ], 200);
    }


    /**
     * Show the form for creating a new resource.
     */
    //////بحث عن طبيب
    public function search(Request $request)
    {
        $request->validate = ([
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'specialization_name' => 'nullable|string',
            'consultation_fee' => 'nullable|numeric',
        ]);
        $first_name=$request->first_name;
        $last_name=$request->last_name;
        $consultation_fee=$request->consultation_fee;
        $specialization_name=$request->specialization_name;

        $query=doctor::query();
        if($first_name){
            $query->where('first_name','like','%'.$first_name.'%');
        }if($consultation_fee) {
        $query->where('consultation_fee', 'like', '%' . $consultation_fee . '%');

    }
        if($last_name){
            $query->where('last_name','like','%'.$last_name.'%');
        }
        if($specialization_name){
            $query->whereHas('specialization',function($q) use($specialization_name){
                $q->where('name',$specialization_name);

            });}
        $data=$query->get();
        if($data->isEmpty()){
            return response()->json([
                'message' => 'لم يتم العثور على أي اطباء بالمواصفات المطلوبة.',
                'data' => [],
            ], 404);

        }
        return response()->json([
            'message' => 'success',
            'data' => DoctorResource::collection($data),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */ ///
//اضافة طبيب
    public function store(Request $request)
    {
        $data = $request->validate([
            'specialization_id'            => 'required|exists:specializations,id',
            'first_name'                   => 'required|string|max:255',
            'last_name'                    => 'required|string|max:255',
            'device_token'                 => 'nullable|string',
            'email'                        => 'required|email|unique:doctors,email|unique:doctors,email',
            'phone'                        => 'required|digits:10|unique:doctors,phone|unique:doctors,phone',
            'password'                     => 'required|string|min:6|confirmed',
            'DateOfBirth'                => 'required|date',
            'Nationality'                 => 'required|string|max:255',
            'ClinicAddress'              => 'required|string|max:500',
            'consultation_fee'            => 'required|numeric|min:0',
        ], [
            'email.unique' => 'Email already exists!',
            'phone.unique' => 'Phone already exists!',
        ]);

        $data['password'] = Hash::make($request->password);

        $doctor = doctor::create($data);

        return response()->json([
            'message' => 'Doctor created successfully',
            'data' => new DoctorResource($doctor),
        ], 201);
    }
////////////////////////////////////////////////

    /**
     * Display the specified resource.
     */
    //عرض مواعيد طبيب
    public function show($id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor not found',
                'data' => [],
            ], 404);
        }

        $schedules = doctor_schedules::where('doctor_id', $doctor->id)->orderBy('day_of_week')->orderBy('start_time')->get();
        $orderedDays = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        if ($schedules->isNotEmpty()) {
            $grouped = $schedules->groupBy(function ($item) {
                return $item->day_name;
            })->map(function ($items) {
                return $items->values();
            });

            $result = [];
            foreach ($orderedDays as $day) {
                $result[$day] = $grouped->get($day, collect());
            }

            return response()->json([
                'message' => 'تم استرجاع جدول المواعيد الأسبوعي بنجاح.',
                'data' => $result,
            ], 200);
        }

        $emptyResult = [];
        foreach ($orderedDays as $day) {
            $emptyResult[$day] = [];
        }

        return response()->json([
            'message' => 'لا يوجد عناصر لعرضها',
            'data' => $emptyResult,
        ], 404);
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
    //تعديل معلومات طبيب
    public function update(Request $request,  $id)
    {
        $doctor = doctor::find($id);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }
        $data = $request->validate([
            'specialization_id'              => 'nullable|exists:specializations,id',
            'first_name'                     => 'nullable|string|max:255',
            'last_name'                      => 'nullable|string|max:255',
            'device_token'                   => 'nullable|string',
            'email'                          => 'nullable|email|unique:doctors,email',
            'phone'                          => 'nullable|digits:10|unique:doctors,phone',
            'password'                       => 'nullable|string|min:6|confirmed',
            'image'                          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'DateOfBirth'                    => 'nullable|date',
            'CurriculumVitae'                => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'Nationality'                    => 'nullable|string|max:255',
            'ClinicAddress'                  => 'nullable|string|max:500',
            'ProfessionalAssociationPhoto'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'CertificateCopy'                => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'consultation_fee'               => 'nullable|numeric|min:0',
        ], [
            'email.unique' => 'Email already exists!',
            'phone.unique' => 'Phone already exists!',
        ]);
        if($request->has('image')) {
            $data['image'] = $request->file('image')->store('doctor_images', 'public');
        }


        if($request->has('CurriculumVitae')){
            $data['CurriculumVitae']=$request->file('CurriculumVitae')->store('cv_files','public');

        }
        if($request->has('CertificateCopy')){
            $data['CertificateCopy']=$request->file('CertificateCopy')->store('certificates','public');

        }
        if($request->has('ProfessionalAssociationPhoto')) {
            $data['ProfessionalAssociationPhoto'] = $request->file('ProfessionalAssociationPhoto')->store('association_photos', 'public');
        }
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $doctor->update($data);
        $doctor->refresh();

        return response()->json([
            'message' => 'Doctor updated successfully',
            'data' => new DoctorResource($doctor),
        ], 200);
    }
    /*
     * Remove the specified resource from storage.
     */
    ///حذف طبيب
    public function destroy(string $id)
    {
        //
    }
    ///////////////عرض تخصصات

    public function indexAllSpecialization()
    {
        $specializations = Specialization::select('id', 'name')->get();

        return response()->json([
            'data' => $specializations
        ]);
    }

}
