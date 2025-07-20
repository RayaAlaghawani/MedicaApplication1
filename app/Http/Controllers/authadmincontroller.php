<?php

namespace App\Http\Controllers;

use App\Models\admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class authadmincontroller extends Controller
{
    public function login_admin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'exists:admins,email'],
            'password' => ['required']
        ]);
        $user = admin::query()->where('email', '=', $request['email'])->first();
        if (!Auth()->guard('admin')->attempt($request->only(['email', 'password']))) {

            return response()->json(['message' => ' Password is Wrong !.'], 401);
        }
        $token = $user->createToken('personal Access Token')->plainTextToken;
        $data = [];
        $data['user'] = $user;
        $data['token'] = $token;
        return response()->json(['token' => $token,
            'message' => 'welcome!!!'], 200);

    }

///logout
        public function logout_admin()
    {
        Auth::User()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'goodbuy!!!'], 200);

    }



}
