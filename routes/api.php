<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AstrologerController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Public astrologer routes
Route::get('/astrologers', [AstrologerController::class, 'index']);
Route::get('/astrologers/{id}', [AstrologerController::class, 'show']);
Route::get('/astrologers/{id}/availability-status', [AstrologerController::class, 'getAvailabilityStatus']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
    
    // User info route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Wallet routes (Users only)
    Route::middleware('role:user')->prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'getBalance']);
        Route::post('/add', [WalletController::class, 'addBalance']);
        Route::get('/transactions', [WalletController::class, 'getTransactions']);
        Route::get('/stats', [WalletController::class, 'getStats']);
    });

    // User wallet data (for any authenticated user)
    Route::get('/user/wallet', [WalletController::class, 'getUserWallet']);

    // Debug route to test authentication
    Route::get('/auth/test', function (Request $request) {
        return response()->json([
            'authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'user' => Auth::user(),
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
        ]);
    });

    // Booking endpoints (Using API Controller consistently)
    Route::get('/astrologers/{id}/availability', [\App\Http\Controllers\BookingController::class, 'getAvailability']);
    
    // User-only booking endpoints
    Route::middleware('role:user')->group(function () {
        Route::post('/bookings', [BookingController::class, 'book']);
    });
    
    // Booking management endpoints (any authenticated user)
    Route::post('/bookings/{id}/end', [\App\Http\Controllers\BookingController::class, 'endSession']);
    Route::get('/bookings/{id}/details', [\App\Http\Controllers\BookingController::class, 'getBookingDetails']);

    // Additional Booking routes
    Route::prefix('bookings')->group(function () {
        // User routes
        Route::middleware('role:user')->group(function () {
            Route::get('/my-bookings', [BookingController::class, 'getUserBookings']);
            Route::post('/{bookingId}/start', [BookingController::class, 'startSession']);
            Route::post('/{bookingId}/cancel', [BookingController::class, 'cancelBooking']);
        });
        
        // Astrologer routes
        Route::middleware('role:astrologer')->group(function () {
            Route::get('/astrologer-bookings', [BookingController::class, 'getAstrologerBookings']);
            Route::post('/{bookingId}/start', [BookingController::class, 'startSession']);
            Route::post('/{bookingId}/end', [BookingController::class, 'endSession']);
        });
        
        // Common routes (both user and astrologer)
        Route::get('/{bookingId}', [BookingController::class, 'getBooking']);
    });

    // Astrologer availability management
    Route::middleware('role:astrologer')->prefix('astrologer')->group(function () {
        Route::post('/availability/toggle-online', [AstrologerController::class, 'toggleOnlineStatus']);
        Route::post('/availability/set-today', [AstrologerController::class, 'setTodayAvailability']);
        Route::post('/availability/toggle-available-now', [AstrologerController::class, 'toggleAvailableNow']);
        Route::get('/availability/status', [AstrologerController::class, 'getMyAvailabilityStatus']);
    });

    // Chat routes (for active bookings only)
    Route::prefix('chat')->group(function () {
        Route::get('/{bookingId}/messages', [ChatController::class, 'getMessages']);
        Route::post('/{bookingId}/send', [ChatController::class, 'sendMessage']);
    });

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        Route::put('/astrologers/{id}', [AstrologerController::class, 'update']);
        Route::delete('/astrologers/{id}', [AstrologerController::class, 'destroy']);
    });
});

// Health check route
Route::get('/health', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working',
        'timestamp' => now(),
    ]);
});

// Public debug endpoint: shows incoming headers, cookies and session/auth info
Route::get('/debug/request', function (Request $request) {
    return response()->json([
        'received_host' => $request->getHost(),
        'received_origin' => $request->headers->get('Origin'),
        'received_referer' => $request->headers->get('Referer'),
        'auth_check' => auth()->check(),
        'auth_user_id' => auth()->id(),
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
        'cookies' => $request->cookies->all(),
        'headers' => array_intersect_key($request->headers->all(), array_flip([
            'host','origin','referer','cookie','x-csrf-token','x-xsrf-token','user-agent'
        ]))
    ]);
});
