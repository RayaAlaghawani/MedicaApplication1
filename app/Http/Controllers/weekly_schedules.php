<?php

namespace App\Http\Controllers;

use App\Models\doctor_schedules;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class weekly_schedules extends Controller
{
    /**
     * عرض الجدول الأسبوعي للطبيب.
     */
    public function index()
    {
        $doctor_id = Auth::user()->id;

        $weekly_schedules = doctor_schedules::where('doctor_id', $doctor_id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $orderedDays = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];

        if ($weekly_schedules->isNotEmpty()) {
            $grouped = $weekly_schedules->groupBy(function ($item) {
                return $item->day_name;
            })->map(function ($items) {
                return $items->values();
            });

            $result = [];
            foreach ($orderedDays as $day) {
                $result[$day] = $grouped->get($day, collect());
            }

            return response()->json([
                'message' => 'تم استرجاع جدول المواعيد الأسبوعي بنجاح.',
                'data' => $result,
            ], 200);
        }

        $emptyResult = [];
        foreach ($orderedDays as $day) {
            $emptyResult[$day] = [];
        }

        return response()->json([
            'message' => 'لا يوجد عناصر لعرضها',
            'data' => $emptyResult,
        ], 200);
    }
    /**
     * إنشاء موعد جديد في الجدول الأسبوعي.
     */
    public function store(Request $request)
    {
        $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'required|integer|min:1',
        ]);

        $doctor_id = Auth::user()->id;

        $existingSchedules = doctor_schedules::where('doctor_id', $doctor_id)
            ->where('day_of_week', $request->day_of_week)
            ->get();

        foreach ($existingSchedules as $existingSchedule) {
            if (
                $request->start_time < $existingSchedule->end_time &&
                $request->end_time > $existingSchedule->start_time
            ) {
                return response()->json([
                    'message' => 'يوجد تعارض مع فترة أخرى في نفس اليوم.',
                    'data' => null,
                ], 409);
            }
        }

        $schedule = doctor_schedules::create([
            'doctor_id' => $doctor_id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'slot_duration' => $request->slot_duration,
        ]);

        return response()->json([
            'message' => 'تمت إضافة الموعد إلى الجدول الأسبوعي بنجاح.',
            'data' => $schedule,
        ], 201);
    }

    /**
     * تعديل موعد موجود في الجدول الأسبوعي.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'day_of_week'    => 'nullable|integer|min:0|max:6',
            'start_time'     => 'nullable|date_format:H:i',
            'end_time'       => 'nullable|date_format:H:i|after:start_time',
            'slot_duration'  => 'nullable|integer|min:1',
        ]);

        $doctor_id = Auth::user()->id;

        $schedule = doctor_schedules::where('id', $id)
            ->where('doctor_id', $doctor_id)
            ->first();

        if (!$schedule) {
            return response()->json([
                'message' => 'عذراً، لم يتم العثور على الموعد المطلوب.',
                'data' => null,
            ], 404);
        }

        $updateData = $request->only(['day_of_week', 'start_time', 'end_time', 'slot_duration']);

        if (empty($updateData)) {
            return response()->json([
                'message' => 'لم يتم إرسال أي بيانات للتعديل.',
                'data' => null,
            ], 400);
        }

        if ($request->filled('day_of_week') && $request->filled('start_time') && $request->filled('end_time')) {
            $conflicts = doctor_schedules::where('doctor_id', $doctor_id)
                ->where('day_of_week', $updateData['day_of_week'])
                ->where('id', '!=', $id)
                ->get();

            foreach ($conflicts as $existing) {
                if (
                    $updateData['start_time'] < $existing->end_time &&
                    $updateData['end_time'] > $existing->start_time
                ) {
                    return response()->json([
                        'message' => 'يوجد تعارض مع فترة أخرى في نفس اليوم.',
                        'data' => null,
                    ], 409);
                }
            }
        }

        $schedule->update($updateData);

        return response()->json([
            'message' => 'تم تعديل الموعد بنجاح.',
            'data' => $schedule,
        ], 200);
    }

    /**
     * حذف موعد من الجدول الأسبوعي.
     */
    public function delete($id)
    {
        $doctor_id = Auth::user()->id;

        $schedule = doctor_schedules::where('id', $id)
            ->where('doctor_id', $doctor_id)
            ->first();

        if (!$schedule) {
            return response()->json([
                'message' => 'عذراً، الموعد المطلوب غير موجود.',
                'data' => null,
            ], 404);
        }

        $schedule->delete();

        return response()->json([
            'message' => 'تم حذف الموعد من الجدول الأسبوعي بنجاح.',
            'data' => null,
        ], 200);
    }
}
