<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [

        'uuid',

        'course_name',

        'title',

        'original_video',

        'hls_url',

        'thumbnail_url',

        'duration',
        'r2_size',

        'status'

    ];

    public function getReadableSizeAttribute(): string
{
    $bytes = $this->r2_size;

    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $i = 0;

    while ($bytes >= 1024 && $i < count($units) - 1) {

        $bytes /= 1024;

        $i++;

    }

    return round($bytes, 2) . ' ' . $units[$i];
}
}