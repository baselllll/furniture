<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use \App\Http\Controllers\ProductController;
use \App\Http\Controllers\CategoryController;



Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::resource('category', CategoryController::class);
Route::post('update-category/{id}', [CategoryController::class,'update']);

Route::get('specific-product/{id}', [ProductController::class,'show']);
Route::get('get-products', [ProductController::class,'index']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/user-profile', [UserController::class, 'userProfile']);
    Route::put('/update-user/{user_id}', [UserController::class, 'updateUser']);
    Route::group(['prefix'=> 'services'],function (){

        Route::resource('products', ProductController::class);
        Route::post('update-product/{id}', [ProductController::class,'update']);
        Route::get('products-logined-users', [ProductController::class,'ProductsLoginedUsers']);
        Route::get('products-featured', [ProductController::class,'ProductFeatured']);

        Route::get('get-products-By/{selectType}',[ProductController::class,'getproductsBy']);
    });


});
