<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use \App\Http\Controllers\ProductController;



Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/user-profile', [UserController::class, 'userProfile']);
    Route::group(['prefix'=> 'services'],function (){
        Route::resource('products', ProductController::class);
        Route::get('products-logined-users', [ProductController::class,'ProductsLoginedUsers']);
        Route::get('products-featured', [ProductController::class,'ProductFeatured']);
    });


});
