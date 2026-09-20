<?php

namespace App\Http\Middleware;

use App\Services\CentralLicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class VerifyV2CentralLicense
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $centralLicenseService = app(CentralLicenseService::class);

        try {
            $result = $centralLicenseService->verify(
                config('app.version', '1.0.0'),
                'v2'
            );

            $licenseStatus = $result['data']['license_status'] ?? null;

            if (
                ! is_array($licenseStatus) ||
                ($licenseStatus['valid'] ?? false) !== true
            ) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                        ?? 'License is not active.',
                    'license_status' => $licenseStatus,
                ], Response::HTTP_FORBIDDEN);
            }

            $request->attributes->set(
                'central_license',
                $result['data']
            );

            return $next($request);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to verify application license.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}