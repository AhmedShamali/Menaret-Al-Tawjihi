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
        if (config('app.env') === 'production' || str_contains(config('app.url'), 'https://') || request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // تشغيل المعالج الذاتي للتحقق من سلامة أعمدة وجداول قاعدة البيانات الحيوية تلقائياً
        \App\Support\SchemaHealer::heal();

        // استخدام مكون الترقيم العصري المخصص باللغة العربية
        \Illuminate\Pagination\Paginator::useBootstrapFive();
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.bootstrap-5');
    }
}
