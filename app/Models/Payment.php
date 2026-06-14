<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    protected $fillable = [

    'uuid',

    'student_uuid',

    'admission_no',

    'receipt_no',

    'amount',

    'payment_mode',

    'remarks',

    'payment_date',
    'next_due_date',
];
}
