<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VideoService
{
    protected FFmpegService $ffmpeg;
    protected R2Service $r2;

    public function __construct()
    {
        $this->ffmpeg = new FFmpegService();
        $this->r2 = new R2Service();
    }

    /**
     * Upload original video and create database record.
     */
    public function create(Request $request): Video
    {
        $uuid = Str::uuid()->toString();

        $file = $request->file('video');

        $filename = $uuid . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            'uploads/original',
            $filename,
            'local'
        );

        $video = Video::create([
            'uuid'           => $uuid,
            'course_name'    => $request->course_name,
            'title'          => $request->title,
            'original_video' => $path,
            'status'         => 'uploaded',
        ]);

        Log::info("Video Uploaded : {$uuid}");

        return $video;
    }

    /**
     * Process Video in Background
     */
    public function process(Video $video): void
    {
        try {

            Log::info("Processing Started : {$video->uuid}");

            $video->update([
                'status' => 'processing'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Paths
            |--------------------------------------------------------------------------
            */

            $inputFile = storage_path(
                'app/private/' . $video->original_video
            );

            $outputFolder = storage_path(
                'app/private/uploads/hls/' . $video->uuid
            );

            // $r2Size = $this->r2->folderSize($outputFolder);

            /*
            |--------------------------------------------------------------------------
            | Convert MP4 → HLS
            |--------------------------------------------------------------------------
            */

            Log::info("FFmpeg Started");

            $this->ffmpeg->convert(
                $inputFile,
                $outputFolder
            );

            Log::info("FFmpeg Completed");

            /*
            |--------------------------------------------------------------------------
            | Generate Thumbnail
            |--------------------------------------------------------------------------
            */

            $thumbnail = $outputFolder . DIRECTORY_SEPARATOR . 'thumbnail.jpg';

            $this->ffmpeg->thumbnail(
                $inputFile,
                $thumbnail
            );

            Log::info("Thumbnail Generated");

            /*
            |--------------------------------------------------------------------------
            | Calculate Folder Size
            |--------------------------------------------------------------------------
            */

            $r2Size = $this->r2->folderSize($outputFolder);

            Log::info("R2 Size : {$r2Size}");


            /*
            |--------------------------------------------------------------------------
            | Get Duration
            |--------------------------------------------------------------------------
            */

            $duration = $this->ffmpeg->duration(
                $inputFile
            );

           
            Log::info("Duration : {$duration}");

            /*
            |--------------------------------------------------------------------------
            | Upload to Cloudflare R2
            |--------------------------------------------------------------------------
            */

            $video->update([
                'status' => 'uploading'
            ]);

            Log::info("Uploading HLS");

            $hlsUrl = $this->r2->uploadFolder(
                $outputFolder,
                $video->uuid
            );

            Log::info("Uploading Thumbnail");

            $thumbnailUrl = $this->r2->uploadThumbnail(
                $thumbnail,
                $video->uuid
            );

            /*
            |--------------------------------------------------------------------------
            | Update Database
            |--------------------------------------------------------------------------
            */

            // dd($r2Size);
            $video->update([
                'hls_url'       => $hlsUrl,
                'thumbnail_url' => $thumbnailUrl,
                'duration'      => $duration,
                'r2_size'       => $r2Size,
                'status'        => 'completed'
            ]);

            Log::info("Database Updated");

            /*
            |--------------------------------------------------------------------------
            | Cleanup Local Files
            |--------------------------------------------------------------------------
            */

            if (File::exists($inputFile)) {
                File::delete($inputFile);
            }

            if (File::exists($thumbnail)) {
                File::delete($thumbnail);
            }

            if (File::isDirectory($outputFolder)) {
                File::deleteDirectory($outputFolder);
            }

            Log::info("Cleanup Completed");

        } catch (\Exception $e) {

            Log::error($e->getMessage());

            $video->update([
                'status' => 'failed'
            ]);

            throw $e;
        }
    }
}