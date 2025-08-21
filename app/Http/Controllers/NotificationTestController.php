<?php

namespace App\Http\Controllers;

use App\Models\Notification as NotificationModel;
use App\Models\Patient;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

use App\Models\notification;
use Illuminate\Http\Request;

class NotificationTestController extends Controller
{

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
///////////////
    public function send($title, $message, $type = 'basic')
    {
        // مسار ملف JSON
        $serviceAccountPath = storage_path('app/awa-v2-8636d2ae5593.json');

        // تهيئة Firebase
        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $messaging = $factory->createMessaging();

        // بيانات الإشعار
        $notification = [
            'title' => $title,
            'body' => $message,
            'sound' => 'default',
        ];

        // بيانات إضافية
        $data = [
            'type' => $type,
            'message' => $message,
        ];

        $cloudMessage = CloudMessage::withTarget('topic', 'test-topic')
            ->withNotification($notification)
            ->withData($data);

        try {
            $messaging->send($cloudMessage);

            notification::create([
                'type' => 'App\Notifications\UserFollow',
                'notifiable_type' => 'Patient',
                'notifiable_id' => 1,
                'data' => json_encode([
                    'user' => 'System',
                    'message' => $message,
                    'title' => $title,
                ]),
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

}
