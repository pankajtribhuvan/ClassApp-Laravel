<?php

namespace App\Http\Controllers\Api\StudentApp;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\SyllabusUpdate;
use Illuminate\Http\Request;

class SyllabusController extends Controller
{
    /**
     * Get syllabus for logged-in student
     */
    public function index(Request $request)
    {
        $student = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Get student's assigned batches
        |--------------------------------------------------------------------------
        */

        $batches = $student->batches()
            ->where('batches.status', 'active')
            ->get([
                'batches.uuid',
                'batches.batch_name',
                'batches.course_name',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Build syllabus response
        |--------------------------------------------------------------------------
        */

        $data = $batches->map(function ($batch) {

            $syllabus = SyllabusUpdate::where(
                'batch_uuid',
                $batch->uuid
            )
                ->orderBy('step_no')
                ->get();

            return [
                'batch' => [
                    'uuid' => $batch->uuid,
                    'batch_name' => $batch->batch_name,
                    'course_name' => $batch->course_name,
                ],

                'syllabus' => $syllabus,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}