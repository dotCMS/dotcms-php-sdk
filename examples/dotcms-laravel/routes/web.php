<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;

// Catch-all route for rendering DotCMS pages
// This should only trigger for routes that might be DotCMS pages
// Static assets should be served directly from public directory
Route::fallback([AppController::class, 'index'])->where('fallbackPlaceholder', '^(?!.*\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)).*$');