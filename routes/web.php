<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

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
    if (!auth()->check()) {
        return redirect('/login');
    }

    // TODO should do a full spa
    return view('users.balance');
});
