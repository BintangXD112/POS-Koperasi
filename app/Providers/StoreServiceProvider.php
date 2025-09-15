<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\StoreHelper;

class StoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share store settings to all views
        View::composer('*', function ($view) {
            try {
                $storeSettings = StoreHelper::getCompleteStoreInfo();
                $view->with('storeSettings', $storeSettings);
            } catch (\Exception $e) {
                // Fallback jika database belum siap
                $view->with('storeSettings', [
                    'name' => 'Sistem Koperasi',
                    'logo_url' => null,
                    'address' => null,
                    'phone' => null,
                    'email' => null,
                    'owner' => null,
                    'website' => null,
                    'description' => null,
                    'currency' => 'Rp',
                    'timezone' => 'Asia/Jakarta',
                    'tax_number' => null,
                    'footer_text' => null,
                    'is_active' => true,
                ]);
            }
        });
    }
}