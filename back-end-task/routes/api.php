<?php

use App\Http\Middleware\LogRouteMiddleware;
use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Passing the routes through LogRouteMiddleware in order to log visited route details

Route::get('/v1/on-covid-19/logs', 'Covid19Controller@getLogs')->middleware(LogRouteMiddleware::class);

Route::post('/v1/on-covid-19/{format?}', 'Covid19Controller@getData')->middleware(LogRouteMiddleware::class);
