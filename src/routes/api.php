<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts-optimized', [PostController::class, 'optimized']);
