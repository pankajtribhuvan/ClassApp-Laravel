<?php


namespace App\Http\Controllers\Api\TeacherApp;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceHistoryController extends Controller
{
    /**
     * Show attendance for one previous date.
     *
     * Teacher:
     * - assigned batch only
     * - previous attendance is READ ONLY
     */
    public function show(
        Request $request,
        string $batchUuid,
        string $attendanceUuid
    ) {
        $teacher = $request->user();

        $attendance = Attendance::where(
            'uuid',
            $attendanceUuid
        )->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found.',
            ], 404);
        }

        if ($attendance->batch_uuid !== $batchUuid) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Attendance does not belong to this batch.',
            ], 404);
        }
        // ------------------------------------------------------
        // Find batch
        // ------------------------------------------------------

        $batch = Batch::where(
            'uuid',
            $attendance->batch_uuid
        )->first();

        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found.',
            ], 404);
        }

        // ------------------------------------------------------
        // SECURITY
        // Teacher can only view assigned batches.
        // ------------------------------------------------------

        if (
            (int) $batch->teacher_id !==
            (int) $teacher->id
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'You are not assigned to this batch.',
            ], 403);
        }

        // ------------------------------------------------------
        // Attendance must be a previous date.
        // Today is handled by AttendancePage.
        // ------------------------------------------------------

        $today = now()->startOfDay();

        $attendanceDate =
            $attendance->attendance_date
                ->copy()
                ->startOfDay();

        if (
            $attendanceDate->greaterThanOrEqualTo(
                $today
            )
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Only previous attendance can be viewed here.',
            ], 422);
        }

        // ------------------------------------------------------
        // Assigned students
        // ------------------------------------------------------

        $students = DB::table('batch_students')
            ->join(
                'students',
                'batch_students.student_uuid',
                '=',
                'students.uuid'
            )
            ->where(
                'batch_students.batch_uuid',
                $batch->uuid
            )
            ->select(
                'students.uuid',
                'students.admission_no',
                'students.full_name',
                'students.mobile',
                'students.student_photo'
            )
            ->orderBy(
                'students.full_name'
            )
            ->get();

        // ------------------------------------------------------
        // Present student UUIDs
        // ------------------------------------------------------

        $presentStudentUuids =
            $attendance->present_student_uuids ?? [];

        // ------------------------------------------------------
        // Build student attendance
        // ------------------------------------------------------

        $studentAttendance =
            $students->map(
                function ($student) use (
                    $presentStudentUuids
                ) {
                    return [
                        'uuid' =>
                            $student->uuid,

                        'admission_no' =>
                            $student->admission_no,

                        'full_name' =>
                            $student->full_name,

                        'mobile' =>
                            $student->mobile,

                        'student_photo' =>
                            $student->student_photo,

                        'is_present' =>
                            in_array(
                                $student->uuid,
                                $presentStudentUuids
                            ),
                    ];
                }
            )->values();

        // ------------------------------------------------------
        // Summary
        // ------------------------------------------------------

        $totalStudents =
            $studentAttendance->count();

        $presentCount =
            $studentAttendance
                ->where(
                    'is_present',
                    true
                )
                ->count();

        $absentCount =
            $totalStudents -
            $presentCount;

        // ------------------------------------------------------
        // Response
        // ------------------------------------------------------

        return response()->json([
            'success' => true,

            'data' => [
                'batch' => [
                    'uuid' =>
                        $batch->uuid,

                    'batch_name' =>
                        $batch->batch_name,

                    'course_name' =>
                        $batch->course_name,
                ],

                'attendance' => [
                    'uuid' =>
                        $attendance->uuid,

                    'date' =>
                        $attendance
                            ->attendance_date
                            ->format('Y-m-d'),

                    'marked_by_type' =>
                        $attendance
                            ->marked_by_type,

                    'marked_by_uuid' =>
                        $attendance
                            ->marked_by_uuid,

                    'status' =>
                        'completed',

                    'read_only' =>
                        true,
                ],

                'summary' => [
                    'total_students' =>
                        $totalStudents,

                    'present_count' =>
                        $presentCount,

                    'absent_count' =>
                        $absentCount,
                ],

                'students' =>
                    $studentAttendance,
            ],
        ], 200);
    }
}