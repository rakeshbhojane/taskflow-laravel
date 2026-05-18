<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'TaskFlow API — Laravel Backend',
        'version' => '1.0.0',
        'docs'    => '/api',
    ]);
});
