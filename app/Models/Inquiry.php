<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $table = 'inquiries';

    protected $fillable = [
        'full_name',
        'mobile',
        'whatsapp',
        'email', 
        'college_school',
        'current_class',
        'interested_courses',
        'referred_by',
        'inquiry_date',
    ];

    // --- CRITICAL STEP: REQUIRED FOR JSON STORAGE ---
    protected $casts = [
        'interested_courses' => 'json',
    ];
}