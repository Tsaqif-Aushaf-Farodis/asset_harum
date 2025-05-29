<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\Format;

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
        Paginator::useBootstrapFive();

        Blade::directive('rupiah', function ($expression) {
            return "<?php echo \App\Helpers\Format::rupiah($expression); ?>";
        });

        // Menggunakan View Composer secara langsung tanpa membuat class khusus
        View::composer('*', function ($view) {
            // Bagikan data ke semua
            // $view->with('noback', false);
            // $view->with('withError', true);
        });
    }
}
