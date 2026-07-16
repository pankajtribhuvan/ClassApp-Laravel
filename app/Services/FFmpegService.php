<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class FFmpegService
{
    /**
     * Convert MP4 to HLS
     */
    public function convert(string $inputFile, string $outputFolder): bool
    {
        if (!is_dir($outputFolder)) {
            mkdir($outputFolder, 0777, true);
        }

        $playlist = $outputFolder . DIRECTORY_SEPARATOR . "index.m3u8";

        $segments = $outputFolder . DIRECTORY_SEPARATOR . "segment%03d.ts";

        Log::info("FFmpeg Started");

        $result = Process::timeout(0)->run([

            'ffmpeg',

            '-y',

            '-i',
            $inputFile,

            '-c:v',
            'libx264',

            '-c:a',
            'aac',

            '-preset',
            'veryfast',

            '-crf',
            '23',

            '-hls_time',
            '6',

            '-hls_playlist_type',
            'vod',

            '-hls_segment_filename',
            $segments,

            $playlist

        ]);

        if ($result->failed()) {

            Log::error($result->errorOutput());

            throw new \Exception($result->errorOutput());

        }

        Log::info("FFmpeg Completed");

        return true;
    }

    /**
     * Generate Thumbnail
     */
    public function thumbnail(
        string $inputFile,
        string $outputImage
    ): bool {

        $result = Process::timeout(0)->run([

            'ffmpeg',

            '-y',

            '-i',
            $inputFile,

            '-ss',
            '00:00:03',

            '-frames:v',
            '1',

            $outputImage

        ]);

        if ($result->failed()) {

            throw new \Exception($result->errorOutput());

        }

        return true;
    }

    /**
     * Video Duration (seconds)
     */
    public function duration(string $inputFile): float
    {
        $result = Process::run([

            'ffprobe',

            '-v',

            'error',

            '-show_entries',

            'format=duration',

            '-of',

            'default=noprint_wrappers=1:nokey=1',

            $inputFile

        ]);

        return round((float) $result->output(), 2);
    }

    /**
     * Resolution
     */
    public function resolution(string $inputFile): array
    {
        $result = Process::run([

            'ffprobe',

            '-v',

            'error',

            '-select_streams',

            'v:0',

            '-show_entries',

            'stream=width,height',

            '-of',

            'csv=s=x:p=0',

            $inputFile

        ]);

        $size = trim($result->output());

        [$width, $height] = explode('x', $size);

        return [

            'width' => (int) $width,

            'height' => (int) $height

        ];
    }
}