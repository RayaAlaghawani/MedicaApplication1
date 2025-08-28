<?php
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\authadmincontroller;
use App\Http\Controllers\authdoctorcontroller;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\joinRequests;
use App\Http\Controllers\NotificationTestController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\resetPasswordcontroller;
use App\Http\Controllers\SecretariasController;
use App\Http\Controllers\weekly_schedules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
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

////////////////////الحجوزات
///
        Route::post('searchDoctors', [AppointmentController::class, 'searchDoctorByName']);

        Route::get('getAllSpecializations', [AppointmentController::class, 'getAllSpecializations']);
        Route::get('getDoctorsBySpecialization/{id}', [AppointmentController::class, 'getDoctorsBySpecialization']);
        Route::get('doctor/{id}', [AppointmentController::class, 'getDoctorById']);
            //
        Route::get('show/{id}', [AppointmentController::class,'show']);

        // Route::get('available-slots/{doctor_id}/{day_of_week}', [AppointmentController::class, 'getAvailableSlots']);

        Route::post('appointments/available/{doctor_id}', [AppointmentController::class, 'availableSlots']);
        Route::post('AppointmentsBook/{doctor_id}', [AppointmentController::class, 'store']);

        Route::post('appointments/{appointment_id}', [AppointmentController::class, 'update']);
        Route::get('myAppointments', [AppointmentController::class, 'myAppointments']);
        Route::post('appointmentsCancel/{appointment_id}', [AppointmentController::class, 'cancelAppointment']);
        Route::get('doctorslatest', [AppointmentController::class, 'getLatestDoctors']);
        Route:: get('appointmentsnearest', [AppointmentController::class, 'getNearestAppointment']);

        /////////////////favourite
        Route::post('addfav/{doctor_id}', [FavouriteController::class, 'addToFavourite']);
        Route::delete('removefav/{doctor_id}', [FavouriteController::class, 'removeFromFavourite']);
        Route::get('getfav', [FavouriteController::class, 'getFavourite']);
///articles
        Route::get('indexallArticles', [\App\Http\Controllers\Articlecontroller::class, 'indexallArticles']);


        ////////Complaint
        Route::post('addComplaint', [ComplaintController::class, 'addComplaint']);
        Route::get('getComplaint', [ComplaintController::class, 'getComplaint']);

        /////المقالات
        Route::get('indexallArticles', [\App\Http\Controllers\Articlecontroller::class, 'indexallArticles']);
       // http://127.0.0.1:8000/api/patient/indexallArticles

    });    });

////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
/// Route Admin
Route::prefix('admin')->group(function() {
    Route::post('login_admin ', [authadmincontroller::class, 'login_admin']);
    Route::middleware(['auth:admin,api-admin'])->group(function() {
        Route::post('logout_admin ', [authadmincontroller::class, 'logout_admin']);
        Route::get('getAllJoinRequests ', [joinRequests::class, 'getAllJoinRequests']);
        Route::post('approveJoinRequest/{id}', [joinRequests::class, 'approveJoinRequest']);
        Route::post('rejectJoinRequest/{id}', [joinRequests::class, 'rejectJoinRequest']);
        //قسم اطباء
        Route::get('indexDoctors', [\App\Http\Controllers\doctorList::class, 'indexDoctors']);
        Route::post('search', [\App\Http\Controllers\doctorList::class, 'search']);
        Route::get('show/{id}', [\App\Http\Controllers\doctorList::class, 'show']);
        Route::post('store', [\App\Http\Controllers\doctorList::class, 'store']);
        Route::post('update/{id}', [\App\Http\Controllers\doctorList::class, 'update']);
        Route::get('indexAllSpecialization', [\App\Http\Controllers\doctorList::class, 'indexAllSpecialization']);
        Route::post('addInformation/{id}', [\App\Http\Controllers\doctorList::class, 'addInformation']);
//قسم سكرتاريا
        Route::get('indexallSecretary', [\App\Http\Controllers\secretariasController::class, 'indexallSecretary']);
//بحث
//قسم المرضى///
        Route::get('showAllPatient', [\App\Http\Controllers\patientList::class, 'showAllPatient']);
        Route::post('searchforPatient', [\App\Http\Controllers\patientList::class, 'searchforPatient']);
        Route::post('banPatient/{id}', [\App\Http\Controllers\patientList::class, 'banPatient']);
        Route::post('Unban/{id}', [\App\Http\Controllers\patientList::class, 'Unban']);
        Route::get('showunbanedPatient', [\App\Http\Controllers\patientList::class, 'showunbanedPatient']);
//قسم حجوزات
        Route::get('indexAppoitmentsList', [\App\Http\Controllers\appointmentAdmin::class, 'indexAppoitmentsList']);
//بحث
        //بجث عن حجز
        //قسم شكاوي
        Route::get('showallcomplaintPendingcomplaints', [\App\Http\Controllers\complaintadmin::class, 'showallcomplaintPendingcomplaints']);
        Route::get('showallAcceptedcomplaints', [\App\Http\Controllers\complaintadmin::class, 'showallAcceptedcomplaints']);

                Route::get('showallRejectedcomplaints', [\App\Http\Controllers\complaintadmin::class, 'showallRejectedcomplaints']);
        Route::post('changStatusComplaint/{id}', [\App\Http\Controllers\complaintadmin::class, 'changStatusComplaint']);
        Route::post('changStatusComplaints/{id}', [\App\Http\Controllers\complaintadmin::class, 'changStatusComplaints']);

        //احصائيات
        //عددحجوزات شهري
        Route::get('reservations_chart', [\App\Http\Controllers\statistics::class, 'reservations_chart']);
                Route::get('reservations_chart_weekly', [\App\Http\Controllers\statistics::class, 'reservations_chart_weekly']);
                        Route::get('reservations_chart_yearly', [\App\Http\Controllers\statistics::class, 'reservations_chart_yearly']);
                                Route::get('showcount_reservation', [\App\Http\Controllers\statistics::class, 'showcount_reservation']);
        Route::get('showcount_reservation', [\App\Http\Controllers\statistics::class, 'showcount_reservation']);
//
        Route::get('countPatients', [\App\Http\Controllers\statistics::class, 'countPatients']);
        Route::get('countDoctors', [\App\Http\Controllers\statistics::class, 'countDoctors']);

        Route::get('showLastAppointments', [\App\Http\Controllers\statistics::class, 'showLastAppointments']);
        Route::get('showLastpatients', [\App\Http\Controllers\statistics::class, 'showLastpatients']);
        Route::get('showLastdoctors', [\App\Http\Controllers\statistics::class, 'showLastdoctors']);
        Route::get('showLastsecretaries', [\App\Http\Controllers\statistics::class, 'showLastsecretaries']);
        Route::get('showLastArticles', [\App\Http\Controllers\statistics::class, 'showLastArticles']);

        //اشعارات
        Route::post('/sends', [NotificationTestController::class, 'sends']);


    });});

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//ٌRoute doctor

Route::prefix('doctor')->group(function() {
    Route::post('register', [authdoctorcontroller::class, 'register_doctor']);
    Route::post('verifyUser', [authdoctorcontroller::class, 'verify']);
    Route::post('login_user ', [authdoctorcontroller::class, 'login_user']);
    Route::post('user/password/email', [resetPasswordcontroller::class, 'userForgetPassword']);
    Route::post('user/password/code/check', [resetPasswordcontroller::class, 'userCheckCode']);
    Route::post('user/password/reset', [resetPasswordcontroller::class, 'userResetPassword']);

    Route::middleware(['auth:doctor,api-doctor'])->group(function() {
        Route::post('logout_user ',[authdoctorcontroller::class,'logout_user']);
        /////////مواعيد طبيب///////////////////1
        Route::post('updateWeekly_schedules/{id}',[weekly_schedules::class,'update']);
        Route::post('delete/{id}',[weekly_schedules::class,'delete']);
        Route::post('store',[weekly_schedules::class,'store']);
        Route::get('index',[weekly_schedules::class,'index']);
        //قسم حجوزات

        Route::get('indexAppoitmentsList', [\App\Http\Controllers\APPOINTMENTDOCTOR::class, 'indexAppoitmentsList']);
        Route::post('searchforPatient', [\App\Http\Controllers\APPOINTMENTDOCTOR::class, 'searchforPatient']);
//قسم مقالات
        Route::get('indexallArticle', [\App\Http\Controllers\Articlecontroller::class, 'indexallArticle']);
        Route::post('destroyArticle/{id}', [\App\Http\Controllers\Articlecontroller::class, 'destroyArticle']);
        Route::post('update/{id}', [\App\Http\Controllers\Articlecontroller::class, 'update']);
        Route::post('createArticle', [\App\Http\Controllers\Articlecontroller::class, 'createArticle']);
//قسم بروفايل
//حذف حساب
        Route::get('indexprofile', [\App\Http\Controllers\profileDoctor::class, 'indexprofile']);
        Route::post('update', [\App\Http\Controllers\profileDoctor::class, 'update']);
//قسم مواعيدي
        //ملاحظة اضافة مدلوير الحظر للمرضى
        //قسم اطباء الاخرين وتواصل معهم
        //قسم سكرتاراي
        Route::get('indexDoctors', [\App\Http\Controllers\doctorList::class, 'indexDoctors']);
        Route::post('searchForDoctor', [\App\Http\Controllers\doctorList::class, 'searchForDoctor']);


        //قسم راية سكرتاريا  هدى
        Route::post('addSecretarias', [SecretariasController::class, 'addSecretarias']);

        Route::get('indexallSecretaryForDoctor', [SecretariasController::class, 'indexallSecretaryForDoctor']);





        //قسم مرضى
/////قسم ادوية
        Route::post('Addmedication/{id}', [\App\Http\Controllers\medicalController::class, 'Addmedication']);
        Route::post('showMedications/{id}', [\App\Http\Controllers\medicalController::class, 'showMedications']);
        Route::post('editMedication/{id}', [\App\Http\Controllers\medicalController::class, 'editMedication']);
//////قسم عمليات جراحية
///
        Route::post('addsurgical_procedure/{id}', [\App\Http\Controllers\medicalController::class, 'addsurgical_procedure']);
        Route::post('showsurgical_procedures/{id}', [\App\Http\Controllers\medicalController::class, 'showsurgical_procedures']);
        Route::post('editsurgical_procedure/{id}', [\App\Http\Controllers\medicalController::class,
            'editsurgical_procedure']);

///قسم حساسيات
        Route::post('addallergies/{id}', [\App\Http\Controllers\medicalController::class, 'addallergies']);
        Route::post('showallergies/{id}', [\App\Http\Controllers\medicalController::class, 'showallergies']);
        Route::post('editallergies/{id}', [\App\Http\Controllers\medicalController::class,
            'editallergies']);


        ///قسم زيارات معاينات مرضى
        Route::post('Addmedical_visit/{id}', [\App\Http\Controllers\medical_visits::class, 'Addmedical_visit']);
        Route::post('showeMedical_visit/{id}', [\App\Http\Controllers\medical_visits::class, 'showeMedical_visit']);
        Route::post('editmedical_visit/{id}', [\App\Http\Controllers\medical_visits::class,
            'editmedical_visit']);
/////////////////////////
        Route::get('showPatient', [\App\Http\Controllers\Patientdoctor::class, 'showPatient']);
        Route::post('searchPatient', [\App\Http\Controllers\Patientdoctor::class, 'searchPatient']);
        Route::post('addPastDiseases/{id}', [\App\Http\Controllers\medicalController::class, 'addPastDiseases']);
//اضافة امراض للسجل الطبي
        Route::post('showPastDiseases/{id}', [\App\Http\Controllers\medicalController::class, 'showPastDiseases']);
        Route::post('showDetailILL/{id}', [\App\Http\Controllers\medicalController::class, 'showDetailILL']);
        Route::post('editPastDiseases/{id}', [\App\Http\Controllers\medicalController::class, 'editPastDiseases']);
//قسم فحوصات
        Route::post('addexamination/{id}', [\App\Http\Controllers\medicalController::class, 'addexamination']);
        Route::post('editExaminations/{id}', [\App\Http\Controllers\medicalController::class, 'editExaminations']);
        Route::post('showExaminations/{id}', [\App\Http\Controllers\medicalController::class, 'showExaminations']);
///////قسم اشعارات
                Route::get('showNotification', [\App\Http\Controllers\NotificationTestController::class, 'showNotification']);

                        Route::post('destroyNotifiable/{id}',
                            [\App\Http\Controllers\NotificationTestController::class, 'destroyNotifiables']);

///
////////////////////////////////////////2
    });});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////قسم سجل طبي
Route::post('showMedicalrecord/{id}', [\App\Http\Controllers\medicalController::class, 'showMedicalrecord']);
Route::post('updateMedicalRecord/{id}', [\App\Http\Controllers\medicalController::class, 'updateMedicalRecord']);



/// Route Secretary
Route::prefix('secretary')->group(function() {




    Route::post('loginSecretary', [SecretariasController::class, 'loginSecretary']);


    Route::middleware(['auth:api-secretary'])->group(function (){
       // logoutSecretary
        Route::post('logoutSecretary', [SecretariasController::class, 'logoutSecretary']);
        Route::get('getAllAppointments', [SecretariasController::class, 'allDoctorAppointments']);
        Route::post('appointmentsby-date', [SecretariasController::class, 'doctorAppointmentsByDate']);
       // getUpcomingAppointments
         Route::get('getUpcomingAppointments', [SecretariasController::class, 'getUpcomingAppointments']);


        Route::post('updateAppointmentStates/{id}', [SecretariasController::class, 'updateAppointmentStatus']);
    });

});



