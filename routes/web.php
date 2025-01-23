<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Spotify\SpotifyController;

Route::get('/spotify/auth', [SpotifyController::class, 'redirectToSpotify'])->name('spotify.auth');
Route::get('/spotify/callback', [SpotifyController::class, 'handleCallback'])->name('spotify.callback');
