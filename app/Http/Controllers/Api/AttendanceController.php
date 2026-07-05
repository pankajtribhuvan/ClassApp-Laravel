<?php



namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class AttendanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SAVE OR UPDATE ATTENDANCE
    |--------------------------------------------------------------------------
    */

    public function saveAttendance(Request $request)
    {
        $validated = $request->validate([

            'batch_uuid' => [
                'required',
                'uuid',
                'exists:batches,uuid',
            ],

            'attendance_date' => [
                'required',
                'date_format:Y-m-d',
            ],

            'present_student_uuids' => [
                'required',
                'array',
            ],

            'present_student_uuids.*' => [
                'uuid',
                'exists:students,uuid',
            ],

            'marked_by_type' => [
                'required',
                'in:admin,teacher',
            ],

            'marked_by_uuid' => [
                'nullable',
                'uuid',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Find existing attendance
        |--------------------------------------------------------------------------
        */

        $attendance = Attendance::where(
            'batch_uuid',
            $validated['batch_uuid']
        )
        ->where(
            'attendance_date',
            $validated['attendance_date']
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | Update existing attendance
        |--------------------------------------------------------------------------
        */

        if ($attendance) {

            $attendance->update([

                'present_student_uuids' =>
                    $validated['present_student_uuids'],

                'marked_by_type' =>
                    $validated['marked_by_type'],

                'marked_by_uuid' =>
                    $validated['marked_by_uuid'] ?? null,
            ]);


            return response()->json([

                'success' => true,

                'message' =>
                    'Attendance updated successfully',

                'data' => $attendance->fresh(),

            ], 200);
        }


        /*
        |--------------------------------------------------------------------------
        | Create new attendance
        |--------------------------------------------------------------------------
        */

        $attendance = Attendance::create([

            'uuid' => Str::uuid(),

            'batch_uuid' =>
                $validated['batch_uuid'],

            'attendance_date' =>
                $validated['attendance_date'],

            'present_student_uuids' =>
                $validated['present_student_uuids'],

            'marked_by_type' =>
                $validated['marked_by_type'],

            'marked_by_uuid' =>
                $validated['marked_by_uuid'] ?? null,
        ]);


        return response()->json([

            'success' => true,

            'message' =>
                'Attendance saved successfully',

            'data' => $attendance,

        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | GET ATTENDANCE BY BATCH + DATE
    |--------------------------------------------------------------------------
    */

    public function getAttendance(
        Request $request
    ) {

        $validated = $request->validate([

            'batch_uuid' => [
                'required',
                'uuid',
            ],

            'attendance_date' => [
                'required',
                'date_format:Y-m-d',
            ],
        ]);


        $attendance = Attendance::where(
            'batch_uuid',
            $validated['batch_uuid']
        )
        ->where(
            'attendance_date',
            $validated['attendance_date']
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | No attendance yet
        |--------------------------------------------------------------------------
        */

        if (!$attendance) {

            return response()->json([

                'success' => true,

                'exists' => false,

                'message' =>
                    'Attendance not marked for this date',

                'data' => null,

            ], 200);
        }


        /*
        |--------------------------------------------------------------------------
        | Existing attendance
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'exists' => true,

            'data' => $attendance,

        ], 200);
    }


    /*
|--------------------------------------------------------------------------
| ATTENDANCE CALENDAR
|--------------------------------------------------------------------------
|
| Example:
| GET /api/attendance/calendar
|     ?batch_uuid=...
|     &year=2026
|     &month=7
|
*/

public function calendar(Request $request)
{
    $validated = $request->validate([

        'batch_uuid' => [
            'required',
            'uuid',
            'exists:batches,uuid',
        ],

        'year' => [
            'required',
            'integer',
            'min:2024',
            'max:2100',
        ],

        'month' => [
            'required',
            'integer',
            'min:1',
            'max:12',
        ],
    ]);


    /*
    |--------------------------------------------------------------------------
    | Total students currently assigned to batch
    |--------------------------------------------------------------------------
    */

    // $totalStudents = \DB::table('batch_students')
    $totalStudents = DB::table('batch_students')
        ->where(
            'batch_uuid',
            $validated['batch_uuid']
        )
        ->count();


    /*
    |--------------------------------------------------------------------------
    | Get monthly attendance records
    |--------------------------------------------------------------------------
    */

    $attendances = Attendance::where(
            'batch_uuid',
            $validated['batch_uuid']
        )
        ->whereYear(
            'attendance_date',
            $validated['year']
        )
        ->whereMonth(
            'attendance_date',
            $validated['month']
        )
        ->orderBy(
            'attendance_date',
            'asc'
        )
        ->get();


    /*
    |--------------------------------------------------------------------------
    | Prepare calendar data
    |--------------------------------------------------------------------------
    */

    $calendarData = $attendances->map(
        function ($attendance) use ($totalStudents) {

            $presentStudents =
                $attendance->present_student_uuids ?? [];

            $presentCount =
                count($presentStudents);

            $absentCount =
                max(
                    0,
                    $totalStudents - $presentCount
                );


            return [

                'attendance_uuid' =>
                    $attendance->uuid,

                'date' =>
                    $attendance
                        ->attendance_date
                        ->format('Y-m-d'),

                'present_count' =>
                    $presentCount,

                'absent_count' =>
                    $absentCount,

                'total_students' =>
                    $totalStudents,

                'marked_by_type' =>
                    $attendance->marked_by_type,

                'marked_by_uuid' =>
                    $attendance->marked_by_uuid,
            ];
        }
    );


    return response()->json([

        'success' => true,

        'batch_uuid' =>
            $validated['batch_uuid'],

        'year' =>
            (int) $validated['year'],

        'month' =>
            (int) $validated['month'],

        'total_students' =>
            $totalStudents,

        'attendance_days' =>
            $calendarData->count(),

        'data' =>
            $calendarData,

    ], 200);
}

/*
|--------------------------------------------------------------------------
| ATTENDANCE REPORT
|--------------------------------------------------------------------------
|
| GET /api/attendance/report
|
| Params:
| batch_uuid
| from_date
| to_date
|
*/

public function report(Request $request)
{
    $validated = $request->validate([

        'batch_uuid' => [
            'required',
            'uuid',
            'exists:batches,uuid',
        ],

        'from_date' => [
            'required',
            'date_format:Y-m-d',
        ],

        'to_date' => [
            'required',
            'date_format:Y-m-d',
            'after_or_equal:from_date',
        ],
    ]);


    /*
    |--------------------------------------------------------------------------
    | Get students assigned to batch
    |--------------------------------------------------------------------------
    */

    $students = DB::table('batch_students')

        ->join(
            'students',
            'batch_students.student_uuid',
            '=',
            'students.uuid'
        )

        ->where(
            'batch_students.batch_uuid',
            $validated['batch_uuid']
        )

        ->select(
            'students.uuid',
            'students.full_name',
            'students.admission_no',
            'students.mobile'
        )

        ->orderBy(
            'students.full_name'
        )

        ->get();


    /*
    |--------------------------------------------------------------------------
    | Get attendance records in date range
    |--------------------------------------------------------------------------
    */

    $attendanceRecords = Attendance::where(
            'batch_uuid',
            $validated['batch_uuid']
        )

        ->whereBetween(
            'attendance_date',
            [
                $validated['from_date'],
                $validated['to_date'],
            ]
        )

        ->orderBy(
            'attendance_date'
        )

        ->get();


    /*
    |--------------------------------------------------------------------------
    | Total attendance days
    |--------------------------------------------------------------------------
    */

    $totalAttendanceDays =
        $attendanceRecords->count();


    /*
    |--------------------------------------------------------------------------
    | Student-wise report
    |--------------------------------------------------------------------------
    */

    $studentReports = $students->map(
        function ($student) use (
            $attendanceRecords,
            $totalAttendanceDays
        ) {

            $presentDays = 0;


            foreach ($attendanceRecords as $attendance) {

                $presentUuids =
                    $attendance->present_student_uuids ?? [];


                if (
                    in_array(
                        $student->uuid,
                        $presentUuids
                    )
                ) {

                    $presentDays++;
                }
            }


            $absentDays =
                $totalAttendanceDays -
                $presentDays;


            $percentage =
                $totalAttendanceDays > 0

                    ? round(
                        (
                            $presentDays /
                            $totalAttendanceDays
                        ) * 100,
                        2
                    )

                    : 0;


            return [

                'student_uuid' =>
                    $student->uuid,

                'full_name' =>
                    $student->full_name,

                'admission_no' =>
                    $student->admission_no,

                'mobile' =>
                    $student->mobile,

                'present_days' =>
                    $presentDays,

                'absent_days' =>
                    $absentDays,

                'total_days' =>
                    $totalAttendanceDays,

                'percentage' =>
                    $percentage,
            ];
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Overall class calculation
    |--------------------------------------------------------------------------
    */

    $totalPossibleAttendances =
        $students->count() *
        $totalAttendanceDays;


    $totalPresentAttendances =
        $studentReports->sum(
            'present_days'
        );


    $classAverage =
        $totalPossibleAttendances > 0

            ? round(
                (
                    $totalPresentAttendances /
                    $totalPossibleAttendances
                ) * 100,
                2
            )

            : 0;


    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'success' => true,


        'batch_uuid' =>
            $validated['batch_uuid'],


        'from_date' =>
            $validated['from_date'],


        'to_date' =>
            $validated['to_date'],


        'summary' => [

            'total_students' =>
                $students->count(),

            'total_attendance_days' =>
                $totalAttendanceDays,

            'total_present_entries' =>
                $totalPresentAttendances,

            'class_average_percentage' =>
                $classAverage,
        ],


        'students' =>
            $studentReports->values(),

    ], 200);
}


public function studentCalendar(Request $request)
{
    $validated = $request->validate([
        'batch_uuid' => [
            'required',
            'uuid',
            'exists:batches,uuid',
        ],

        'student_uuid' => [
            'required',
            'uuid',
            'exists:students,uuid',
        ],

        'year' => [
            'required',
            'integer',
        ],

        'month' => [
            'required',
            'integer',
            'between:1,12',
        ],
    ]);


    /*
    |--------------------------------------------------------------------------
    | CHECK STUDENT IS ASSIGNED TO BATCH
    |--------------------------------------------------------------------------
    */

    $isAssigned = DB::table('batch_students')
        ->where(
            'batch_uuid',
            $validated['batch_uuid']
        )
        ->where(
            'student_uuid',
            $validated['student_uuid']
        )
        ->exists();


    if (!$isAssigned) {

        return response()->json([
            'success' => false,
            'message' => 'Student is not assigned to this batch.',
        ], 422);
    }


    /*
    |--------------------------------------------------------------------------
    | GET STUDENT
    |--------------------------------------------------------------------------
    */

    $student = DB::table('students')
        ->where(
            'uuid',
            $validated['student_uuid']
        )
        ->select(
            'uuid',
            'full_name',
            'admission_no',
            'mobile'
        )
        ->first();


    /*
    |--------------------------------------------------------------------------
    | GET MONTH ATTENDANCE
    |--------------------------------------------------------------------------
    */

    $attendanceRecords = Attendance::where(
            'batch_uuid',
            $validated['batch_uuid']
        )
        ->whereYear(
            'attendance_date',
            $validated['year']
        )
        ->whereMonth(
            'attendance_date',
            $validated['month']
        )
        ->orderBy(
            'attendance_date'
        )
        ->get();


    /*
    |--------------------------------------------------------------------------
    | BUILD DAILY STATUS
    |--------------------------------------------------------------------------
    */

    $attendanceData = $attendanceRecords->map(
        function ($attendance) use ($validated) {

            $presentStudentUuids =
                $attendance->present_student_uuids ?? [];


            $isPresent = in_array(
                $validated['student_uuid'],
                $presentStudentUuids
            );


            return [
                'attendance_uuid' =>
                    $attendance->uuid,

                'date' =>
                    $attendance
                        ->attendance_date
                        ->format('Y-m-d'),

                'status' =>
                    $isPresent ? 1 : 0,

                'marked_by_type' =>
                    $attendance->marked_by_type,

                'marked_by_uuid' =>
                    $attendance->marked_by_uuid,
            ];
        }
    );


    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    $totalDays =
        $attendanceData->count();


    $presentDays =
        $attendanceData
            ->where('status', 1)
            ->count();


    $absentDays =
        $attendanceData
            ->where('status', 0)
            ->count();


    $percentage =
        $totalDays > 0

            ? round(
                (
                    $presentDays /
                    $totalDays
                ) * 100,
                2
            )

            : 0;


    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'success' => true,


        'batch_uuid' =>
            $validated['batch_uuid'],


        'year' =>
            (int) $validated['year'],


        'month' =>
            (int) $validated['month'],


        'student' => [

            'uuid' =>
                $student->uuid,

            'full_name' =>
                $student->full_name,

            'admission_no' =>
                $student->admission_no,

            'mobile' =>
                $student->mobile,
        ],


        'summary' => [

            'present_days' =>
                $presentDays,

            'absent_days' =>
                $absentDays,

            'total_days' =>
                $totalDays,

            'percentage' =>
                $percentage,
        ],


        'attendance' =>
            $attendanceData->values(),

    ], 200);
}


public function monthlyRegister(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    $validated = $request->validate([
        'batch_uuid' => [
            'required',
            'uuid',
            'exists:batches,uuid',
        ],

        'year' => [
            'required',
            'integer',
        ],

        'month' => [
            'required',
            'integer',
            'between:1,12',
        ],
    ]);


    /*
    |--------------------------------------------------------------------------
    | GET BATCH DETAILS
    |--------------------------------------------------------------------------
    */

    $batch = DB::table('batches')
        ->leftJoin(
            'teachers',
            'batches.teacher_id',
            '=',
            'teachers.id'
        )
        ->where(
            'batches.uuid',
            $validated['batch_uuid']
        )
        ->select(
            'batches.uuid',
            'batches.batch_name',
            'batches.course_name',
            'batches.start_time',
            'batches.end_time',
            'batches.days',
            'batches.room_no',

            // 'teachers.name as teacher_name'
                    'teachers.full_name as teacher_name'

        )
        ->first();


    if (!$batch) {
        return response()->json([
            'success' => false,
            'message' => 'Batch not found.',
        ], 404);
    }


    /*
    |--------------------------------------------------------------------------
    | GET ASSIGNED STUDENTS
    |--------------------------------------------------------------------------
    */

    $students = DB::table('batch_students')
        ->join(
            'students',
            'batch_students.student_uuid',
            '=',
            'students.uuid'
        )
        ->where(
            'batch_students.batch_uuid',
            $validated['batch_uuid']
        )
        ->select(
            'students.uuid',
            'students.full_name',
            'students.admission_no',
            'students.mobile'
        )
        ->orderBy(
            'students.full_name'
        )
        ->get();


    /*
    |--------------------------------------------------------------------------
    | GET MONTH ATTENDANCE RECORDS
    |--------------------------------------------------------------------------
    */

    $attendanceRecords = Attendance::where(
            'batch_uuid',
            $validated['batch_uuid']
        )
        ->whereYear(
            'attendance_date',
            $validated['year']
        )
        ->whereMonth(
            'attendance_date',
            $validated['month']
        )
        ->orderBy(
            'attendance_date'
        )
        ->get();


    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE DATES
    |--------------------------------------------------------------------------
    */

    $attendanceDates = $attendanceRecords
        ->map(function ($attendance) {
            return $attendance
                ->attendance_date
                ->format('Y-m-d');
        })
        ->values();


    /*
    |--------------------------------------------------------------------------
    | BUILD STUDENT DAILY STATUS
    |--------------------------------------------------------------------------
    |
    | 1 = Present
    | 0 = Absent
    |
    | Dates where attendance was not taken will NOT exist in daily_status.
    | Flutter PDF will display "-" for those dates.
    |
    */

    $studentData = $students->map(
        function ($student) use ($attendanceRecords) {

            $dailyStatus = [];


            foreach ($attendanceRecords as $attendance) {

                $date = $attendance
                    ->attendance_date
                    ->format('Y-m-d');


                $presentStudentUuids =
                    $attendance->present_student_uuids ?? [];


                $dailyStatus[$date] = in_array(
                    $student->uuid,
                    $presentStudentUuids
                )
                    ? 1
                    : 0;
            }


            /*
            |--------------------------------------------------------------------------
            | SUMMARY
            |--------------------------------------------------------------------------
            */

            $presentDays = collect(
                $dailyStatus
            )
                ->filter(
                    fn ($status) => $status == 1
                )
                ->count();


            $absentDays = collect(
                $dailyStatus
            )
                ->filter(
                    fn ($status) => $status == 0
                )
                ->count();


            $totalDays =
                count($dailyStatus);


            $percentage =
                $totalDays > 0
                    ? round(
                        ($presentDays / $totalDays) * 100,
                        2
                    )
                    : 0;


            return [
                'student_uuid' =>
                    $student->uuid,

                'admission_no' =>
                    $student->admission_no,

                'full_name' =>
                    $student->full_name,

                'mobile' =>
                    $student->mobile,


                'present_days' =>
                    $presentDays,

                'absent_days' =>
                    $absentDays,

                'total_days' =>
                    $totalDays,

                'percentage' =>
                    $percentage,


                'daily_status' =>
                    $dailyStatus,
            ];
        }
    );


    /*
    |--------------------------------------------------------------------------
    | NUMBER OF DAYS IN REAL CALENDAR MONTH
    |--------------------------------------------------------------------------
    */

    $daysInMonth = Carbon::create(
        $validated['year'],
        $validated['month'],
        1
    )->daysInMonth;


    /*
    |--------------------------------------------------------------------------
    | BUILD REAL CALENDAR DAYS
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | {
    |   "date": "2026-07-01",
    |   "day": 1,
    |   "weekday": "We"
    | }
    |
    */

    $calendarDays = [];


    for ($day = 1; $day <= $daysInMonth; $day++) {

        $date = Carbon::create(
            $validated['year'],
            $validated['month'],
            $day
        );


        $calendarDays[] = [
            'date' =>
                $date->format('Y-m-d'),

            'day' =>
                $day,

            'weekday' =>
                $date->format('D'),

            'weekday_short' =>
                substr(
                    $date->format('D'),
                    0,
                    2
                ),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'success' => true,


        /*
        |--------------------------------------------------------------------------
        | BATCH HEADER
        |--------------------------------------------------------------------------
        */

        'batch' => [

            'uuid' =>
                $batch->uuid,

            'batch_name' =>
                $batch->batch_name,

            'course_name' =>
                $batch->course_name,

            'teacher_name' =>
                $batch->teacher_name,

            'start_time' =>
                $batch->start_time,

            'end_time' =>
                $batch->end_time,

            'days' =>
                $batch->days,

            'room_no' =>
                $batch->room_no,
        ],


        /*
        |--------------------------------------------------------------------------
        | MONTH DETAILS
        |--------------------------------------------------------------------------
        */

        'year' =>
            (int) $validated['year'],

        'month' =>
            (int) $validated['month'],

        'month_name' =>
            Carbon::create(
                $validated['year'],
                $validated['month'],
                1
            )->format('F'),

        'days_in_month' =>
            $daysInMonth,


        /*
        |--------------------------------------------------------------------------
        | REAL CALENDAR HEADER
        |--------------------------------------------------------------------------
        */

        'calendar_days' =>
            $calendarDays,


        /*
        |--------------------------------------------------------------------------
        | DATES WHERE ATTENDANCE EXISTS
        |--------------------------------------------------------------------------
        */

        'attendance_dates' =>
            $attendanceDates,


        /*
        |--------------------------------------------------------------------------
        | COUNTS
        |--------------------------------------------------------------------------
        */

        'summary' => [

            'total_students' =>
                $students->count(),

            'attendance_days' =>
                $attendanceRecords->count(),
        ],


        /*
        |--------------------------------------------------------------------------
        | STUDENT MATRIX
        |--------------------------------------------------------------------------
        */

        'students' =>
            $studentData->values(),

    ], 200);
}
}