<?php

use App\Jobs\FetchProductWebDataJob;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    FetchProductWebDataJob::dispatch();
    return view('welcome');
});
