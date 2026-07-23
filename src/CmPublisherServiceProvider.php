<?php

namespace BahriCanli\CmPublisher;

use Illuminate\Support\ServiceProvider;

class CmPublisherServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Config yayınla: php artisan vendor:publish --tag=cm-publisher
        $this->publishes([
            __DIR__ . '/../config/cm-publisher.php' => config_path('cm-publisher.php'),
        ], 'cm-publisher');

        $this->mergeConfigFrom(__DIR__ . '/../config/cm-publisher.php', 'cm-publisher');

        // Route'ları yükle
        $this->loadRoutesFrom(__DIR__ . '/../routes/cm-publisher.php');
    }
}
