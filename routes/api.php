<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WalletsController;
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
Route::post('auth', [AuthController::class, 'auth']);

Route::post('/user/store', [UsersController::class, 'store']);
Route::prefix('user')->group(function () {
    Route::put('update', [UsersController::class, 'update']);
    Route::get('{id}', [UsersController::class, 'get'])->where('id', '[0-9]+');
    Route::get('filters/{value?}', [UsersController::class, 'filters'])->where('value', '.*');
});


Route::prefix('wallet')->group(function () {
    Route::post('self', [WalletsController::class, 'selfCredit']);
    Route::post('outer', [WalletsController::class, 'outerCredit']);
});
