<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('api/')
    ->middleware(['middleware' => 'auth:api'])
    ->group(function() {
        // Zapier route
        Route::get('polling/trigger', 'KUHdo\Webhookable\Controllers\RestHooksController@pollForTrigger');
        // RestHooks
        // http://resthooks.org/docs/
        Route::apiResource('subscription', 'KUHdo\Webhookable\Controllers\RestHooksController');
});
