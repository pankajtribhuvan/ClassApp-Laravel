<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
   protected $fillable = [

    'uuid',

    'course_name',

    'description',

    'duration',

    'fees',

    'installments',

    'course_mode',

    'status',
    
];
    
}
