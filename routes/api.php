<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArticleController;



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


Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    Route::prefix('categories')->controller(CategoryController::class)->group(function () {
        Route::post('/', 'createCategory');
        Route::get('/', 'getCategories');
        Route::get('{id}', 'getCategory');
        Route::post('{id}', 'updateCategory');
        Route::delete('{id}', 'deleteCategory');
    });


    Route::prefix('articles')->controller(ArticleController::class)->group(function () {
        Route::post('/', 'createArticle');
        Route::get('/', 'getArticles');
        Route::get('{id}', 'getArticle');
        Route::post('{id}', 'updateArticle');
        Route::delete('{id}', 'deleteArticle');
    });
});
