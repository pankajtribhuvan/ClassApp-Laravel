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
        'serial_no',
        
        'inquiry_id',

        'full_name',

        'mobile',

        'whatsapp',

        'email',

        'password',

        'college_school',

        'current_class',

        // 'interested_courses',

        'referred_by',

        'inquiry_date',

        'parent_phone',

        'father_occupation',

        'student_photo',

        'aadhar_photo',

        'admission_date',

        'status',

        'course_uuid',
'course_name',

'total_fees',
'paid_fees',
'balance_fees',

'installments',

'admission_no',
    ];


    protected $hidden = [
    'password',
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

    public function batches()
    {
        return $this->belongsToMany(
            Batch::class,
            'batch_students',
            'student_uuid',
            'batch_uuid',
            'uuid',
            'uuid'
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}