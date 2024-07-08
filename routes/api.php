<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UrlController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EmotionController;
use App\Http\Controllers\SitemapController;

Route::get('/sitemap', SitemapController::class . '@get');

Route::get('/initial-url-data', UrlController::class . '@single');

Route::get('/comments', CommentController::class . '@get');
Route::post('/comments', CommentController::class . '@add');

Route::post('/emotions', EmotionController::class . '@add');
