<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
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
Route::post('register' , [AuthController::class , 'register']);
Route::post('login' , [AuthController::class , 'login']);
Route::post('logout' , [AuthController::class , 'logout'])->middleware(['auth:sanctum']);

// راوت لجلب التصنيفات مع الأقسام الفرعية
Route::get('/categories', [CategoryController::class, 'getCategoriesWithSections']);

// راوت لعرض قسم معين بناءً على المعرف
Route::get('/categories/{id}', [CategoryController::class, 'showCategory']);

// راوت للبحث عن الخبراء بناءً على التقييم
Route::get('/categories/{categoryId}/experts/searchByRating', [CategoryController::class, 'searchExpertsByRating']);

Route::get('/categories-with-sections', [CategoryController::class, 'getCategoriesWithSections']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();




});
