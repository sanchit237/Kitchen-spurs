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
    Route::get('/user-roles', [UserController::class, 'userRoles']);

    Route::prefix('categories')->controller(CategoryController::class)->group(function () {
        Route::post('create', 'createCategory');
        Route::get('list', 'getCategories');
        Route::get('detail/{id}', 'getCategory');
        Route::post('update/{id}', 'updateCategory');
        Route::delete('delete/{id}', 'deleteCategory');
    });


    Route::prefix('articles')->controller(ArticleController::class)->group(function () {
        Route::post('create', 'createArticle');
        Route::post('list', 'getArticles');
        Route::get('detail/{id}', 'getArticle');
        Route::post('update/{id}', 'updateArticle');
        Route::delete('delete/{id}', 'deleteArticle');
        Route::get('status', 'articleStatus');
    });
});
