<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [

        'uuid',

        'batch_name',

        'teacher_id',

        'course_name',

        'start_time',

        'end_time',

        'days',

        'room_no',

        'status',
    ];

    public function teacher()
    {
        return $this->belongsTo(
            Teacher::class
        );
    }

    public function students()
{
    return $this->belongsToMany(
        Student::class,
        'batch_students',
        'batch_uuid',
        'student_uuid',
        'uuid',
        'uuid'
    );
}
}