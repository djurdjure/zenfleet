<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

    Route::middleware(['auth:sanctum', 'can:view assignments'])->prefix('admin')->name('api.admin.')->group(function () {
    Route::patch('assignments/{assignment}/move', [\App\Http\Controllers\Api\AssignmentController::class, 'move'])->name('assignments.move');
    Route::apiResource('assignments', \App\Http\Controllers\Api\AssignmentController::class)->only(['show', 'update', 'store', 'destroy']);
});