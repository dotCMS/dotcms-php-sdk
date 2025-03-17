<?php

use Illuminate\Support\Facades\Route;

// Catch-all route for rendering all paths with the same view
Route::fallback(function () {
    return view('app');
});