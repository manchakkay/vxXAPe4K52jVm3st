<?php

namespace App\Providers;

use DOMDocument;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        URL::forceScheme('https');

        Paginator::useBootstrap();

        // Загрузка SVG иконок
        Blade::directive('svg', function ($arguments) {
            // Funky madness to accept multiple arguments into the directive
            list(
                $path, $class, $attr_name, $attr_value
            ) = array_pad(explode(',', trim($arguments, "() ")), 4, '');
            $path = trim($path, "\"' ");
            $class = trim($class, "\"' ");
            $attr_name = trim($attr_name, "\"' ");
            $attr_value = trim($attr_value, "\"' ");


            // Create the dom document as per the other answers
            $svg = new DOMDocument();
            $svg->load(\public_path($path), LIBXML_NOERROR);
            $svg->documentElement->setAttribute("class", $class);
            if ($attr_name && $attr_value) {
                $svg->documentElement->setAttribute($attr_name, $attr_value);
            }
            $output = $svg->saveXML($svg->documentElement);

            return $output;
        });
    }
}
