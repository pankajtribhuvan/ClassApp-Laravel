<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class R2Service
{
    /**
     * Upload HLS Folder
     */
    public function uploadFolder(string $localFolder, string $uuid): string
    {
        if (!File::isDirectory($localFolder)) {
            throw new \Exception("Directory not found : {$localFolder}");
        }

        $files = File::allFiles($localFolder);

        foreach ($files as $file) {

            // Skip thumbnail (uploaded separately)
            if ($file->getFilename() === 'thumbnail.jpg') {
                continue;
            }

            $absolutePath = $file->getRealPath();

            // Works on Windows & Linux
            $relativePath = str_replace(
                '\\',
                '/',
                $file->getRelativePathname()
            );

            // $destination = "videos/{$uuid}/{$relativePath}";
           $destination =
config('app.class_storage_folder')
.'/videos/'
.$uuid
.'/'
.$relativePath;


            Log::info("Uploading : {$destination}");

            Storage::disk('r2')->put(
                $destination,
                fopen($absolutePath, 'r')
            );

            Log::info("Uploaded : {$destination}");
        }

        // $playlistUrl = rtrim(env('VIDEO_BASE_URL'), '/'). "/videos/{$uuid}/index.m3u8";
        // $playlistUrl =
        // rtrim(env('VIDEO_BASE_URL'),'/')
        // .'/'
        // .config('app.class_storage_folder')
        // .'/videos/'
        // .$uuid
        // .'/index.m3u8';


        $folder = trim(config('app.class_storage_folder'), '/');

$playlistUrl =
    rtrim(env('VIDEO_BASE_URL'), '/')
    . ($folder ? "/{$folder}" : "")
    . "/videos/{$uuid}/index.m3u8";

        Log::info("Playlist URL : {$playlistUrl}");

        return $playlistUrl;
    }

    /**
     * Upload Thumbnail
     */
    public function uploadThumbnail(string $localFile, string $uuid): string
    {
        if (!File::exists($localFile)) {
            throw new \Exception("Thumbnail not found : {$localFile}");
        }

        // $destination = "videos/{$uuid}/thumbnail.jpg";
        $destination=
config('app.class_storage_folder')
."/videos/"
.$uuid
."/thumbnail.jpg";

        Log::info("Uploading Thumbnail : {$destination}");

        Storage::disk('r2')->put(
            $destination,
            fopen($localFile, 'r')
        );

        Log::info("Thumbnail Uploaded");

        // return rtrim(env('VIDEO_BASE_URL'), '/')
        //     . "/videos/{$uuid}/thumbnail.jpg";

    //     return rtrim(env('VIDEO_BASE_URL'), '/')
    // . '/'
    // . rtrim(config('app.class_storage_folder'), '/')
    // . "/videos/{$uuid}/thumbnail.jpg";

    $folder = trim(config('app.class_storage_folder'), '/');

return rtrim(env('VIDEO_BASE_URL'), '/')
    . ($folder ? "/{$folder}" : "")
    . "/videos/{$uuid}/thumbnail.jpg";
    
    }


    public function folderSize(string $localFolder): int
    {
        $size = 0;

        foreach (File::allFiles($localFolder) as $file) {

            $size += $file->getSize();

        }

        return $size;
    }
}