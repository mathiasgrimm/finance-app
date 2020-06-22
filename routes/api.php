<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('mathias', function() {
        return 'hi';
    });

    Route::group(['prefix' => '/users/{user}'], function() {
        Route::get('/transactions/total-by-date', 'TransactionsController@totalByDate');
        Route::get('/transactions', 'TransactionsController@index');
        Route::post('/transactions', 'TransactionsController@store');
        Route::get('/balance', 'UsersController@balance');

        Route::group(['prefix' => '/transaction-imports'], function () {
            Route::post('/', 'TransactionImportsController@store');
            Route::get('/importing', 'TransactionImportsController@importing');
        });

    });

    Route::delete('/transactions/{transaction}', 'TransactionsController@destroy');
    Route::put('/transactions/{transaction}', 'TransactionsController@update');
});


