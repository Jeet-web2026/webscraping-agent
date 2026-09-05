<?php

use App\Http\Controllers\ResearchRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ai submission
Route::post('/research-requests', [ResearchRequestController::class, 'store'])->name('ai-request.store');
Route::get('/research-requests/{researchRequest}', [ResearchRequestController::class, 'show'])->name('ai-request.show');;
