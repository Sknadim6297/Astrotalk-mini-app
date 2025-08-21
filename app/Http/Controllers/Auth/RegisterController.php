<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Astrologer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:user,astrologer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // If registering as astrologer, create astrologer profile
        if ($request->role === 'astrologer') {
            Astrologer::create([
                'user_id' => $user->id,
                'specialization' => [],
                'languages' => [],
                'experience' => 0,
                'per_minute_rate' => 0,
            ]);
        }

        Auth::login($user);

        // Redirect based on user role
        switch ($user->role) {
            case 'user':
                return redirect('/dashboard')->with('success', 'Registration successful! Welcome to AstroConnect!');
            case 'astrologer':
                return redirect('/astrologer/dashboard')->with('success', 'Registration successful! Please complete your profile.');
            default:
                return redirect('/')->with('success', 'Registration successful!');
        }
    }
}
