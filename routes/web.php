<?php

use Illuminate\Support\Facades\Route;

// TODO
// ensuring the first user is always logged in
if (!app()->environment('testing')) {
    if ($user = \App\User::first()) {
        auth()->loginUsingId($user->id);
    }
}

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
    // TODO should do a full spa
    return view('users.balance');
});

// TODO just for convenience
Route::group(['prefix' => '/api'], function () {
    Route::group(['prefix' => '/users/{user}'], function() {
        Route::get('/transactions/total-by-date', 'TransactionsController@totalByDate');
        Route::get('/transactions', 'TransactionsController@index');
        Route::post('/transactions', 'TransactionsController@store');
        Route::get('/balance', 'UsersController@balance');
    });

    Route::delete('/transactions/{transaction}', 'TransactionsController@destroy');
    Route::put('/transactions/{transaction}', 'TransactionsController@update');

});

Auth::routes();

