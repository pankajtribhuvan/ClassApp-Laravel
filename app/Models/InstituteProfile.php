<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstituteProfile extends Model
{
    protected $fillable = [

        'name',
        'short_name',

        'phone',
        'email',
        'website',

        'gst_no',
        'registration_no',

        'address_line1',
        'address_line2',

        'city',
        'state',
        'pincode',

        'receipt_footer',

        'principal_name',

        'logo'
    ];

    public function getLogoAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }
}