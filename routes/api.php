<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ListingController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => ' Detail Login User Success',
        'data' => $request->user()
    ]);
});

Route::resource('listing', ListingController::class)->only(['index', 'show']);
require __DIR__ . '/auth.php';
