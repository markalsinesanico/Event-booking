<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\BookingController;

// API Routes
Route::group(['middleware' => ['web']], function () {
    // Public routes
    Route::post('/api/register', [UserController::class, 'register']);
    Route::post('/api/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/api/logout', [AuthController::class, 'logout']);
        Route::get('/api/user/profile', [UserController::class, 'getUserProfile']);
        Route::post('/api/user/profile/update', [UserController::class, 'updateProfile']);
        Route::post('/api/user/profile/image', [UserController::class, 'uploadProfileImage']);
        Route::get('/api/admin/customers', [UserController::class, 'getAllCustomers']);
        Route::delete('/api/admin/customers/{id}', [UserController::class, 'deleteCustomer']);
        Route::put('/api/admin/customers/{id}', [UserController::class, 'updateCustomer']);
        
        // Venue routes
        Route::get('/api/venues/admin/{adminId}', [VenueController::class, 'getVenuesByAdmin']);
        Route::get('/api/venues', [VenueController::class, 'index']);
        Route::post('/api/venues', [VenueController::class, 'store']);
        Route::put('/api/venues/{id}', [VenueController::class, 'update']);
        Route::delete('/api/venues/{id}', [VenueController::class, 'destroy']);

        // Admin dashboard routes
        Route::get('/api/admin/dashboard', [AdminDashboardController::class, 'getDashboardData']);
        Route::get('/api/admin/events', [AdminDashboardController::class, 'getEvents']);
        Route::put('/api/admin/events/{id}/approve', [AdminDashboardController::class, 'approveEvent']);
        Route::delete('/api/admin/events/{id}', [AdminDashboardController::class, 'deleteEvent']);

        Route::get('/users/admins', [UserController::class, 'getAdminUsers']);
        Route::get('/api/admin/users', [UserController::class, 'getAdminUsers']);

        Route::post('/api/booking', [BookingController::class, 'store']);
        Route::get('/api/user/booking', [BookingController::class, 'getUserBooking']);

        // Booking routes
        Route::get('/booking/user/{userId}', [BookingController::class, 'getUserBooking']);
        Route::post('/booking', [BookingController::class, 'store']);

        Route::middleware(['auth:sanctum', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
            Route::get('/api/admin/booking', [BookingController::class, 'getAdminBooking']);
            Route::put('/api/admin/booking/{id}/status', [BookingController::class, 'updateBookingStatus']);
            Route::delete('/api/admin/booking/{id}', [BookingController::class, 'deleteBooking']);
        });
    });
});

// Catch all routes and direct to the Vue router
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');