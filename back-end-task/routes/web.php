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
        "message" => 'Welcome to covid 19 challenge'
    ];
    return json_encode($app_info, JSON_PRETTY_PRINT);
//    return view('welcome');
});
