<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // load system settings from database and override config values
        try {
            if (class_exists(\App\Models\SystemSetting::class)) {
                $stored = \App\Models\SystemSetting::allSettings();
                if ($stored) {
                    if (isset($stored['site_name'])) {
                        config(['app.name' => $stored['site_name']]);
                    }
                    if (isset($stored['default_currency'])) {
                        config(['invoicehero.default_currency' => $stored['default_currency']]);
                    }
                    if (isset($stored['default_gst_rate'])) {
                        config(['invoicehero.default_gst_rate' => $stored['default_gst_rate']]);
                    }
                    if (isset($stored['smtp_host'])) {
                        config(['mail.mailers.smtp.host' => $stored['smtp_host']]);
                    }
                    if (isset($stored['smtp_port'])) {
                        config(['mail.mailers.smtp.port' => $stored['smtp_port']]);
                    }
                    if (isset($stored['smtp_username'])) {
                        config(['mail.mailers.smtp.username' => $stored['smtp_username']]);
                    }
                    if (isset($stored['from_address'])) {
                        config(['mail.from.address' => $stored['from_address']]);
                    }
                    if (isset($stored['whatify_api_key'])) {
                        config(['services.whatify.api_key' => $stored['whatify_api_key']]);
                    }
                    if (isset($stored['whatify_base_url'])) {
                        config(['services.whatify.base_url' => $stored['whatify_base_url']]);
                    }
                    // maintenance mode is handled separately via artisan call in controller
                }
            }
        } catch (\Exception $e) {
            // ignore errors, e.g. during migrations when table doesn't exist yet
        }

        // Register tracking helper for global access
        if (class_exists(\App\Models\TrackingCode::class)) {
            try {
                view()->share('tracking', resolve(\App\Helpers\TrackingHelper::class));
            } catch (\Exception $e) {
                // ignore errors
            }
        }
    }
}
