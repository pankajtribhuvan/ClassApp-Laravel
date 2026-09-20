<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerApplication;
use App\Services\LicenseVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function verify(
        Request $request,
        LicenseVerificationService $licenseVerificationService
    ): JsonResponse {
        /*
        |--------------------------------------------------------------------------
        | Validate Request
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'application_uuid' => [
                'required',
                'uuid',
            ],

            'app_version' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Find Customer Application
        |--------------------------------------------------------------------------
        */

        $customerApplication = CustomerApplication::query()
            ->where('uuid', $validated['application_uuid'])
            ->where('is_active', true)
            ->first();

        if (! $customerApplication) {
            return response()->json([
                'valid' => false,
                'status' => 'invalid_application',
                'message' => 'Application is invalid or inactive.',
                'license' => null,
                'subscription' => null,
                'plan' => null,
                'features' => [],
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify License
        |--------------------------------------------------------------------------
        */

        $result = $licenseVerificationService->verify(
            $customerApplication
        );

        return response()->json($result);
    }
}