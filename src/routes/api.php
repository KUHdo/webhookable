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

Route::group(['middleware' => 'auth:api'], function() {
    ///////////////
    // RestHooks //
    ///////////////

    //POST /api/hooks - subscribe
    // http://resthooks.org/docs/
    // zapier route
    Route::get('polling/trigger', 'KUHdo\Webhookable\Controllers\RestHooksController@pollForTrigger')
        ->middleware('auth:api');
});
Route::apiResource('subscription', 'KUHdo\Webhookable\Controllers\RestHooksController')
    ->middleware('auth:api');
