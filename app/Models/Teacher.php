<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Teacher extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'uuid',
        'full_name',
        'mobile',
        'whatsapp',
        'email',
        'password',
        'qualification',
        'specialization',
        'joining_date',
        'salary',
        'photo',
        'address',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'salary' => 'decimal:2',
        'last_login_at' => 'datetime',
    ];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }
}