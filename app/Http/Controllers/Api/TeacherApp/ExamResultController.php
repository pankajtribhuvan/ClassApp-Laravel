<?php

namespace App\Http\Controllers\Api\TeacherApp;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\ExamResult;
use Illuminate\Http\Request;

class ExamResultController extends Controller
{
    /**
     * List all exams created by logged-in teacher.
     */
    public function index(Request $request)
    {
        $teacher = $request->user();

        $examResults = ExamResult::where(
                'teacher_uuid',
                $teacher->uuid
            )
            ->orderByDesc('exam_date')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            // 'data' => $examResults,
            'data' => $examResults->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'uuid' => $exam->uuid,
                    'teacher_uuid' => $exam->teacher_uuid,
                    'batch_uuid' => $exam->batch_uuid,
                    'exam_name' => $exam->exam_name,
                    'exam_date' => $exam->exam_date->format('Y-m-d'),
                    'duration' => $exam->duration,
                    'out_of' => $exam->out_of,
                    'results' => $exam->results,
                    'created_at' => $exam->created_at,
                    'updated_at' => $exam->updated_at,
                ];
            }),
        ]);
    }

    /**
     * Get students of selected batch.
     */
    public function students(
        Request $request,
        string $batchUuid
    ) {
        $teacher = $request->user();

        $batch = Batch::where('uuid', $batchUuid)
            ->where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->firstOrFail();

        $students = $batch->students()
            ->where('students.status', 'active')
            ->orderBy('full_name')
            ->get([
                'students.uuid',
                'students.full_name',
            ]);

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    /**
     * Create one exam with all student results.
     */
    public function store(Request $request)
    {
        $teacher = $request->user();

        $validated = $request->validate([
            'batch_uuid' => [
                'required',
                'string',
            ],

            'exam_name' => [
                'required',
                'string',
                'max:255',
            ],

            'exam_date' => [
                'required',
                'date',
            ],

            'duration' => [
                'required',
                'integer',
                'min:1',
            ],

            'out_of' => [
                'required',
                'integer',
                'min:1',
            ],

            'results' => [
                'required',
                'array',
                'min:1',
            ],

            'results.*.student_uuid' => [
                'required',
                'string',
            ],

            'results.*.marks' => [
                'required',
                'numeric',
                'min:0',
            ],

            'results.*.teacher_suggestion' => [
                'nullable',
                'string',
            ],
        ]);

        // Make sure batch belongs to teacher.
        $batch = Batch::where(
                'uuid',
                $validated['batch_uuid']
            )
            ->where(
                'teacher_id',
                $teacher->id
            )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();

        // Get students actually belonging to batch.
        $batchStudentUuids = $batch
            ->students()
            ->where('students.status', 'active')
            ->pluck('students.uuid')
            ->toArray();

        // Make sure every submitted student
        // belongs to this batch.
        foreach ($validated['results'] as $result) {
            if (!in_array(
                $result['student_uuid'],
                $batchStudentUuids,
                true
            )) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'One or more students do not belong to the selected batch.',
                ], 422);
            }
        }

        $examResult = ExamResult::create([
            'teacher_uuid' => $teacher->uuid,
            'batch_uuid' => $validated['batch_uuid'],
            'exam_name' => $validated['exam_name'],
            'exam_date' => $validated['exam_date'],
            'duration' => $validated['duration'],
            'out_of' => $validated['out_of'],
            'results' => $validated['results'],
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Exam result saved successfully.',
            // 'data' => $examResult,
            'data' => [
            'id' => $examResult->id,
            'uuid' => $examResult->uuid,
            'teacher_uuid' => $examResult->teacher_uuid,
            'batch_uuid' => $examResult->batch_uuid,
            'exam_name' => $examResult->exam_name,
            'exam_date' => $examResult->exam_date->format('Y-m-d'),
            'duration' => $examResult->duration,
            'out_of' => $examResult->out_of,
            'results' => $examResult->results,
            'created_at' => $examResult->created_at,
            'updated_at' => $examResult->updated_at,
        ],
        ], 201);
    }

    /**
     * Show one exam.
     */
    public function show(
        Request $request,
        string $uuid
    ) {
        $teacher = $request->user();

        $examResult = ExamResult::where(
                'uuid',
                $uuid
            )
            ->where(
                'teacher_uuid',
                $teacher->uuid
            )
            ->firstOrFail();

        return response()->json([
            'success' => true,
            // 'data' => $examResult,
            'data' => [
                'id' => $examResult->id,
                'uuid' => $examResult->uuid,
                'teacher_uuid' => $examResult->teacher_uuid,
                'batch_uuid' => $examResult->batch_uuid,
                'exam_name' => $examResult->exam_name,
                'exam_date' => $examResult->exam_date->format('Y-m-d'),
                'duration' => $examResult->duration,
                'out_of' => $examResult->out_of,
                'results' => $examResult->results,
                'created_at' => $examResult->created_at,
                'updated_at' => $examResult->updated_at,
            ],
        ]);
    }

    /**
     * Update one complete exam.
     */
    public function update(
        Request $request,
        string $uuid
    ) {
        $teacher = $request->user();

        $examResult = ExamResult::where(
                'uuid',
                $uuid
            )
            ->where(
                'teacher_uuid',
                $teacher->uuid
            )
            ->firstOrFail();

        $validated = $request->validate([
            'batch_uuid' => [
                'required',
                'string',
            ],

            'exam_name' => [
                'required',
                'string',
                'max:255',
            ],

            'exam_date' => [
                'required',
                'date',
            ],

            'duration' => [
                'required',
                'integer',
                'min:1',
            ],

            'out_of' => [
                'required',
                'integer',
                'min:1',
            ],

            'results' => [
                'required',
                'array',
                'min:1',
            ],

            'results.*.student_uuid' => [
                'required',
                'string',
            ],

            'results.*.marks' => [
                'required',
                'numeric',
                'min:0',
            ],

            'results.*.teacher_suggestion' => [
                'nullable',
                'string',
            ],
        ]);

        // Make sure selected batch belongs to teacher.
        $batch = Batch::where(
                'uuid',
                $validated['batch_uuid']
            )
            ->where(
                'teacher_id',
                $teacher->id
            )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();

        $batchStudentUuids = $batch
            ->students()
            ->where('students.status', 'active')
            ->pluck('students.uuid')
            ->toArray();

        foreach ($validated['results'] as $result) {
            if (!in_array(
                $result['student_uuid'],
                $batchStudentUuids,
                true
            )) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'One or more students do not belong to the selected batch.',
                ], 422);
            }
        }

        $examResult->update([
            'batch_uuid' => $validated['batch_uuid'],
            'exam_name' => $validated['exam_name'],
            'exam_date' => $validated['exam_date'],
            'duration' => $validated['duration'],
            'out_of' => $validated['out_of'],
            'results' => $validated['results'],
        ]);

        $examResult = $examResult->fresh();


           return response()->json([
                'success' => true,
                'message' => 'Exam result updated successfully.',
                'data' => [
                    'id' => $examResult->id,
                    'uuid' => $examResult->uuid,
                    'teacher_uuid' => $examResult->teacher_uuid,
                    'batch_uuid' => $examResult->batch_uuid,
                    'exam_name' => $examResult->exam_name,
                    'exam_date' => $examResult->exam_date->format('Y-m-d'),
                    'duration' => $examResult->duration,
                    'out_of' => $examResult->out_of,
                    'results' => $examResult->results,
                    'created_at' => $examResult->created_at,
                    'updated_at' => $examResult->updated_at,
                ],
            ]);

    }

    /**
     * Delete one complete exam.
     */
    public function destroy(
        Request $request,
        string $uuid
    ) {
        $teacher = $request->user();

        $examResult = ExamResult::where(
                'uuid',
                $uuid
            )
            ->where(
                'teacher_uuid',
                $teacher->uuid
            )
            ->firstOrFail();

        $examResult->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'Exam result deleted successfully.',
        ]);
    }
}