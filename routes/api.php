<?php
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

// Leave this file empty or remove the duplicate routes since they're in web.php

Route::get('/users/admins', [UserController::class, 'getAdmins'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookings/user/{userId}', [BookingController::class, 'getUserBookings']);
});



