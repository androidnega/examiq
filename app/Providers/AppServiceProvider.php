<?php

namespace App\Providers;

use App\Services\Sms\Contracts\SmsSender;
use App\Services\Sms\RuntimeSmsSender;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SmsSender::class, RuntimeSmsSender::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function (\Illuminate\View\View $view): void {
            $view->with('monitoringBannerEnabled', Cache::get('examiq.monitoring_banner_enabled', true));
        });
    }
}
