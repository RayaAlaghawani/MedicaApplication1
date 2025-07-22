<?php

namespace App\Http\Controllers;

use App\Http\Resources\DoctorResource;
use App\Http\Resources\SecretaryResource;
use App\Models\secretary;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class secretarias extends Controller
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
