<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\InvoiceController;

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    

    Route::post('/logout', [LoginController::class, 'logout']);
});

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::apiResource('/clients', ClientController::class);
Route::apiResource('/invoices', InvoiceController::class);

Route::put('/clients/{id}', [ClientController::class, 'update']);



