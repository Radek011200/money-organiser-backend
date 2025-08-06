<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\UserController;

// Trasy API dla zarządzania wydatkami
Route::middleware('api')->group(function () {
    // Expenses routes
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses/{id}', [ExpenseController::class, 'show']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);

    // Categories routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/income', [CategoryController::class, 'income']);
    Route::get('/categories/expense', [CategoryController::class, 'expense']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Deposits routes
    Route::get('/deposits', [DepositController::class, 'index']);
    Route::post('/deposits', [DepositController::class, 'store']);
    Route::get('/deposits/{id}', [DepositController::class, 'show']);
    Route::put('/deposits/{id}', [DepositController::class, 'update']);
    Route::delete('/deposits/{id}', [DepositController::class, 'destroy']);

    // Statistics routes
    Route::get('/statistics', [StatisticsController::class, 'getStatistics']);

    // User routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::get('/user/profile', [UserController::class, 'edit']);
        Route::put('/user/profile', [UserController::class, 'update']);
        Route::put('/user/password', [UserController::class, 'updatePassword']);
    });
});
