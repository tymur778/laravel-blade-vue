<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class sharedDataProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $sharedData = [
            'menu' => getMenus(),
            'phpVersion' => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
            'laravelVersion' => explode('.', App::version())[0],
            'renderType' => getRenderType(),
            'bladeType' => \RenderType::BLADE->value,
            'inertiaType' => \RenderType::INERTIA->value,
            'currentYear' => \Carbon\Carbon::now()->year,
        ];
        foreach ($sharedData as $key => $data) {
            switch (getRenderType()) {
                case \RenderType::BLADE->value:
                    View::share($key, $data);
                    break;
                case \RenderType::INERTIA->value:
                    Inertia::share($key, $data);
                    break;
            }
        }
    }
}
