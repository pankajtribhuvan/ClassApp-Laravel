<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Student extends Model
{
    /*
    |--------------------------------------------------------------------------
    | PRIMARY KEY
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'uuid',
        
        'inquiry_id',

        'full_name',

        'mobile',

        'whatsapp',

        'email',

        'college_school',

        'current_class',

        'interested_courses',

        'referred_by',

        'inquiry_date',

        'parent_phone',

        'father_occupation',

        'student_photo',

        'aadhar_photo',

        'admission_date',

        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | TYPE CASTING
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'interested_courses' => 'array',

        'admission_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | AUTO UUID
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $model->uuid =
                (string) Str::uuid();
        });
    }
}