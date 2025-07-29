<?php

namespace App\Http\Controllers;

use App\Http\Resources\appointment;
use App\Models\appointments;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class appointmentAdmin extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**    /**
     * Show the form for creating a new resource.
     */
    //عرض حجوزات كلية للمرضى
    public function indexAppoitmentsList()
    {
        $AppoitmentsList =appointments::all();
        if($AppoitmentsList->isEmpty()){
            return response()->json([
                'message' => 'لا يوجد حجوزات في التطبيق.',
                'data' => [],
            ], 404);

        }

        return response()->json([
            'message' => 'success.',
            'data' => appointment::collection($AppoitmentsList),
        ], 200);
    }


    public function create()
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
