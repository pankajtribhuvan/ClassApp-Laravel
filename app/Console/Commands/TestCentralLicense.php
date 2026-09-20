<?php

namespace App\Console\Commands;

use App\Services\CentralLicenseService;
use Illuminate\Console\Command;
use Throwable;

class TestCentralLicense extends Command
{
    protected $signature = 'license:verify';

    protected $description = 'Test V1 license verification with Class-License';

    public function handle(
        CentralLicenseService $centralLicenseService
    ): int {
        $this->info('Testing V1 license verification...');
        $this->newLine();

        try {
            $result = $centralLicenseService->verify(
                config('app.version', '1.0.0')
            );

            $this->info('License verification successful.');
            $this->newLine();

            $this->line(
                json_encode(
                    $result,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                )
            );

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('License verification failed.');
            $this->newLine();
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}