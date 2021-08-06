<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\PassportAuthController;
use App\Http\Controllers\Api\v1\CurrencyMiddlewareController;

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

Route::group(['prefix' => 'v1'], function (){
    Route::post('register', [PassportAuthController::class, 'register']);
    Route::post('login', [PassportAuthController::class, 'login']);
    Route::middleware('auth:api')->get('/logout', [PassportAuthController::class, 'logout']);
    Route::middleware('auth:api')->get('/userInfo', [PassportAuthController::class, 'userInfo']);

    Route::middleware('auth:api')->get('GetAndSaveRates', [CurrencyMiddlewareController::class, 'GetAndSaveRates']);
    Route::middleware('auth:api')->get('GetCurrencyInformation', [CurrencyMiddlewareController::class, 'GetCurrencyInformation']);
    Route::middleware('auth:api')->get('GetRates', [CurrencyMiddlewareController::class, 'GetRates']);


});

