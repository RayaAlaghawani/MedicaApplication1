<?php

namespace App\Http\Controllers;

use App\Mail\SendResetPassword;
use App\Models\doctor;
use App\Models\resetPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class resetPasswordcontroller extends Controller
{

    public function userForgetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:doctors'],
        ]);
        resetPassword::query()->where('email', $request['email'])->delete();
        $data['code'] = mt_rand(100000, 999999);
        $codeData = resetPassword::query()->create($data);
        Mail::to($request['email'])->send(new SendResetPassword($codeData['code']));
        return response()->json(['message' => ('code.sent')]);
    }

    public function userCheckCode(Request $request)
    {
        $request->validate([

            'code' => 'required|string|exists:reset_passwords',
        ]);
        $passwordReset = ResetPassword::query()->firstWhere('code', $request['code']);
        if ($passwordReset['created_at'] > now()->addHour()) {
            $passwordReset->delete();
            return response()->json(['message' => ('passwords.code.is_expire')], 422);
        }
        return response()->json([
            'code' => $passwordReset['code'],
            'message' => 'password.code.is_vaild',
        ]);
    }

    public function userResetPassword(Request $request)
    {
        $input = $request->validate([
            'code' => 'required|string|exists:reset_passwords',
            'password' => ['required', 'confirmed'],
        ]);
        $passwordReset = resetPassword::query()->firstWhere('code', $request['code']);
        if ($passwordReset['created_at'] > now()->addHour()) {
            $passwordReset->delete();
            return response()->json(['message' => 'passwords.code_is_expire'], 422);
        }
        $user = doctor::query()->firstWhere('email', $passwordReset['email']);
        $input['password'] = bcrypt($input['password']);
        $user->update([
            'password' => $input['password'],
        ]);
        $passwordReset->delete();
        return response()->json(['message' => 'password has been successfully reset']);
    }
////////////////////////////////////////////////////////////////////////************************************8888
///

}
