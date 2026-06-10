<?php

namespace App\Providers;

use App\Services\MidtransClient;
use App\Services\PaymentService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class MidtransServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register MidtransClient as singleton
        $this->app->singleton(MidtransClient::class, function ($app) {
            return new MidtransClient();
        });

        // Register PaymentService as singleton
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService($app->make(MidtransClient::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->validateMidtransConfiguration();
    }

    /**
     * Validate Midtrans configuration on application startup
     *
     * @return void
     */
    protected function validateMidtransConfiguration(): void
    {
        $serverKey = config('midtrans.server_key');
        $clientKey = config('midtrans.client_key');
        $isProduction = config('midtrans.is_production');

        // Check if required configuration values are set
        if (empty($serverKey)) {
            Log::warning('Midtrans Server Key is not configured. Payment features will not work.');
        }

        if (empty($clientKey)) {
            Log::warning('Midtrans Client Key is not configured. Payment features will not work.');
        }

        // Log configuration status
        if (!empty($serverKey) && !empty($clientKey)) {
            $environment = $isProduction ? 'PRODUCTION' : 'SANDBOX';
            Log::info("Midtrans configuration validated successfully. Environment: {$environment}");
        }

        // Validate environment mode is boolean
        if (!is_bool($isProduction)) {
            Log::warning('Midtrans is_production should be a boolean value (true/false).');
        }
    }
}
