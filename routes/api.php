<?php
use App\Http\Controllers\EncryptionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/encrypt', [EncryptionController::class, 'encryptData']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // We'll add our task routes here later
     // Add the task routes here:
     Route::apiResource('tasks', TaskController::class);
});