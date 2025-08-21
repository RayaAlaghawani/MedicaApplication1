<?php

namespace App\Http\Controllers;

use App\Http\Resources\MedicalRecordResource;
use App\Http\Resources\SecretaryResource;
use App\Models\allergies;
use App\Models\doctor;
use App\Models\Examinations;
use App\Models\medications;
use App\Models\PastDiseasesTable;
use App\Models\Patient;
use App\Models\record_medical_past_disease;
use App\Models\RecordMedical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Foreach_;

class medicalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //عرض سجل طبي
    public function showMedicalrecord($id)
    {
        $medicalrecord = RecordMedical::where('patient_id', $id)->first();

        if (!$medicalrecord) {
            return response()->json([
                'status' => 'error',
                'message' => 'Medical record not found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Here is the patients medical record information',
        'data' => new MedicalRecordResource($medicalrecord)
    ], 200);
}

    /**
     * Show the form for creating a new resource.
     */

//تعديل سجل طبي لمريض
    public function updateMedicalRecord(Request $request, string $id)
    {
        $medicalrecord = RecordMedical::find($id);
        if (!$medicalrecord) {
            return response()->json([
                'message' => 'Medical record not found.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'weight' => 'nullable|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:3',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'diet_type' => 'nullable|in:balanced,high_fat,high_sugar,vegetarian,irregular',
            'is_smoker' => 'nullable|in:yes,no',
            'drinks_alcohol'=>'nullable|in:yes,no',
            'sleep_hours' => 'nullable|integer|min:0|max:24',
        ]);

        $medicalrecord->update($data);

        return response()->json([
            'message' => 'Medical record updated successfully.',
            'data' => new MedicalRecordResource($medicalrecord),
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
//اضافة امراض للسجل طبي للمريض
    public function addPastDiseases(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;

        $medicalrecord = RecordMedical::find($id);
        if (!$medicalrecord) {
            return response()->json([
                'message' => 'Medical record not found.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:public,private',
            'code' => 'nullable|string|max:255',
            'diagnosed_at' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $pastDisease = PastDiseasesTable::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'code' => $data['code'] ?? null,
            'diagnosed_at' => $data['diagnosed_at'] ?? null,
            'description' => $data['description'] ?? null,
            'doctor_id' => $doctor_id,
        ]);
        $medicalrecord->PastDiseasesTable()->attach($pastDisease->id);

        return response()->json([
            'message' => 'Past disease added successfully.',
            'data' => new \App\Http\Resources\PastDiseasesTable($pastDisease),
        ], 200);
    }
    /**
     * Display the specified resource.
     */
    //عرض امراض المريض
    public function showPastDiseases($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;

        $medicalrecord = RecordMedical::find($id);
        if (!$medicalrecord) {
            return response()->json([
                'message' => 'Medical record not found.',
                'data' => [],
            ], 404);
        }

        $pastDiseases = $medicalrecord->PastDiseasesTable;

        $data = [];

        foreach ($pastDiseases as $disease) {
            if ($disease->type === 'public' || $disease->doctor_id == $doctor_id) {
                $data[] = [
                    'id'=>$disease->id,
                    'name' => $disease->name,
                 //   'type' => $disease->type,
                 //   'code' => $disease->code,
                  //  'diagnosed_at' => $disease->diagnosed_at,
                  //  'description' => $disease->description,
                  //  'doctor_id' => $disease->doctor_id,
                ];
            }
        }

        if (empty($data)) {
            return response()->json([
                'message' => 'No past diseases found for this medical record.',
                'data' => [],
            ], 204);
        }

        return response()->json([
            'message' => 'Past diseases retrieved successfully.',
            'data' => $data,
        ], 200);
    }
    ////////////////////////////////عرض تفاصيل مرض معين

    public function showDetailILL($id)
    {
        $pastDisease = PastDiseasesTable::where('id',$id)->first();

        if (!$pastDisease) {
            return response()->json([
                'message' => 'The disease details you requested were not found.',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'message' => 'Disease details retrieved successfully.',
            'data' => new \App\Http\Resources\PastDiseasesTable($pastDisease),
        ], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    //تعديل امراض مريض
    public function editPastDiseases(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;


        $pastDisease = PastDiseasesTable::where('id', $id)->first();

        if (!$pastDisease) {
            return response()->json([
                'message' => 'The disease you want to edit does not exist.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'name' => 'string|max:255',
            'type' => 'in:public,private',
            'code' => 'nullable|string|max:255',
            'diagnosed_at' => 'nullable|date',
            'description' => 'nullable|string',
        ]);
        if ($pastDisease->doctor_id == $doctor_id) {
            $pastDisease->update($data);

            return response()->json([
                'message' => 'Disease updated successfully.',
                'data' => new \App\Http\Resources\PastDiseasesTable($pastDisease),
            ], 200);
        }
        return response()->json([
            'message' => 'You cannot edit the details of this disease because you  are not the one who added it.',
            'data' => new \App\Http\Resources\PastDiseasesTable($pastDisease),
        ], 403);
    }

//اضافة دواء
    public function Addmedication(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()
                ->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;

        $medication = medications::find($id);
        if (!$medication) {
            return response()->json([
                'message' => '.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Laboratory,Radiology',
            'image_path' =>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'exam_date' => 'nullable|date',
            'summary' => 'required|string',
        ]);
        if($request->has('image_path')) {
            $imagePath = $request->file('image_path')->store('Examination_images', 'public');
        }

        $Examinations= Examinations::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'image_path' => $imagePath,
            'summary' => $data['diagnosed_at'] ?? null,
            'exam_date' => $data['exam_date'],
            'doctor_id' => $doctor_id,
            'record_medical_id'=>$id
        ]);

        return response()->json([
            'message' => 'Examinations  added successfully.',
            'data' => new \App\Http\Resources\Examinations($Examinations),
        ], 200);


    }

    /**
     * Update the specified resource in storage.
     */
    //تعديل ////////////////////////////////////
    //اضافة فحوصات للسجل الطبي
    public function addexamination(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;

        $medicalrecord = RecordMedical::find($id);
        if (!$medicalrecord) {
            return response()->json([
                'message' => 'Medical record not found.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Laboratory,Radiology',
            'image_path' =>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'exam_date' => 'nullable|date',
            'summary' => 'required|string',
        ]);
        if($request->has('image_path')) {
            $imagePath = $request->file('image_path')->store('Examination_images', 'public');
        }

        $Examinations= Examinations::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'image_path' => $imagePath,
            'summary' => $data['diagnosed_at'] ?? null,
            'exam_date' => $data['exam_date'],
           'doctor_id' => $doctor_id,
            'record_medical_id'=>$id
        ]);

        return response()->json([
            'message' => 'Examinations  added successfully.',
            'data' => new \App\Http\Resources\Examinations($Examinations),
        ], 200);


    }

    /**
     * Remove the specified resource from storage.
     */
    //عرض فحوصات المريض
    public function showExaminations(string $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;

        $medicalrecord = RecordMedical::find($id);
        if (!$medicalrecord) {
            return response()->json([
                'message' => 'Medical record not found.',
                'data' => [],
            ], 404);
        }

        $Examinations = $medicalrecord->Examinationss;
        if (empty($Examinations)) {
            return response()->json([
                'message' => 'Examinations found for this medical record.',
                'data' => [],
            ], 204);
        }
foreach ($Examinations as $Examination ){
    if($Examination->is_private == false || $Examination->doctor_id == $doctor_id ){
        return response()->json([
            'message' => 'Examination  retrieved successfully.',
            'data' =>\App\Http\Resources\Examinations::collection($Examinations),
        ], 200);

    }
}
        return response()->json([
            'message' => 'These test results are private and can only be viewed by the doctor who added them.'
            ,           'data' =>\App\Http\Resources\Examinations::collection($Examinations),
        ], 403);

    }

    //تعديل فحوصات المريض
    public function editExaminations(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;


        $Examinations= Examinations::where('id', $id)->first();

        if (!$Examinations) {
            return response()->json([
                'message' => 'The Examinations you want to edit does not exist.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Laboratory,Radiology',
            'image_path' =>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'exam_date' => 'nullable|date',
            'summary' => 'required|string',
        ]);

        if ($Examinations->doctor_id == $doctor_id) {
            $Examinations->update($data);

            return response()->json([
                'message' => '$Examinations updated successfully.',
                'data' => new \App\Http\Resources\Examinations($Examinations),
            ], 200);
        }
        return response()->json([
            'message' => 'These test results are private and can only be viewed by the doctor who added them.',
            'data' => new \App\Http\Resources\Examinations($Examinations),
        ], 403);
    }

    ////////////////////////
    //اضافة حساسية للمريض
    public function addallergies(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;

        $medicalrecord = RecordMedical::find($id);
        if (!$medicalrecord) {
            return response()->json([
                'message' => 'Medical record not found.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'allergy_type' => 'required|string|max:255',
            'allergen' => 'required|in:مخبري,شعاعي',
            'reaction_description' => 'nullable|string|max:255',
            'severity' => 'date',
            'start_date' => 'required|nullable|string',
            'notes' => 'required|nullable|string',
            'is_private' => 'required|nullable|string',
        ]);
        $pastDisease = PastDiseasesTable::create([
            'allergy_type' => $data['allergy_type'],
            'allergen' => $data['allergen'],
            'reaction_description' => $data['reaction_description'] ?? null,
            'severity' => $data['severity'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_private' => $data['is_private'] ?? null,
            'doctor_id' => $doctor_id,
        ]);
        $medicalrecord->PastDiseasesTable()->attach($pastDisease->id);

        return response()->json([
            'message' => 'Past disease added successfully.',
            'data' => new \App\Http\Resources\PastDiseasesTable($pastDisease),
        ], 200);
    }
    //عرض حساسية مريض
    public function showallergies(string $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;

        $medicalrecord = RecordMedical::find($id);
        if (!$medicalrecord) {
            return response()->json([
                'message' => 'Medical record not found.',
                'data' => [],
            ], 404);
        }

        $Examinations = $medicalrecord->allergies;


        if (empty($Examinations)) {
            return response()->json([
                'message' => 'No past allergies found for this medical record.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'message' => ' allergies retrieved successfully.',
            'data' =>\App\Http\Resources\Examinations::collection($Examinations),
        ], 200);
    }


    //تعديل حساسية مريض
    public function editallergies(Request $request, $id)
    {
        $allergies= allergies::where('id', $id)->first();
        if (!$allergies) {
            return response()->json([
                'message' => 'The allergies you want to edit does not exist.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'allergy_type' => 'required|string|max:255',
            'allergen' => 'required|in:مخبري,شعاعي',
            'reaction_description' => 'nullable|string|max:255',
            'severity' => 'date',
            'start_date' => 'required|nullable|string',
            'notes' => 'required|nullable|string',
            'is_private' => 'required|nullable|string',


        ]);

        $allergies->update($data);

        return response()->json([
            'message' => 'allergies updated successfully.',
            'data' => new \App\Http\Resources\PastDiseasesTable($allergies),
        ], 200);
    }

    ////////////////////////////
    //عرض ادوية المريض
    //اضافة دواء لمريض
    //تعديل دواء مريض
    //عرض عمليات جراحية لمريض
    //تعديل عمليات جراحية لمريض
    //اضافة عملية جراحية لمريض
}
