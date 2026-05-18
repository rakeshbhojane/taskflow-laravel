<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Users (admin only)
    Route::middleware('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
    });

    // Projects
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{id}', [ProjectController::class, 'show']);
    Route::get('/projects/{id}/members', [ProjectController::class, 'members']);

    Route::middleware('admin')->group(function () {
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::put('/projects/{id}', [ProjectController::class, 'update']);
        Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);
        Route::post('/projects/{id}/members', [ProjectController::class, 'addMember']);
    });

    // Tasks
    Route::get('/projects/{projectId}/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/my-tasks', [TaskController::class, 'myTasks']);
    Route::get('/tasks/{id}', [TaskController::class, 'show']);
    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);

    Route::middleware('admin')->group(function () {
        Route::post('/projects/{projectId}/tasks', [TaskController::class, 'store']);
        Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    });
});
