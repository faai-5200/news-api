<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::middleware([EnsureFrontendRequestsAreStateful::class])->get('/test', function () {
    return response()->json(['message' => 'Middleware works!']);
});
