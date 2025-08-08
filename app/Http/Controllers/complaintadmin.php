<?php

namespace App\Http\Controllers;
use App\Http\Resources\complaints;
use App\Models\Complaint;
use Illuminate\Http\Request;

class complaintadmin extends Controller
{
    // عرض الشكاوي قيد الانتظار
    public function showallcomplaintPendingcomplaints()
    {
        $complaints = Complaint::where('status', 'Pending')->orderBy('created_at')->orderBy('id')->get();

        if ($complaints->isEmpty()) {
            return response()->json([
                'message' => 'No pending complaints found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Pending complaints retrieved successfully.',
            'data' => complaints::collection($complaints),
        ], 200);
    }

    // عرض الشكاوى المقبولة
    public function showallAcceptedcomplaints()
    {
        $complaints = Complaint::where('status', 'Accepted')->get();

        if ($complaints->isEmpty()) {
            return response()->json([
                'message' => 'No accepted complaints found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Accepted complaints retrieved successfully.',
            'data' => complaints::collection($complaints)
        ], 200);
    }

    // عرض الشكاوى المرفوضة
    public function showallRejectedcomplaints()
    {
        $complaints = Complaint::where('status', 'Rejected')->get();

        if ($complaints->isEmpty()) {
            return response()->json([
                'message' => 'No rejected complaints found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Rejected complaints retrieved successfully.',
            'data' => complaints::collection($complaints)
        ], 200);
    }

    // تغيير حالة الشكوى إلى مقبولة
    public function changStatusComplaint(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_response' => 'required|string',
        ]);

        $complaints = Complaint::find($id);

        if (!$complaints) {
            return response()->json([
                'message' => 'Complaint not found.',
                'data' => null,
            ], 404);
        }

        $complaints->status = 'Accepted';
        $complaints->admin_response=$validated['admin_response'];
        $complaints->save();

        return response()->json([
            'message' => 'Complaint status updated to accepted successfully.',
            'data' => new \App\Http\Resources\complaints($complaints),
        ], 200);
    }

    // تغيير حالة الشكوى إلى مرفوضة
    public function changStatusComplaints(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_response' => 'required|string',
        ]);

        $complaints = Complaint::find($id);

        if (!$complaints) {
            return response()->json([
                'message' => 'Complaint not found.',
                'data' => null,
            ], 404);
        }

        $complaints->status = 'Rejected';
        $complaints->admin_response=$validated['admin_response'];
        $complaints->save();


        return response()->json([
            'message' => 'Complaint status updated to rejected successfully.',
            'data' => new \App\Http\Resources\complaints($complaints),
        ], 200);
    }
}
