<?php

namespace App\Http\Controllers\Api\TeacherApp;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    /**
     * Get today's attendance screen for a teacher's batch.
     *
     * GET:
     * /api/v2/teacher/batches/{batchUuid}/attendance
     */
    public function show(Request $request, string $batchUuid)
    {
        $teacher = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Verify batch belongs to logged-in teacher
        |--------------------------------------------------------------------------
        */

        $batch = Batch::where('uuid', $batchUuid)
            ->where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->first();

        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found or you are not assigned to this batch.',
            ], 404);
        }

        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | Get active students of this batch
        |--------------------------------------------------------------------------
        */

        $students = $batch->students()
            ->where('students.status', 'active')
            ->orderBy('students.full_name')
            ->get([
                'students.uuid',
                'students.admission_no',
                'students.full_name',
                'students.mobile',
                'students.student_photo',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Check whether today's attendance already exists
        |--------------------------------------------------------------------------
        */

        $attendance = Attendance::where('batch_uuid', $batch->uuid)
            ->whereDate('attendance_date', $today)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Existing attendance
        |--------------------------------------------------------------------------
        */

        $presentStudentUuids = $attendance
            ? ($attendance->present_student_uuids ?? [])
            : [];

        return response()->json([
            'success' => true,

            'data' => [

                'batch' => [
                    'uuid' => $batch->uuid,
                    'batch_name' => $batch->batch_name,
                    'course_name' => $batch->course_name,
                ],

                'date' => $today->format('Y-m-d'),

                'attendance_status' => $attendance
                    ? 'completed'
                    : 'pending',

                'can_submit' => true,

                'students' => $students->map(function ($student) use (
                    $presentStudentUuids
                ) {
                    return [
                        'uuid' => $student->uuid,
                        'admission_no' => $student->admission_no,
                        'full_name' => $student->full_name,
                        'mobile' => $student->mobile,
                        'student_photo' => $student->student_photo,

                        'is_present' => in_array(
                            $student->uuid,
                            $presentStudentUuids,
                            true
                        ),
                    ];
                })->values(),

            ],
        ]);
    }

    /**
     * Submit today's attendance.
     *
     * POST:
     * /api/v2/teacher/batches/{batchUuid}/attendance
     */
    public function store(Request $request, string $batchUuid)
    {
        $teacher = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Verify batch belongs to logged-in teacher
        |--------------------------------------------------------------------------
        */

        $batch = Batch::where('uuid', $batchUuid)
            ->where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->first();

        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found or you are not assigned to this batch.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Today's date only
        |--------------------------------------------------------------------------
        */

        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | Check whether attendance is already submitted
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Teacher cannot edit or submit attendance again.
        |
        */


        /*
        |--------------------------------------------------------------------------
        | Validate request
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'present_student_uuids' => [
                'required',
                'array',
            ],

            'present_student_uuids.*' => [
                'uuid',
            ],
        ]);

        $presentStudentUuids = array_values(
            array_unique(
                $validated['present_student_uuids']
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Get only ACTIVE students belonging to this batch
        |--------------------------------------------------------------------------
        */

        $activeStudentUuids = $batch->students()
            ->where('students.status', 'active')
            ->pluck('students.uuid')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Make sure every submitted UUID belongs to this batch
        |--------------------------------------------------------------------------
        */

        $invalidStudentUuids = array_values(
            array_diff(
                $presentStudentUuids,
                $activeStudentUuids
            )
        );

        if (!empty($invalidStudentUuids)) {
            return response()->json([
                'success' => false,
                'message' => 'One or more students do not belong to this active batch.',
                'invalid_student_uuids' => $invalidStudentUuids,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Attendance
        |--------------------------------------------------------------------------
        */

        $existingAttendance = Attendance::where(
            'batch_uuid',
            $batch->uuid
        )
            ->whereDate(
                'attendance_date',
                $today
            )
            ->first();

        if ($existingAttendance) {

            // --------------------------------------------
            // UPDATE TODAY'S ATTENDANCE
            // --------------------------------------------

            $existingAttendance->update([
                'present_student_uuids' => $presentStudentUuids,
                'marked_by_type' => 'teacher',
                'marked_by_uuid' => $teacher->uuid,
            ]);

            $attendance = $existingAttendance;
            $action = 'updated';

        } else {

            // --------------------------------------------
            // CREATE TODAY'S ATTENDANCE
            // --------------------------------------------

            $attendance = Attendance::create([
                'uuid' => (string) Str::uuid(),

                'batch_uuid' => $batch->uuid,

                'attendance_date' => $today->format('Y-m-d'),

                'present_student_uuids' => $presentStudentUuids,

                'marked_by_type' => 'teacher',

                'marked_by_uuid' => $teacher->uuid,
            ]);

            $action = 'created';
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate counts
        |--------------------------------------------------------------------------
        */

        $totalStudents = count($activeStudentUuids);

        $presentCount = count($presentStudentUuids);

        $absentCount = max(
            0,
            $totalStudents - $presentCount
        );

        return response()->json([
            'success' => true,

            'message' => $action === 'created'
            ? 'Attendance submitted successfully.'
            : 'Attendance updated successfully.',

            'data' => [
                'attendance_uuid' => $attendance->uuid,

                'batch_uuid' => $batch->uuid,

                'attendance_date' => $today->format('Y-m-d'),

                'total_students' => $totalStudents,

                'present_count' => $presentCount,

                'absent_count' => $absentCount,

                'attendance_status' => 'completed',

                'can_submit' => true,
            ],
        ], $action === 'created' ? 201 : 200);
    }
}