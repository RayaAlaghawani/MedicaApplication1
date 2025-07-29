<?php

namespace App\Http\Controllers;

use App\Http\Resources\patientResource;
use App\Models\appointments;
use App\Models\doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use function PHPUnit\Framework\isEmpty;

class patientList extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //عرض المرضى///
    public function showAllPatient()
    {
        $doctors = patient::all();

        if ($doctors->isEmpty()) {
            return response()->json([
                'message' => 'لا يوجد مرضى مسجلين في التطبيق.',
                'data' => [],
            ], 404);
        }
        return response()->json([
            'message' => 'تم جلب كل لمرضى الموجودين بالتطبيق.',
            'data' => patientResource::collection($doctors),
        ], 200);
    }

    //////بحث عن مريض
    public function searchforPatient(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|string|in:Male,Female',
        ]);
        $name = $request->name;
        $age = $request->age;
        $gendar = $request->gendar;
        $query = patient::query();
        if($name){
            $query->where('name','like','%'.$name.'%')  ;
        }
        if($age){
            $query->where('age',$age)  ;
        }
        if($gendar){
            $query->where('gendar',$gendar)  ;
        }

        $patients = $query->get();
        if($patients->isEmpty()){
            return response()->json([
                'message' => 'لم يتم العثور على أي مرضى بالمواصفات المطلوبة.',
                'data' => [],
            ], 404);


        }
        return response()->json([
            'message' => 'تم جلب المرضى بنجاح.',
            'data' => patientResource::collection($patients),
        ], 200);
    }


    /**
     * Show the form for creating a new resource.
     */
// حظر مريض مع إدخال سبب الحظر من قبل المدير
    public function banPatient(Request $request, $id)
    {
        $ReasonFortheban = $request->ReasonFortheban;
        if (empty($ReasonFortheban)) {
            return response()->json([
                'message' => 'يرجى إدخال سبب الحظر.',
            ], 400);
        }
        $patient_id = patient::findOrFail($id);

        if ($patient_id->is_banned) {
            return response()->json([
                'message' => 'المريض قد تم حظره بالفعل.',
            ], 400);
        }$patient_id->is_banned = true;
        $patient_id->save();
        // إرسال إشعار للمريض(

        return response()->json([
            'message' => 'تم حظر المريض بنجاح.',
            'ban_reason' => $ReasonFortheban, //
        ], 200);
    }

    /**=
     * Store a newly created resource in storage.
     */
// رفع حظر عن مريض مع إدخال سبب رفع الحظر
    public function Unban(Request $request, $id)
    {
        $unbanReason = $request->unbanReason;
        if (empty($unbanReason)) {
            return response()->json([
                'message' => 'يرجى إدخال سبب رفع الحظر.',
            ], 400);
        }

        $patient = patient::findOrFail($id);

        if (!$patient->is_banned) {
            return response()->json([
                'message' => 'المريض غير محظور ليتم إزالة الحظر عنه.',
            ], 400);
        }

        $patient->is_banned = false;
        $patient->save();
//ارسال اشعار للمريض
        return response()->json([
            'message' => 'تم إلغاء حظر المريض بنجاح.',
            'unban_reason' => $unbanReason,
        ], 200);
    }
    /**
     * Display the specified resource.
     */
    //عرض المرضى المحظورين
    public function showunbanedPatient()
    {

$patient=Patient::where('is_banned',true)->get();
if(!$patient){
        return response()->json([
            'message' => 'لا يوجد مرضى محظورين حاليا.',
        ], 404);
    }


        return response()->json([
            'message' => 'هذه قائمة المرضى المحظورين.',
            'data'=>patientResource::collection($patient)
        ], 200);


    }
    //عرض حجوزات كلية للمرضى
    public function indexAppoitmentsList()
    {
        $AppoitmentsLis =appointments::all();

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }
}
