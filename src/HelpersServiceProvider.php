<?php

namespace QmediaBy\Helpers;

use Illuminate\Support\ServiceProvider;

class HelpersServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/helpers.php', 'helpers');
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/helpers.php' => $this->configPublishPath(),
        ], 'helpers-config');
    }

    /**
     * @return string
     */
    private function configPublishPath(): string
    {
        return function_exists('config_path')
            ? config_path('helpers.php')
            : base_path('config/helpers.php');
    }
}
