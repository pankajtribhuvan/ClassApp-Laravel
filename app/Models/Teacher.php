<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [

        'uuid',

        'full_name',

        'mobile',

        'whatsapp',

        'email',

        'qualification',

        'specialization',

        'joining_date',

        'salary',

        'photo',

        'address',

        'status',
    ];

    protected $casts = [

        'joining_date' => 'date',

        'salary' => 'decimal:2',
    ];

    public function batches()
    {
        return $this->hasMany(
            Batch::class
        );
    }
}