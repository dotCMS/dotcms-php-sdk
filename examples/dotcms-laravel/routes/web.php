<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;

// Catch-all route for rendering all paths with the same view
Route::fallback([AppController::class, 'index']);