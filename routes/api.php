<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/initial-url-data', function () {
    $timerStart = microtime(true);

    // db query to get data
    $users = DB::table('users')->get();

    $timerStop = microtime(true);

    $diff = $timerStop - $timerStart;

    return response()->json([
        'message' => 'Hello, World!',
        'time' => $diff,
    ]);
});
