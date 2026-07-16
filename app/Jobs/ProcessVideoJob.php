<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\VideoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessVideoJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $videoId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(VideoService $videoService): void
    {
        $video = Video::find($this->videoId);

        if (!$video) {
            return;
        }

        $videoService->process($video);
    }
}