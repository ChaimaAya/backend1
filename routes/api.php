<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicationsController;
use App\Http\Controllers\UserController;
use GuzzleHttp\Middleware;
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
Route::group(['middleware'=>'api','prefix'=>'auth'],function($router){
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::get('/user',[AuthController::class,'user']);
    Route::get('/secteurs',[AuthController::class,'secteurs']);
    Route::put('/updateprofile',[AuthController::class,'updateProfile']);
    Route::get('/startup',[AuthController::class,'getStartupDetailsForUser']);
    Route::post('/logout',[AuthController::class,'logout']);
    Route::post('/getUserType',[AuthController::class,'getUserType']);

    Route::get('/publications', [PublicationsController::class, 'index']);
    Route::post('/publication', [PublicationsController::class, 'store']);
    Route::get('/publicationsUser', [PublicationsController::class, 'userProfilePublications']);
    Route::get('/publications/{id}', [PublicationsController::class, 'show']);
    Route::get('/publications/{id}/edit', [PublicationsController::class, 'edit']);
    Route::put('/publications/{id}/edit', [PublicationsController::class, 'update']);
    Route::delete('/publications/{id}/', [PublicationsController::class, 'destroy']);

    Route::get('/search', [UserController::class, 'search']);
    Route::post('/uploadAvatar', [UserController::class, 'upload']);


});
