<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/tasks', [TaskController::class, 'getAllTasks']);
    Route::post('/tasks', [TaskController::class, 'addTask']);
    Route::put('/tasks/{id}', [TaskController::class, 'editTask']);
    Route::delete('/tasks/{id}', [TaskController::class, 'softDeleteTask']);
    Route::put('/tasks/{id}/toggle-status', [TaskController::class, 'toggleTaskStatus']);
    Route::put('/tasks/{id}/restore', [TaskController::class, 'restoreTask']);
    Route::get('/tasks/deleted', [TaskController::class, 'getDeletedTasks']);
    Route::post('/categories', [TaskController::class, 'addCategory']);
});
