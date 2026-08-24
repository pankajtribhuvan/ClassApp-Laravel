<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExamResult extends Model
{
    protected $table = 'exam_results';

    protected $fillable = [
        'uuid',
        'teacher_uuid',
        'batch_uuid',
        'exam_name',
        'exam_date',
        'results',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'results' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($examResult) {
            if (empty($examResult->uuid)) {
                $examResult->uuid = (string) Str::uuid();
            }
        });
    }
}