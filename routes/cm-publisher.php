<?php

use BahriCanli\CmPublisher\CmPublisherController;
use BahriCanli\CmPublisher\CmPublisherMiddleware;
use Illuminate\Support\Facades\Route;

$prefix = config('cm-publisher.prefix', 'cm');

Route::prefix($prefix)
    ->middleware([CmPublisherMiddleware::class])
    ->group(function () {
        Route::post('blog/create', [CmPublisherController::class, 'create']);
        Route::post('blog/update', [CmPublisherController::class, 'update']);
        Route::post('blog/delete', [CmPublisherController::class, 'delete']);
    });
