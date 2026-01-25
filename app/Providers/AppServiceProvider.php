<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade; // <--- KIỂM TRA DÒNG NÀY CÓ CHƯA?
use Illuminate\Pagination\Paginator;

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
        // Đảm bảo đường dẫn này đúng. 
        // resource_path('views/layouts') nghĩa là thư mục: resources/views/layouts
        Blade::anonymousComponentPath(resource_path('views/layouts'), 'layouts');
    }
}   