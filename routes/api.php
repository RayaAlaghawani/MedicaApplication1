<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\authadmincontroller;
use App\Http\Controllers\authdoctorcontroller;
use App\Http\Controllers\joinRequests;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\weekly_schedules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
////Route Patient
Route::prefix('patient')->group(function () {

    Route::post('register', [PatientController::class, 'register']);
    Route::post('login', [PatientController::class, 'login']);
    Route::post('/verify-email/{id}', [PatientController::class, 'verifyEmail']);
    Route::post('forgot-password', [PatientController::class, 'sendResetCode']);
    Route::post('reset-password/{id}', [PatientController::class, 'resetPassword']);
    Route::middleware(['auth:api-patient'])->group(function() {

        Route::get('showprofile', [PatientController::class, 'showProfile']);
        Route::post('update-profile', [PatientController::class, 'updateProfile']);
        Route::post('logout', [PatientController::class, 'logout']);

        Route::post('medical-record/child', [PatientController::class, 'storeChildMedicalRecord']);
        Route::post('medical-record/adult', [PatientController::class, 'storeAdultMedicalRecord']);

        Route::get('specializations', [PatientController::class, 'getAvailableSpecializations']);
        Route::post('addspecialization', [PatientController::class, 'addspecialization']);
      //  Route::get('doctors-by-specialization/{id}', [PatientController::class, 'getDoctorsBySpecialization']);

////////////////////الحجوزات
        Route::get('getAllSpecializations', [AppointmentController::class, 'getAllSpecializations']);
        Route::get('specializations/{id}/doctors', [AppointmentController::class, 'getDoctorsBySpecialization']);
        Route::get('doctor/{id}', [AppointmentController::class, 'getDoctorById']);
        Route::get('available-slots/{doctor_id}/{day_of_week}', [AppointmentController::class, 'getAvailableSlots']);

    });    });

////////////////////////////////////////////////////////////////////////////////////////////////////
/// Route Admin
Route::prefix('admin')->group(function() {
    Route::post('login_admin ', [authadmincontroller::class, 'login_admin']);

Route::middleware(['auth:admin,api-admin'])->group(function() {
    Route::post('logout_admin ', [authadmincontroller::class, 'logout_admin']);
    Route::get('getAllJoinRequests ', [joinRequests::class, 'getAllJoinRequests']);
    Route::post('approveJoinRequest/{id}', [joinRequests::class, 'approveJoinRequest']);
    Route::post('rejectJoinRequest/{id}', [joinRequests::class, 'rejectJoinRequest']);

});});

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//ٌRoute doctor
Route::prefix('doctor')->group(function() {
    Route::post('register', [authdoctorcontroller::class, 'register_doctor']);
    Route::post('verifyUser', [authdoctorcontroller::class, 'verify']);
    Route::post('login_user ', [authdoctorcontroller::class, 'login_user']);
Route::middleware(['auth:doctor,api-doctor'])->group(function() {
 //   Route::post('user/password/email', [resetPasswordcontroller::class, 'userForgetPassword']);
  //  Route::post('user/password/code/check', [resetPasswordcontroller::class, 'userCheckCode']);
  //  Route::post('user/password/reset', [resetPasswordcontroller::class, 'userResetPassword']);
    Route::post('logout_user ',[authdoctorcontroller::class,'logout_user']);
    /////////مواعيد طبيب///////////////////1
    Route::post('update/{id}',[weekly_schedules::class,'update']);
    Route::post('delete/{id}',[weekly_schedules::class,'delete']);
    Route::post('store',[weekly_schedules::class,'store']);
    Route::get('index',[weekly_schedules::class,'index']);
////////////////////////////////////////2
});});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

