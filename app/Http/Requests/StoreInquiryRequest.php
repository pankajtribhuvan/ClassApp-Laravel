<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'full_name'          => 'required|string|max:255',
            'mobile'             => 'required|string|max:20',
            'whatsapp'           => 'nullable|string|max:20',
            'email'              => 'nullable|email|max:255', 
            'college_school'     => 'nullable|string|max:255',
            'current_class'      => 'nullable|string|max:50',
            'interested_courses' => 'required|array', 
            'referred_by'        => 'nullable|string|max:255',
            'inquiry_date'       => 'nullable|string|max:50',
        ];
    }
}