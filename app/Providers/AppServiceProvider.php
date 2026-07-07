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
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        \Illuminate\Support\Facades\Blade::directive('indianCurrency', function ($expression) {
            return "<?php echo '₹ ' . (function(\$amount) {
                if (\$amount <= 0) return '0.00';
                \$parts = explode('.', number_format((float)\$amount, 2, '.', ''));
                \$integer = \$parts[0];
                \$decimal = isset(\$parts[1]) ? '.' . \$parts[1] : '.00';
                \$lastThree = substr(\$integer, -3);
                \$rest = substr(\$integer, 0, -3);
                if (\$rest !== '') {
                    \$rest = preg_replace('/\\B(?=(\\d{2})+(?!\\d))/', ',', \$rest) . ',';
                }
                return \$rest . \$lastThree . \$decimal;
            })($expression); ?>";
        });
    }
}
