<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (){
    
    Route::get('/user', [AuthController::class, 'profil']);
    
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('category', CategoryController::class);

});