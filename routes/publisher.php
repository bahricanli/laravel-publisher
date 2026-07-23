<?php

use BahriCanli\Publisher\PublisherController;
use BahriCanli\Publisher\PublisherMiddleware;
use Illuminate\Support\Facades\Route;

$prefix = config('bahricanli-publisher.prefix', 'cm');

Route::prefix($prefix)
    ->middleware([PublisherMiddleware::class])
    ->group(function () {
        Route::post('blog/create', [PublisherController::class, 'create']);
        Route::post('blog/update', [PublisherController::class, 'update']);
        Route::post('blog/delete', [PublisherController::class, 'delete']);
    });
