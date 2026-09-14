<?php

namespace App\Http\Controllers\Api\AdminApp;

use App\Http\Controllers\Controller;
use App\Models\ExamResult;
use App\Models\Student;
use App\Models\Batch;
use App\Models\Teacher;

class ExamResultController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET ALL EXAM RESULTS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $examResults = ExamResult::latest('exam_date')
            ->latest('created_at')
            ->get();

        $data = $examResults->map(function ($exam) {

            /*
            |--------------------------------------------------------------------------
            | BATCH
            |--------------------------------------------------------------------------
            */

            $batch = Batch::where(
                'uuid',
                $exam->batch_uuid
            )->first();

            /*
            |--------------------------------------------------------------------------
            | TEACHER
            |--------------------------------------------------------------------------
            */

            $teacher = null;

            if ($batch && $batch->teacher_id) {
                $teacher = Teacher::find(
                    $batch->teacher_id
                );
            }

            /*
            |--------------------------------------------------------------------------
            | STUDENT COUNT
            |--------------------------------------------------------------------------
            */

            $studentCount = is_array($exam->results)
                ? count($exam->results)
                : 0;

            return [
                'uuid' => $exam->uuid,

                'exam_name' => $exam->exam_name,

                'exam_date' => $exam->exam_date
                    ? $exam->exam_date->format('Y-m-d')
                    : null,

                'duration' => $exam->duration,

                'out_of' => $exam->out_of,

                'teacher_name' => $teacher
                    ? $teacher->full_name
                    : null,

                'batch_name' => $batch
                    ? $batch->batch_name
                    : null,

                'student_count' => $studentCount,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GET SINGLE EXAM RESULT
    |--------------------------------------------------------------------------
    */

    public function show($uuid)
    {
        $exam = ExamResult::where(
            'uuid',
            $uuid
        )->first();

        if (!$exam) {
            return response()->json([
                'success' => false,
                'message' => 'Exam result not found.',
            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | BATCH
        |--------------------------------------------------------------------------
        */

        $batch = Batch::where(
            'uuid',
            $exam->batch_uuid
        )->first();


        /*
        |--------------------------------------------------------------------------
        | TEACHER
        |--------------------------------------------------------------------------
        */

        $teacher = null;

        if ($batch && $batch->teacher_id) {
            $teacher = Teacher::find(
                $batch->teacher_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | STUDENT RESULTS
        |--------------------------------------------------------------------------
        */

        $results = [];

        if (is_array($exam->results)) {

            foreach ($exam->results as $result) {

                $student = Student::where(
                    'uuid',
                    $result['student_uuid'] ?? null
                )->first();

                $results[] = [
                    'student_uuid' =>
                        $result['student_uuid'] ?? null,

                    'student_name' =>
                        $student
                            ? $student->full_name
                            : null,

                    'marks' =>
                        $result['marks'] ?? null,

                    'teacher_suggestion' =>
                        $result['suggestion'] ?? null,
                ];
            }
        }


        return response()->json([
            'success' => true,

            'data' => [
                'uuid' => $exam->uuid,

                'exam_name' => $exam->exam_name,

                'exam_date' => $exam->exam_date
                    ? $exam->exam_date->format('Y-m-d')
                    : null,

                'duration' => $exam->duration,

                'out_of' => $exam->out_of,

                'teacher_name' => $teacher
                    ? $teacher->full_name
                    : null,

                'batch_name' => $batch
                    ? $batch->batch_name
                    : null,

                'results' => $results,
            ],
        ]);
    }
}