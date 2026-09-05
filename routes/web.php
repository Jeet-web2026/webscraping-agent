<?php

use App\Jobs\FetchProductWebDataJob;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ai submission
Route::post('/', function () {
    FetchProductWebDataJob::dispatch();
    return redirect()->back()->with('success', 'Your request has been submitted successfully!');
});
