<?php

namespace App\Http\Controllers\WebAdmin;

use App\Http\Controllers\Controller;

use App\Jobs\ProcessVideoJob;
use App\Models\Video;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Video List
     */
    public function index()
    {
        $videos = Video::latest()->get();

        return view('Admin.videos.index', compact('videos'));
    }

    /**
     * Upload Form
     */
    public function create()
    {
        return view('admin.videos.create');
    }

    /**
     * Upload Video
     */
    public function store(Request $request, VideoService $videoService)
    {
        $request->validate([
            'course_name' => 'required',
            'title'       => 'required',
            'video'       => 'required|mimes:mp4|max:512000',
        ]);

        $video = $videoService->create($request);

        ProcessVideoJob::dispatch($video->id);

        return redirect()
            ->route('videos.index')
            ->with('success', 'Video uploaded successfully. Processing in background...');
    }

    /**
     * Get Video Details (AJAX)
     */
    public function edit(Video $video)
    {
        return response()->json([
            'success' => true,
            'video'   => $video
        ]);
    }

    /**
     * Update Video (AJAX)
     */
    public function update(Request $request, Video $video)
    {
        $request->validate([
            'course_name' => 'required|string|max:255',
            'title'       => 'required|string|max:255',
        ]);

        $video->update([
            'course_name' => $request->course_name,
            'title'       => $request->title,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Video updated successfully.',
            'video'   => $video
        ]);
    }

    /**
     * Delete Video (AJAX)
     */
    public function destroy(Video $video)
    {
        try {

            // Storage::disk('r2')->deleteDirectory(
            //     'videos/' . $video->uuid
            // );

            Storage::disk('r2')->deleteDirectory(

            config('app.class_storage_folder')

            .'/videos/'

            .$video->uuid

            );

            $video->delete();

            return response()->json([
                'success' => true,
                'message' => 'Video deleted successfully.'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }
}