<?php

namespace App\Http\Controllers\Api\TeacherApp;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\SyllabusUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SyllabusController extends Controller
{
    /**
     * Get syllabus for teacher's batch
     */
    public function index(
        Request $request,
        string $batchUuid
    ) {
        $teacher = $request->user();

        $batch = Batch::where('uuid', $batchUuid)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found or not assigned to you.',
            ], 404);
        }

        $syllabus = SyllabusUpdate::where(
                'batch_uuid',
                $batchUuid
            )
            ->orderBy('step_no')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $syllabus,
        ]);
    }

    /**
     * Create syllabus item
     */
    public function store(
        Request $request,
        string $batchUuid
    ) {
        $teacher = $request->user();

        $batch = Batch::where('uuid', $batchUuid)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found or not assigned to you.',
            ], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'step_no' => 'required|integer|min:1',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'topics' => 'nullable|array',
                'topics.*' => 'string',
                'status' => 'nullable|in:planned,in_progress,completed',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $status = $request->input(
            'status',
            'planned'
        );

        $syllabus = SyllabusUpdate::create([
            'batch_uuid' => $batchUuid,
            'step_no' => $request->step_no,
            'title' => $request->title,
            'description' =>
                $request->description,
            'topics' =>
                $request->topics ?? [],
            'status' => $status,
            'completed_at' =>
                $status === 'completed'
                    ? now()
                    : null,
            'created_by_type' => 'teacher',
            'created_by_uuid' => $teacher->uuid,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Syllabus item created successfully.',
            'data' => $syllabus,
        ], 201);
    }

    /**
     * Update syllabus item
     */
    public function update(
        Request $request,
        string $batchUuid,
        string $uuid
    ) {
        $teacher = $request->user();

        $batch = Batch::where('uuid', $batchUuid)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found or not assigned to you.',
            ], 404);
        }

        $syllabus = SyllabusUpdate::where(
                'batch_uuid',
                $batchUuid
            )
            ->where('uuid', $uuid)
            ->first();

        if (!$syllabus) {
            return response()->json([
                'success' => false,
                'message' => 'Syllabus item not found.',
            ], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'step_no' => 'sometimes|integer|min:1',
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'topics' => 'nullable|array',
                'topics.*' => 'string',
                'status' => 'sometimes|in:planned,in_progress,completed',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $syllabus->fill(
            $request->only([
                'step_no',
                'title',
                'description',
                'topics',
                'status',
            ])
        );

        if (
            $request->has('status') &&
            $request->status === 'completed'
        ) {
            $syllabus->completed_at = now();
        }

        if (
            $request->has('status') &&
            $request->status !== 'completed'
        ) {
            $syllabus->completed_at = null;
        }

        $syllabus->save();

        return response()->json([
            'success' => true,
            'message' => 'Syllabus item updated successfully.',
            'data' => $syllabus,
        ]);
    }

    /**
     * Delete syllabus item
     */
    public function destroy(
        Request $request,
        string $batchUuid,
        string $uuid
    ) {
        $teacher = $request->user();

        $batch = Batch::where('uuid', $batchUuid)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found or not assigned to you.',
            ], 404);
        }

        $syllabus = SyllabusUpdate::where(
                'batch_uuid',
                $batchUuid
            )
            ->where('uuid', $uuid)
            ->first();

        if (!$syllabus) {
            return response()->json([
                'success' => false,
                'message' => 'Syllabus item not found.',
            ], 404);
        }

        $syllabus->delete();

        return response()->json([
            'success' => true,
            'message' => 'Syllabus item deleted successfully.',
        ]);
    }
}