<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login',[\App\Http\Controllers\Api\LoginController::class,'post'])->middleware(['api-log']);
/*Route::middleware('api-log')->post('/login', function (Request $request) {
    return ['code' => 200,'msg' => 'success','data' => ['userId'=> '1', 'token'=> 'debug',]];
});*/
Route::middleware(['api-log','auth.api'])->group(function(){
    Route::get('/menu',[\App\Http\Controllers\Api\BaseController::class,'menu']);
    Route::post('/commonest/get-select-list',[\App\Http\Controllers\Api\CommonestController::class,'getSelectList']);

    Route::post('/user',[\App\Http\Controllers\Api\UserController::class,'index']);
    Route::delete('/user/{id}',[\App\Http\Controllers\Api\UserController::class,'delete']);
    Route::post('/user/create-or-update',[\App\Http\Controllers\Api\UserController::class,'createOrUpdate']);

    Route::post('/material',[\App\Http\Controllers\Api\MaterialController::class,'index']);
    Route::delete('/material/{id}',[\App\Http\Controllers\Api\MaterialController::class,'delete']);
    Route::post('/material/create-or-update',[\App\Http\Controllers\Api\MaterialController::class,'createOrUpdate']);

    Route::post('/sku',[\App\Http\Controllers\Api\SkuController::class,'index']);
    Route::post('/sku/createOrUpdate',[\App\Http\Controllers\Api\SkuController::class,'createOrUpdate']);
    Route::get('/sku/info/{id}',[\App\Http\Controllers\Api\SkuController::class,'getInfo']);
    Route::delete('/sku/{id}',[\App\Http\Controllers\Api\SkuController::class,'delete']);

    Route::post('/supplier',[\App\Http\Controllers\Api\SupplierController::class,'index']);
    Route::post('/supplier/create-or-update',[\App\Http\Controllers\Api\SupplierController::class,'createOrUpdate']);
    Route::delete('/supplier/{id}',[\App\Http\Controllers\Api\SupplierController::class,'delete']);

    Route::post('/asn',[\App\Http\Controllers\Api\AsnController::class,'index']);
    Route::post('/asn/create-or-update',[\App\Http\Controllers\Api\AsnController::class,'createOrUpdate']);
    Route::delete('/asn/{id}',[\App\Http\Controllers\Api\AsnController::class,'delete']);
    Route::get('/asn/info/{id}',[\App\Http\Controllers\Api\AsnController::class,'getInfo']);
    Route::get('/asn/items/{asn_number}',[\App\Http\Controllers\Api\AsnController::class,'getItems']);
    Route::post('/asn/inbound',[\App\Http\Controllers\Api\AsnController::class,'inbound']);

    Route::post('/order',[\App\Http\Controllers\Api\OrderController::class,'index']);
    Route::post('/order/create-or-update',[\App\Http\Controllers\Api\OrderController::class,'createOrUpdate']);
    Route::delete('/order/{id}',[\App\Http\Controllers\Api\OrderController::class,'delete']);
    Route::get('/order/info/{id}',[\App\Http\Controllers\Api\OrderController::class,'getInfo']);
});

