<?php

namespace BahriCanli\Publisher;

use Illuminate\Support\ServiceProvider;

class PublisherServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/publisher.php' => config_path('publisher.php'),
        ], 'bahricanli-publisher');

        $this->mergeConfigFrom(__DIR__ . '/../config/publisher.php', 'bahricanli-publisher');

        $this->loadRoutesFrom(__DIR__ . '/../routes/publisher.php');
    }
}
