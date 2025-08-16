<?php

namespace App\Http\Controllers;

use App\Http\Resources\appointment;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\patientResource;
use App\Http\Resources\SecretaryResource;
use App\Models\appointments;
use App\Models\article;
use App\Models\doctor;
use App\Models\Patient;
use App\Models\secretary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class statistics extends Controller
{
//appointment_date
    public function reservations_chart()
    {
        // قائمة الأشهر (1-12)
        $months = collect(range(1, 12));

        // جلب البيانات من قاعدة البيانات (الحجوزات المؤكدة فقط)
        $data = appointments::select(
            DB::raw('COUNT(*) as count'),
            DB::raw('MONTH(created_at) as month')
        )
            ->whereYear('created_at', date('Y'))
            ->where('status', 'confirmed')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('count', 'month') // المفتاح هو الشهر
            ->all();

        // إنشاء مصفوفة 12 شهر مع وضع 0 إذا لم توجد بيانات
        $confirmedData = $months->map(fn($m) => $data[$m] ?? 0)->toArray();

        // أسماء الأشهر
        $labels = $months->map(fn($m) => date('F', mktime(0, 0, 0, $m, 1)))->toArray();

        // إخراج البيانات
        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Confirmed',
                    'data' => $confirmedData,
                   // 'borderColor' => '#27ae60',
                 //   'backgroundColor' => 'rgba(39, 174, 96, 0.2)',
                   // 'fill' => true,
                   // 'tension' => 0.3
                ],
            ],
        ]);
    }
    /**
     * إحصائية أفضل 3 مستخدمين حسب عدد المنشورات
     */
    public function users_chart()
    {
        $users = User::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->take(3)
            ->pluck('posts_count', 'name');

        return response()->json([
            'labels' => $users->keys()->toArray(),
            'datasets' => [
                'name' => 'Top Users',
                'values' => $users->values()->toArray()
            ]
        ]);
    }


    //احصائيات اسبوعية عن عدد الحجوزات المكتملة
    public function reservations_chart_weekly()
    {
        // أيام الأسبوع (السبت = 0 حسب Carbon، إذا أردت تعديل البداية إلى الأحد يمكن التغيير)
        $daysOfWeek = ['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];

        // بداية ونهاية الأسبوع الحالي
        $startOfWeek = now()->startOfWeek(); // يبدأ الأحد حسب إعدادات Carbon
        $endOfWeek   = now()->endOfWeek();   // ينتهي السبت

        // دالة مساعدة لجلب البيانات لأي حالة
        $getDailyData = function ($status) use ($startOfWeek, $endOfWeek, $daysOfWeek) {
            return collect($daysOfWeek)->map(function ($day) use ($status, $startOfWeek, $endOfWeek) {
                $date = $startOfWeek->copy()->next($day); // الحصول على اليوم المحدد
                return appointments::where('status', $status)
                    ->whereDate('created_at', $date)
                    ->count();
            })->toArray();
        };

        // جلب البيانات لكل حالة
        $confirmedData = $getDailyData('confirmed');

        // إخراج البيانات
        return response()->json([
            'labels' => $daysOfWeek,
            'datasets' => [
                [
                    'label' => 'Confirmed',
                    'data' => $confirmedData,
                ],
            ]
        ]);
    }
    public function reservations_chart_yearly()
    {
        // جلب جميع السنوات التي تحتوي على حجوزات
        $years = appointments::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->toArray();

        // دالة مساعدة لجلب البيانات لكل حالة
        $getYearlyData = function ($status) use ($years) {
            return collect($years)->map(function ($year) use ($status) {
                return appointments::whereYear('created_at', $year)
                    ->where('status', $status)
                    ->count();
            })->toArray();
        };

        // جلب البيانات لكل حالة
        $confirmedData = $getYearlyData('confirmed');
       // $pendingData   = $getYearlyData('pending');
      //  $cancelledData = $getYearlyData('cancelled');

        // حساب إجمالي الحجوزات لكل سنة
        $totalData = collect($years)->map(function($year) {
            return appointments::whereYear('created_at', $year)->count();
        })->toArray();

        // إخراج البيانات بصيغة JSON
        return response()->json([
            'labels' => $years,
            'datasets' => [
                [
                    'label' => 'Confirmed',
                    'data' => $confirmedData,
                ],
            ]
        ]);
    }

    public function showcount_reservation()
    {
        // إجمالي عدد الحجوزات
        $total_reservations = appointments::count();

        // عدد الحجوزات المؤكدة
        $count_reservation = appointments::where('status', 'Confirmed')->count();

        // حساب النسبة المئوية
        $percentage = $total_reservations > 0
            ? ($count_reservation / $total_reservations) * 100
            : 0;

        return response()->json([
            'message' => 'success.',
            'count' => $count_reservation,
           // 'total' => $total_reservations,
            'percentage' => round($percentage, 2), // تقريبها لرقمين عشريين
        ], 200);
    }
//العدد الكلي للمرضى ومؤشر ارتفاع التطبيق
    public function countPatients()
    {
        // إجمالي عدد المرضى
        $total_patients = Patient::count();

        // عدد المرضى الجدد هذا الأسبوع
        $new_patients_this_week = Patient::whereDate('created_at', '>=', now()->startOfWeek())->count();

        // عدد المرضى الجدد الأسبوع الماضي
        $new_patients_last_week = Patient::whereBetween('created_at', [
            now()->subWeek()->startOfWeek(),
            now()->subWeek()->endOfWeek()
        ])->count();

        // حساب نسبة النمو
        if ($new_patients_last_week == 0 && $new_patients_this_week > 0) {
            $growth_rate = "100%";
        } elseif ($new_patients_last_week == 0 && $new_patients_this_week == 0) {
            $growth_rate = "0%";
        } else {
            $rate = (($new_patients_this_week - $new_patients_last_week) / $new_patients_last_week) * 100;
            $growth_rate = round($rate, 2) . "%"; // إظهار النسبة مع علامة %
        }

        return response()->json([
            'message' => 'success.',
            'total_count' => $total_patients,
            'new_this_week' => $new_patients_this_week,
            'growth_rate' => $growth_rate
        ], 200);
    }

    public function countDoctors()
    {
        // العدد الكلي للأطباء
        $total_doctors = Doctor::count();

        // عدد الأطباء الجدد هذا الأسبوع
        $new_doctors_this_week = Doctor::whereDate('created_at', '>=', now()->startOfWeek())->count();

        // حساب النسبة الحقيقية
        $percentage = $total_doctors > 0
            ? round(($new_doctors_this_week / $total_doctors) * 100, 2)
            : 0;

        return response()->json([
            'message' => 'success.',
            'total_count' => $total_doctors,
            'percentage' => $percentage . '%'
        ], 200);
    }
// جلب اخر 5 حجوزات مؤكدة
    public function showLastAppointments()
    {
        $appointments = appointments::where('status', 'Confirmed')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        if ($appointments->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد معلومات.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'message' => 'success.',
            'data' => appointment::collection($appointments),
        ], 200);
    }

// جلب اخر 5 مرضى
    public function showLastPatients()
    {
        $patients = Patient::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        if ($patients->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد معلومات.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'message' => 'success.',
            'data' =>
                patientResource::collection($patients),
        ], 200);
    }

// جلب اخر 5 اطباء
    public function showLastDoctors()
    {
        $doctors = Doctor::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        if ($doctors->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد معلومات.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'message' => 'success.',
            'data' =>DoctorResource::collection($doctors) ,
        ], 200);
    }

// جلب اخر 5 سكرتيرات
    public function showLastSecretaries()
    {
        $secretaries = Secretary::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        if ($secretaries->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد معلومات.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'message' => 'success.',
            'data' => SecretaryResource::collection($secretaries),
        ], 200);
    }

// جلب اخر 5 مقالات
    public function showLastArticles()
    {
        $articles = Article::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        if ($articles->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد معلومات.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'message' => 'success.',
            'data' =>\App\Http\Resources\article::collection($articles) ,
        ], 200);
    }


}
