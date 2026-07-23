<?php

use BahriCanli\Publisher\PublisherController;
use BahriCanli\Publisher\PublisherMiddleware;
use Illuminate\Support\Facades\Route;

$prefix = config('bahricanli-publisher.prefix', 'cm');

// Laravel 5.x uses string-based controller syntax; 6+ supports array syntax
if (version_compare(app()->version(), '6.0', '>=')) {
    Route::group(['prefix' => $prefix, 'middleware' => [PublisherMiddleware::class]], function () {
        Route::post('blog/create', [PublisherController::class, 'create']);
        Route::post('blog/update', [PublisherController::class, 'update']);
        Route::post('blog/delete', [PublisherController::class, 'delete']);
    });
} else {
    Route::group(['prefix' => $prefix, 'middleware' => [PublisherMiddleware::class]], function () {
        Route::post('blog/create', 'BahriCanli\Publisher\PublisherController@create');
        Route::post('blog/update', 'BahriCanli\Publisher\PublisherController@update');
        Route::post('blog/delete', 'BahriCanli\Publisher\PublisherController@delete');
    });
}
