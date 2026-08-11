<?php

namespace App\Http\Controllers\Api\TeacherApp;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    /**
     * Get all active batches assigned to logged-in teacher.
     */
    public function index(Request $request)
    {
        $teacher = $request->user();

        $batches = Batch::where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->with([
                'students' => function ($query) {
                    $query->where('students.status', 'active');
                }
            ])
            ->orderBy('start_time')
            ->get();

        $data = $batches->map(function ($batch) {

            $days = $batch->days
                ? array_values(
                    array_filter(
                        array_map('trim', explode(',', $batch->days))
                    )
                )
                : [];

            return [
                'uuid' => $batch->uuid,
                'batch_name' => $batch->batch_name,
                'course_name' => $batch->course_name,

                'days' => $days,

                'start_time' => $batch->start_time,
                'end_time' => $batch->end_time,

                'room_no' => $batch->room_no,

                'student_count' => $batch->students->count(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}