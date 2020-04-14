<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $app_info = [
        "message" => 'Welcome to #BuildForSDG Cohort-1 Assessment'
    ];

    return json_encode($app_info, JSON_PRETTY_PRINT);
});
