<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UrlController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EmotionController;

Route::get('/initial-url-data', UrlController::class . '@single');

Route::get('/api/comments', CommentController::class . '@get');
Route::post('/api/comments', CommentController::class . '@add');

Route::post('/api/emotions[/]', EmotionController::class . '@add');
