<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);

        // Share partner logos globally for footer and all views
        try {
            $partnerLogos = \App\Models\PartnersSectionCommonPage::active()->ordered()->get();
            view()->share('partnerLogos', $partnerLogos);
        } catch (\Exception $e) {
            // Table may not exist during migrations, gracefully handle
            view()->share('partnerLogos', collect());
        }
    }
}
