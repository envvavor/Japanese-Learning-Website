<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KanjiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/kanjis', [KanjiController::class, 'index']);
Route::get('/kanjis/{character}', [KanjiController::class, 'show']);
Route::post('/kanjis', [KanjiController::class, 'store']);