<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class CentralLicenseService
{
    public function verify(
        string $appVersion = '1.0.0',
        string $application = 'v1'
    ): array {
        $application = strtolower($application);

        if (! in_array($application, ['v1', 'v2'], true)) {
            throw new RuntimeException(
                'Invalid license application.'
            );
        }

        $url = rtrim(
            config('services.license.url'),
            '/'
        ) . '/api/license/verify';

        $identifier = config(
            "services.license.{$application}_identifier"
        );

        $secret = config(
            "services.license.{$application}_secret"
        );

        $applicationUuid = config(
            "services.license.{$application}_application_uuid"
        );

        if (
            ! $identifier ||
            ! $secret ||
            ! $applicationUuid
        ) {
            throw new RuntimeException(
                strtoupper($application) .
                ' license system configuration is missing.'
            );
        }

        $timestamp = (string) now()->timestamp;
        $nonce = Str::random(32);

        $payload = [
            'application_uuid' => $applicationUuid,
            'app_version' => $appVersion,
        ];

        $body = json_encode(
            $payload,
            JSON_UNESCAPED_SLASHES
        );

        if ($body === false) {
            throw new RuntimeException(
                'Unable to encode license verification payload.'
            );
        }

        $bodyHash = hash('sha256', $body);

        $path = '/api/license/verify';

        $canonicalString = implode("\n", [
            'POST',
            $path,
            $timestamp,
            $nonce,
            $bodyHash,
        ]);

        $signature = hash_hmac(
            'sha256',
            $canonicalString,
            $secret
        );

        $response = Http::withHeaders([
            'X-License-Identifier' => $identifier,
            'X-License-Timestamp' => $timestamp,
            'X-License-Nonce' => $nonce,
            'X-License-Signature' => $signature,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($url, $payload);

        if (! $response->successful()) {
            throw new RuntimeException(
                'License verification failed: ' .
                $response->body()
            );
        }

        return $response->json();
    }
}