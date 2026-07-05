<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [

        'uuid',

        'batch_uuid',

        'attendance_date',

        'present_student_uuids',

        'marked_by_type',

        'marked_by_uuid',
    ];

    protected $casts = [

        'attendance_date'
            => 'date',

        'present_student_uuids'
            => 'array',
    ];
}