<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FinancialInsightController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
        Route::get('/insights', [FinancialInsightController::class, 'index']);
        Route::post('/insights', [FinancialInsightController::class, 'store']);
        Route::get('/insights/{insight}', [FinancialInsightController::class, 'show']);
        Route::put('/insights/{insight}', [FinancialInsightController::class, 'update']);
        Route::delete('/insights/{insight}', [FinancialInsightController::class, 'destroy']);
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        Route::get('/budgets', [BudgetController::class, 'index']);
        Route::post('/budgets', [BudgetController::class, 'store']);
        Route::get('/budgets/{budget}', [BudgetController::class, 'show']);
        Route::put('/budgets/{budget}', [BudgetController::class, 'update']);
        Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy']);

        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::post('/transactions', [TransactionController::class, 'store']);
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);
        Route::put('/transactions/{transaction}', [TransactionController::class, 'update']);
        Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']);

        Route::get('/reports/summary', [ReportController::class, 'summary']);
        Route::get('/reports/chart-data', [ReportController::class, 'chartData']);
    });
});
