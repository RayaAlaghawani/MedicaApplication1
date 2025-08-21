<?php
use App\Http\controlles\NourController;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\support\facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/get-firebase-token', function () {
    // 1️⃣ مسار ملف JSON
    $jsonPath = storage_path('app/awa-v2-8636d2ae5593.json');

    // 2️⃣ تحديد scope المطلوب لإرسال إشعارات
    $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

    // 3️⃣ إنشاء credentials من الملف
    $credentials = new ServiceAccountCredentials($scopes, $jsonPath);

    // 4️⃣ الحصول على Access Token
    $token = $credentials->fetchAuthToken();

    return response()->json($token);
});















