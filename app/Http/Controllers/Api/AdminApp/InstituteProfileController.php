<?php

namespace App\Http\Controllers\Api\AdminApp;

use App\Http\Controllers\Controller;
use App\Models\InstituteProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class InstituteProfileController extends Controller
{
    /**
     * Get Profile
     */
    public function index()
    {
        $profile = InstituteProfile::first();

        if (!$profile) {

            $profile = InstituteProfile::create([
                'name' => 'CodingWale Institute'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $profile
        ]);
    }

    /**
     * Update Profile
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required|max:255',

            'short_name' => 'nullable|max:50',

            'phone' => 'nullable|max:20',

            'email' => 'nullable|email',

            'website' => 'nullable|max:255',

            'gst_no' => 'nullable|max:100',

            'registration_no' => 'nullable|max:100',

            'address_line1' => 'nullable',

            'address_line2' => 'nullable',

            'city' => 'nullable|max:100',

            'state' => 'nullable|max:100',

            'pincode' => 'nullable|max:20',

            'receipt_footer' => 'nullable',

            'principal_name' => 'nullable|max:255',

            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ],422);
        }

        $profile = InstituteProfile::first();

        if (!$profile) {
            $profile = new InstituteProfile();
        }

        $profile->fill($request->all());

        if ($request->hasFile('logo')) {

            // Delete old logo
            if ($profile->getRawOriginal('logo') &&
                Storage::disk('public')->exists($profile->getRawOriginal('logo'))) {

                Storage::disk('public')->delete(
                    $profile->getRawOriginal('logo')
                );
            }

            $profile->logo = $request
                ->file('logo')
                ->store('institute_logo', 'public');
        }

        $profile->save();

        return response()->json([
            'success'=>true,
            'message'=>'Profile Updated Successfully',
            'data'=>$profile
        ]);
    }

    
}