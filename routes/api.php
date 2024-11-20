<?php

use App\Http\Controllers\API\TransactionController;
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
Route::post('transaction/is-available', [TransactionController::class, 'isAvalilable'])->middleware(['auth:sanctum']);
Route::resource('transaction', TransactionController::class)->only(['store'])->middleware(['auth:sanctum']);
require __DIR__ . '/auth.php';
