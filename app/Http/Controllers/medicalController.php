<?php

namespace App\Http\Controllers;

use App\Http\Resources\MedicalRecordResource;
use App\Http\Resources\MedicationResources;
use App\Http\Resources\SecretaryResource;
use App\Models\allergies;
use App\Models\doctor;
use App\Models\examination;
use App\Models\Examinations;
use App\Models\medications;
use App\Models\PastDiseasesTable;
use App\Models\Patient;
use App\Models\record_medical_past_disease;
use App\Models\RecordMedical;
use App\Models\surgical_procedures;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
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
///////////////////////////////////////////////////////////
//اضافة دواء
    public function Addmedication(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()
                ->json(['message' => 'Unauthorized: User not logged in.'], 401);
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
        ]);
        $medications= medications::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'doctor_id' => $doctor_id,
        ]);
        $medicalrecord->Medications()->attach($medications->id);
        return response()->json([
            'message' => 'Medication  added successfully.',
            'data' => new MedicationResources($medications),
        ], 201);}
        ////////////////////////////////////////////////////////////////

    //عرض ادوية المريض
    public function showMedications($id)
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
        $Medications = $medicalrecord->Medications;
        $data = [];

        foreach ($Medications as $Medication) {
            if ($Medication->type === 'public' || $Medication->doctor_id == $doctor_id) {
                $data[] = [
                    'id'=>$Medication->id,
                    'name' => $Medication->name,
                ];
            }
        }
        if (empty($data)) {
            return response()->json([
                'message' => 'No Medication found for this medical record.',
                'data' => [],
            ], 204);
        }

        return response()->json([
            'message' => 'Medication  retrieved successfully.',
            'data' => $data,
        ], 200);
    }

    //تعديل دواء مريض
    public function editMedication(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;


        $medications= medications::where('id', $id)->first();

        if (!$medications) {
            return response()->json([
                'message' => 'The medication you want to edit does not exist.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'name' => 'string|max:255',
            'type' => 'in:public,private',
        ]);
        if ($medications->doctor_id == $doctor_id) {
            $medications->update($data);

            return response()->json([
                'message' => 'medications updated successfully.',
                'data' => new MedicationResources($medications),
            ], 200);
        }
        return response()->json([
            'message' => 'You cannot edit the details of this medications because you are not the one who added it.',
            'data' => new \App\Http\Resources\MedicationResources($medications),
        ], 403);
    }

////////////////////////////////////////////فحوصات
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

        $Examinations= examination::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'image_path' => $imagePath,
            'summary' => $data['summary'] ?? null,
            'exam_date' => Carbon::today()->toDateString(),
           'doctor_id' => $doctor_id,
            'record_medical_id'=>$medicalrecord->id
        ]);

        return response()->json([
            'message' => 'Examinations  added successfully.',
            'data' => new \App\Http\Resources\Examinations($Examinations),
        ], 200);


    }

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
    if($Examination->type == "public" || $Examination->doctor_id == $doctor_id ){
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


        $Examinations= examination::where('id', $id)->first();

        if (!$Examinations) {
            return response()->json([
                'message' => 'The Examinations you want to edit does not exist.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:Laboratory,Radiology',
            'image_path' =>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'exam_date' => 'nullable|date',
            'summary' => 'nullable|string',
        ]);

        if ($Examinations->doctor_id == $doctor_id) {
            $Examinations->update($data);

            return response()->json([
                'message' => 'Examinations updated successfully.',
                'data' => new \App\Http\Resources\Examinations($Examinations),
            ], 200);
        }
        return response()->json([
            'message' => 'These test results are private and can only be viewed by the doctor who added them.',
            'data' => new \App\Http\Resources\Examinations($Examinations),
        ], 403);
    }

    ///////////////////////////////////////////////////////////////////////////
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
                'allergy_type'      => 'required|string|max:255',
                'allergen'          => 'required|string|max:255',
                'severity'          => 'required|in:mild,moderate,severe',
                'is_private' => 'required|boolean',
        ]);
        $allergies= allergies::create([
            'allergy_type' => $data['allergy_type'],
            'allergen' => $data['allergen'],
            'severity' => $data['severity'] ?? null,
            'is_private' => $data['is_private'] ?? null,
            'doctor_id' => $doctor_id,
            'record_medical_id'=>$medicalrecord->id

        ]);

        return response()->json([
            'message' => 'allergies  added successfully.',
            'data' => new \App\Http\Resources\allergies($allergies),
        ], 200);
    }
    //////////////////////////عرض حساسية مريض
    public function showallergies( $id)
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

        $allergies = $medicalrecord->allergies;


        $allowedAllergies = [];
        foreach ($allergies as $allergie) {
            if (!$allergie->is_private || $allergie->doctor_id == $doctor_id) {
                $allowedAllergies[] = $allergie;
            }
        }

        if (empty($allowedAllergies)) {
            return response()->json([
                'message' => 'These allergies are private and can only be viewed by the doctor who added them.',
                'data' => null,
            ], 403);
        }

        return response()->json([
            'message' => 'Allergies retrieved successfully.',
            'data' => \App\Http\Resources\allergies::collection(collect($allowedAllergies)),
        ], 200);
    }
    //تعديل حساسية مريض//////////////////////////
    public function editallergies(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;

        $allergies= allergies::where('id', $id)->first();
        if (!$allergies) {
            return response()->json([
                'message' => 'The allergies you want to edit does not exist.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'allergy_type'      => 'string|max:255',
            'allergen'          => 'string|max:255',
            'severity'          => 'string|in:mild,moderate,severe',
            'is_private'        => 'string|in:true,false',
        ]);

if ($allergies->doctor_id == $doctor_id) {
$allergies->update($data);

    return response()->json([
        'message' => 'allergies updated successfully.',
        'data' => new \App\Http\Resources\allergies($allergies),
    ], 200);
}
return response()->json([
    'message' => 'These allergie  are private and can only be viewed by the doctor who added them.',
    'data' => null,
], 403);
}

    /////////////////////////////////////////////////////عمليات جراحية
    //اضافة عملية جراحية لمريض
    public function addsurgical_procedure(Request $request, $id)
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
            'name'      => 'required|string|max:255',
            'type'          => 'required|string|in:public,private',
        ]);
        $surgical_procedures= surgical_procedures::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'doctor_id' => $doctor_id,
            'procedure_date' => Carbon::today()->toDateString(),


        ]);
        $medicalrecord->surgical_proceduress()->attach($surgical_procedures->id);
        return response()->json([
            'message' => 'surgical_procedure  added successfully.',
            'data' => new \App\Http\Resources\allergies($surgical_procedures),
        ], 200);
    }
    //عرض عمليات جراحية لمريض
    public function showsurgical_procedures( $id)
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

        $surgical_procedures = $medicalrecord->surgical_proceduress;
        $allowedsurgical_procedure = [];
        foreach ($surgical_procedures as $surgical_procedure) {
            if ($surgical_procedure->type=="public" ||
                $surgical_procedure->doctor_id == $doctor_id) {
                $allowedsurgical_procedure[] = $surgical_procedure;
            }
        }
        if (empty($allowedsurgical_procedure)) {
            return response()->json([
                'message' => 'These surgical_procedure are private and can only be viewed by the doctor who added them.',
                'data' => null,
            ], 403);
        }

        return response()->json([
            'message' => 'surgical_procedure retrieved successfully.',
            'data' => \App\Http\Resources\surgical_procedures::collection(collect($allowedsurgical_procedure)),
        ], 200);
    }


    //تعديل عمليات جراحية لمريض
    public function editsurgical_procedure(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not logged in.'], 401);
        }
        $doctor_id = $user->id;

        $surgical_procedures= surgical_procedures::where('id', $id)->first();
        if (!$surgical_procedures) {
            return response()->json([
                'message' => 'The surgical_procedure you want to edit does not exist.',
                'data' => [],
            ], 404);
        }

        $data = $request->validate([
            'allergy_type'      => 'string|max:255',
            'allergen'          => 'string|max:255',
            'severity'          => 'string|in:mild,moderate,severe',
            'is_private'        => 'string|in:true,false',
        ]);


if ($surgical_procedures->doctor_id == $doctor_id) {
$surgical_procedures->update($data);

    return response()->json([
        'message' => 'surgical_procedures updated successfully.',
        'data' => new \App\Http\Resources\surgical_procedures($surgical_procedures),
    ], 200);
}
return response()->json([
    'message' => 'These surgical_procedure  is private and can only be viewed by the doctor who added them.',
    'data' => null,
], 403);
}


}
