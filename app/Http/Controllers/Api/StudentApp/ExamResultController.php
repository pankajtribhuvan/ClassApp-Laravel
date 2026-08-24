<?php

namespace App\Http\Controllers\Api\StudentApp;

use App\Http\Controllers\Controller;
use App\Models\ExamResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamResultController extends Controller
{
    /**
     * Get exam results of the authenticated student.
     */
    public function index(Request $request): JsonResponse
    {
        $studentUuid = $request->user()->uuid;

        $examResults = ExamResult::query()
            ->orderByDesc('exam_date')
            ->get();

        $results = $examResults
            ->map(function ($exam) use ($studentUuid) {

                $studentResult = collect(
                    $exam->results ?? []
                )->firstWhere(
                    'student_uuid',
                    $studentUuid
                );

                if (!$studentResult) {
                    return null;
                }

                return [
                    'uuid' => $exam->uuid,
                    'exam_name' => $exam->exam_name,
                    'exam_date' => $exam->exam_date?->format('Y-m-d'),
                    'duration' => $exam->duration,
                    'out_of' => $exam->out_of,
                    'marks' => $studentResult['marks'] ?? null,
                    'teacher_suggestion' =>
                        $studentResult['teacher_suggestion'] ?? null,
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }
}