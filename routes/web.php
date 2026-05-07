<?php

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-engine', [TestController::class, 'index'])->name('test-engine.index');
Route::post('/test-engine/fetch', [TestController::class, 'fetch'])->name('test-engine.fetch');
