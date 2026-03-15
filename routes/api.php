<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KanjiController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\AiValidationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/kanjis', [KanjiController::class, 'index']);
Route::get('/kanjis/{character}', [KanjiController::class, 'show']);
Route::post('/kanjis', [KanjiController::class, 'store']);

// Route untuk menerima kiriman gambar Auto-Save dari JavaScript
Route::post('/dataset/save', [DatasetController::class, 'store']);

// Endpoint untuk Jembatan AI
Route::post('/validate-ai', [AiValidationController::class, 'check']);