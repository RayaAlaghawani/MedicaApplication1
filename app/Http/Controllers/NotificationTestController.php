<?php

namespace App\Http\Controllers;

use App\Http\Resources\notifications;
use App\Models\doctor;
use App\Models\Notification as NotificationModel;
use App\Models\Patient;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

use App\Models\notification;
use Illuminate\Http\Request;

class NotificationTestController extends Controller
{
//عرض اشعارات
// Show notifications
    public function showNotification(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized: User not logged in.'
            ], 401);
        }

        $doctor = doctor::find($user->id);

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor not found.',
                'data' => []
            ], 404);
        }

        $notifications = $doctor->Notifiables;

        if ($notifications->isEmpty()) {
            return response()->json([
                'message' => 'No notifications found.',
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Notifications retrieved successfully.',
            'data' => notifications::collection($notifications)
        ], 200);
    }

    public function destroyNotifiables($id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized: User not logged in.'
            ], 401);
        }

        $notification = notification::find($id);

        if (!$notification || $notification->notifiable_id->$user->id) {
            return response()->json([
                'message' => ' the notification not found او ليس لديك صلاحية لحذف اشعار.',
                'data' => []
            ], 404);
        }
        $notification->delete();
        return response()->json([
            'message' => 'تم حذف الاشعار بنجاح.',
        ], 200);
    }

    public function sends(Request $request)
    {
        $patient = Patient::find(1);

        if (!$patient || !$patient->fcm_token) {
            return response()->json(['success' => false, 'error' => 'Patient not found or no FCM token']);
        }

        $title = $request->input('title');
        $message = $request->input('message');
        $type = $request->input('type', 'basic');

        $serviceAccountPath = storage_path('app/awa-v2-8636d2ae5593.json');

        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $messaging = $factory->createMessaging();

        $notification = [
            'title' => $title,
            'body' => $message,
            'sound' => 'default',
        ];

        $data = [
            'type' => $type,
            'id' => $patient->id,
            'message' => $message,
        ];

        $cloudMessage = CloudMessage::withTarget('token', $patient->fcm_token)
            ->withNotification($notification)
            ->withData($data);

        try {
            $messaging->send($cloudMessage);

            notification::query()->create([
                'type' => 'App\Notifications\UserFollow',
                'notifiable_type' => 'Patient',
                'notifiable_id' => $patient->id,
                'data' => json_encode([
                    'user' => $patient->first_name . ' ' . $patient->last_name,
                    'message' => $message,
                    'title' => $title,
                ]),
            ]);

            return response()->json(['success' => true]);
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
