<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SyllabusUpdate extends Model
{
    use HasFactory;

    protected $table = 'syllabus_updates';

    protected $fillable = [
        'uuid',
        'batch_uuid',
        'step_no',
        'title',
        'description',
        'topics',
        'status',
        'completed_at',
        'created_by_type',
        'created_by_uuid',
    ];

    protected $casts = [
        'topics' => 'array',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($syllabus) {
            if (empty($syllabus->uuid)) {
                $syllabus->uuid = (string) Str::uuid();
            }
        });
    }
}