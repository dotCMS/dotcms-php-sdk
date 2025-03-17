<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app'); // Change 'app' to your desired view name
});

// Catch-all route for rendering all paths with the same view
Route::get('{any}', function () {
    return view('app'); // Change 'app' to the view you want to render
})->where('any', '.*'); // Regex to match everything
