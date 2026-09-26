<?php

use App\Http\Controllers\ResearchRequestController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    Artisan::call('queue:work', ['--stop-when-empty' => true]);
    return view('welcome');
});

// Ai submission
Route::post('/research-requests', [ResearchRequestController::class, 'store'])->name('ai-request.store');
Route::get('/research-requests/{researchRequest}', [ResearchRequestController::class, 'show'])->name('ai-request.show');
Route::get('research-requests/{researchRequest}/download', [ResearchRequestController::class, 'download'])
    ->name('research-requests.download')
    ->middleware('signed');
