<?php

use Illuminate\Http\Request;

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


Route::post(
    '/login',
    [
        'uses' => '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken',
    ]
);

Route::post(
    '/loans',
    [
        'uses' => '\App\Http\Controllers\ChallengeController\LoansController@createLoan'
    ]
);

Route::get(
    '/loans',
    [
        'uses' => '\App\Http\Controllers\ChallengeController\LoansController@getLoans'
    ]
);

Route::post(
    '/loans/{loan_id}/payments',
    [
        'uses' => '\App\Http\Controllers\ChallengeController\LoansController@createPayment'
    ]
);


Route::get(
    '/loans/{loan_id}/balance',
    [
        'uses' => '\App\Http\Controllers\ChallengeController\LoansController@getBalance'
    ]
);

Route::middleware('auth:api')->get(
    '/user', function (Request $request) {
        return $request->user();
    }
);
