<?php

namespace App\Http\Controllers;

use App\Http\Resources\DoctorResource;
use App\Models\doctor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class profileDoctor extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexprofile()
    {
        $doctor_id=Auth::user()->id;
$doctorinfo=doctor::where('id',$doctor_id)->first();
        return response()->json([
            'message' => 'success.',
            'data' => new DoctorResource($doctorinfo),
        ], 200);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    //تعديل معلومات طبيب
    public function update(Request $request)
    {
        $doctor_id=Auth::user()->id;
        $doctorinfo=doctor::where('id',$doctor_id)->first();

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
        $doctorinfo->update($data);
        $doctorinfo->refresh();

        return response()->json([
            'message' => 'Doctor updated successfully',
            'data' => new DoctorResource($doctorinfo),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
