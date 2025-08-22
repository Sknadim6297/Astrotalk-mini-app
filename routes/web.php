<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\WalletWebController;
use App\Http\Controllers\AstrologerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\ChatController;

Route::get('/', function () {
    return view('welcome_new');
})->name('home');

// WebSocket test route
Route::get('/test-websocket', function () {
    return view('test-websocket');
})->name('test-websocket');

// Broadcast test event
Route::get('/test-broadcast', function () {
    broadcast(new \Illuminate\Broadcasting\Channel('test-channel'), 'test-event', [
        'message' => 'Hello from Laravel Reverb!',
        'timestamp' => now()->toISOString()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Test event broadcasted to test-channel'
    ]);
})->name('test-broadcast');

// Demo page
Route::get('/demo', function () {
    return view('demo');
})->name('demo');

// Test broadcasting auth
Route::get('/test-auth', function () {
    return response()->json([
        'authenticated' => Auth::check(),
        'user_id' => Auth::id(),
        'user_role' => Auth::user()?->role,
        'csrf_token' => csrf_token(),
        'session_id' => session()->getId()
    ]);
})->name('test-auth');

// Test message broadcast
Route::get('/test-message-broadcast/{bookingId}', function ($bookingId) {
    $message = \App\Models\ChatMessage::where('booking_id', $bookingId)->latest()->first();
    if ($message) {
        broadcast(new \App\Events\MessageSent($message));
        return response()->json([
            'success' => true,
            'message' => 'Broadcasted message: ' . $message->message,
            'booking_id' => $bookingId,
            'channel' => 'chat.' . $bookingId
        ]);
    }
    return response()->json(['error' => 'No messages found for booking ' . $bookingId]);
})->name('test-message-broadcast');

// Debug chat page
Route::get('/debug-chat', function () {
    return view('debug-chat');
})->name('debug-chat');

// Availability management for astrologers (protected route)
Route::middleware(['auth', 'verified'])->get('/availability-management', function () {
    // Ensure only astrologers can access this page
    if (Auth::user()->role !== 'astrologer') {
        abort(403, 'Access denied. This page is for astrologers only.');
    }
    return view('availability-management');
})->name('availability-management');

// Astrologer profile with availability
Route::get('/astrologer/{id}/profile', function ($id) {
    $astrologer = \App\Models\Astrologer::where('id', $id)
                                       ->where('status', 'approved')
                                       ->with('user')
                                       ->firstOrFail();
    
    return view('astrologer.profile', compact('astrologer'));
})->name('astrologer.profile');

// Test availability system
Route::get('/test-availability', function () {
    $astrologer = \App\Models\Astrologer::first();
    
    if (!$astrologer) {
        return response()->json(['error' => 'No astrologers found']);
    }
    
    // Set test availability
    $astrologer->update([
        'is_online' => true,
        'is_available_now' => true,
        'today_availability' => [
            ['start_time' => '09:00', 'end_time' => '12:00'],
            ['start_time' => '14:00', 'end_time' => '18:00']
        ],
        'last_seen_at' => now()
    ]);
    
    $status = $astrologer->fresh()->getAvailabilityStatus();
    
    return response()->json([
        'astrologer' => $astrologer->user->name,
        'is_online' => $astrologer->fresh()->is_online,
        'is_available_now' => $astrologer->fresh()->is_available_now,
        'availability_check' => $astrologer->fresh()->isAvailableNow(),
        'status' => $status,
        'today_availability' => $astrologer->fresh()->today_availability
    ]);
})->name('test-availability');

// Availability system demo
Route::get('/availability-demo', function () {
    return view('availability-demo');
})->name('availability-demo');

// Authentication routes (public)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/auth/login', function () {
    return view('auth.login');
})->name('auth.login');

Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'webLogin'])->name('login.post');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/auth/register', function () {
    return view('auth.register');
})->name('auth.register');

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'webRegister'])->name('register.post');

// Logout route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    if (request()->expectsJson()) {
        return response()->json(['success' => true]);
    }
    return redirect('/');
})->name('logout');

// Admin login route (route-only access)
Route::get('/admin/login', function () {
    return view('auth.admin-login');
});

// Admin login POST route
Route::post('/admin/login', [\App\Http\Controllers\Api\AuthController::class, 'adminLogin'])->name('admin.login.post');

// Public astrologers routes
Route::get('/astrologers', [AstrologerController::class, 'index'])->name('astrologers.index');

// Public astrologer profile view
Route::get('/astrologer/profile/{id}', [AstrologerController::class, 'showProfile'])->name('astrologer.public-profile');

// Review submission route
Route::post('/astrologer/review/{id}', [AstrologerController::class, 'submitReview'])->name('astrologer.submit-review');

// General dashboard route (redirects based on role)
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    switch ($user->role) {
        case 'user':
            return view('dashboard', ['requiredRole' => 'user']);
        case 'astrologer':
            return redirect('/astrologer/dashboard');
        case 'admin':
            return redirect('/admin/dashboard');
        default:
            return redirect('/');
    }
})->name('dashboard');

// Protected User routes
Route::middleware(['auth'])->group(function () {
    // Broadcasting Authentication
    Broadcast::routes();

    Route::get('/dashboard', function () {
        return view('dashboard', ['requiredRole' => 'user']);
    })->name('user.dashboard');

    Route::get('/profile', function () {
        return view('user.profile', ['requiredRole' => null]);
    })->name('user.profile');

    Route::get('/wallet', function () {
        return redirect('/wallet/dashboard');
    });

    // Wallet management routes (Users only)
    Route::get('/wallet/dashboard', [WalletWebController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/add-money', [WalletWebController::class, 'addMoney'])->name('wallet.add-money');
    Route::post('/wallet/add', [WalletWebController::class, 'store'])->name('wallet.add');
    Route::get('/wallet/data', [WalletWebController::class, 'data'])->name('wallet.data');
    Route::get('/wallet/stats', [WalletWebController::class, 'stats'])->name('wallet.stats');
    Route::get('/wallet/transactions', [WalletWebController::class, 'transactions'])->name('wallet.transactions');
    Route::get('/wallet/transactions-data', [WalletWebController::class, 'transactionsData'])->name('wallet.transactions-data');

    // Booking route - updated to use new booking system
    Route::get('/book-astrologer/{id}', function ($id) {
        $astrologer = \App\Models\Astrologer::with('user')->where('user_id', $id)->firstOrFail();
        return view('user.book-astrologer-new', compact('astrologer'), ['requiredRole' => 'user']);
    });

    // Booking POST (use session-based auth so web users don't get "Unauthenticated." from API routes)
    Route::post('/book-astrologer', [\App\Http\Controllers\BookingController::class, 'create'])->name('web.book-astrologer');

    // User appointments route
    Route::get('/appointments', [\App\Http\Controllers\BookingController::class, 'userAppointments'])->name('user.appointments');

    // Web booking details endpoint (session auth)
    Route::get('/bookings/{id}/details', [\App\Http\Controllers\BookingController::class, 'getBookingDetails'])->name('web.booking-details');

    // Web user bookings API endpoint (session auth)
    Route::get('/my-bookings', [\App\Http\Controllers\BookingController::class, 'getUserBookingsWeb'])->name('web.my-bookings');

    // Session management (web routes with session auth)
    Route::post('/bookings/{id}/start', [\App\Http\Controllers\BookingController::class, 'startSessionWeb'])->name('web.start-session');
    Route::post('/bookings/{id}/cancel', [\App\Http\Controllers\BookingController::class, 'cancelBookingWeb'])->name('web.cancel-booking');
    Route::post('/bookings/{id}/end', [\App\Http\Controllers\BookingController::class, 'endSession'])->name('web.end-session');

    // Chat interface route (for active bookings)
    Route::get('/chat/{bookingId}', [\App\Http\Controllers\Api\ChatController::class, 'getChatInterface'])->name('chat.interface');
    // Chat data routes (session-authenticated for web clients)
    Route::get('/chat/{bookingId}/messages', [\App\Http\Controllers\Api\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/{bookingId}/send', [\App\Http\Controllers\Api\ChatController::class, 'sendMessage'])->name('chat.send');
});

// Protected Astrologer routes
Route::middleware(['astrologer'])->group(function () {
    Route::get('/astrologer/dashboard', function () {
        return view('astrologer.dashboard');
    })->name('astrologer.dashboard');

    Route::get('/astrologer/bookings', [\App\Http\Controllers\BookingController::class, 'astrologerBookings'])->name('astrologer.bookings');

    Route::get('/astrologer/edit-profile', [AstrologerController::class, 'editProfile'])->name('astrologer.edit-profile');
    Route::post('/astrologer/update-profile', [AstrologerController::class, 'updateProfile'])->name('astrologer.update-profile');

    // Astrologer availability management
    Route::get('/astrologer/availability', [AstrologerController::class, 'availability'])->name('astrologer.availability');
    Route::post('/astrologer/update-availability', [AstrologerController::class, 'updateAvailability'])->name('astrologer.update-availability');

    // Astrologer reviews
    Route::get('/astrologer/reviews', function () {
        return view('astrologer.reviews');
    })->name('astrologer.reviews');
});

// Public admin dashboard entry: show admin login to unauthenticated visitors and only allow admins to view the dashboard
Route::get('/admin/dashboard', function () {
    // If user is not authenticated, show the admin login form (separate admin login flow)
    if (!Auth::check()) {
        return redirect('/admin/login');
    }

    // If authenticated but not an admin, redirect to their dashboard
    $user = Auth::user();
    if ($user->role !== 'admin') {
        return redirect('/dashboard');
    }

    // Authenticated admin: delegate to controller
    return app(\App\Http\Controllers\AdminController::class)->dashboard();
})->name('admin.dashboard');

// Protected Admin routes
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/astrologers', [\App\Http\Controllers\AdminController::class, 'astrologers'])->name('admin.astrologers');
    Route::get('/admin/bookings', [\App\Http\Controllers\AdminController::class, 'bookings'])->name('admin.bookings');

    // Astrologer management AJAX routes
    Route::post('/admin/astrologers/{id}/approve', [\App\Http\Controllers\AdminController::class, 'approveAstrologer'])->name('admin.astrologers.approve');
    Route::post('/admin/astrologers/{id}/reject', [\App\Http\Controllers\AdminController::class, 'rejectAstrologer'])->name('admin.astrologers.reject');
    Route::post('/admin/astrologers/{id}/deactivate', [\App\Http\Controllers\AdminController::class, 'deactivateAstrologer'])->name('admin.astrologers.deactivate');
    Route::post('/admin/astrologers/{id}/reactivate', [\App\Http\Controllers\AdminController::class, 'reactivateAstrologer'])->name('admin.astrologers.reactivate');
    Route::get('/admin/astrologers/{id}/details', [\App\Http\Controllers\AdminController::class, 'getAstrologerDetails'])->name('admin.astrologers.details');

    // User management
    Route::delete('/admin/users/{id}', [\App\Http\Controllers\AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('/admin/users/{id}/details', [\App\Http\Controllers\AdminController::class, 'getUserDetails'])->name('admin.users.details');

    // Chat conversation management
    Route::get('/admin/conversations/{bookingId}', [AdminController::class, 'viewConversation'])->name('admin.conversations.view');
    Route::get('/admin/conversations/{bookingId}/messages', [AdminController::class, 'getConversationMessages'])->name('admin.conversations.messages');
});

// Chat routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat/{bookingId}', [ChatController::class, 'getChatInterface'])->name('chat.interface');
    Route::get('/chat/{bookingId}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/{bookingId}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
});

// Broadcasting authentication
Route::post('/broadcasting/auth', function () {
    return response()->json(['status' => 'ok']);
})->middleware('auth');

// Debug authentication status
Route::get('/debug-auth', function () {
    $user = Auth::user();
    return response()->json([
        'authenticated' => Auth::check(),
        'user' => $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ] : null,
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token()
    ]);
});

// Debug route (remove in production)
// require __DIR__.'/debug.php';
