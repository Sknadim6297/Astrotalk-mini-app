<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Astrologer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:user,astrologer,admin',
                'languages' => 'nullable|array',
                'languages.*' => 'string',
                'specialization' => 'nullable|array',
                'specialization.*' => 'string',
                'experience' => 'nullable|integer|min:0',
                'per_minute_rate' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // If role is astrologer, create astrologer profile
            if ($request->role === 'astrologer') {
                $astrologerData = [
                    'user_id' => $user->id,
                    'wallet_balance' => 0.00,
                ];

                if ($request->has('languages')) {
                    $astrologerData['languages'] = $request->languages;
                }

                if ($request->has('specialization')) {
                    $astrologerData['specialization'] = $request->specialization;
                }

                if ($request->has('experience')) {
                    $astrologerData['experience'] = $request->experience;
                }

                if ($request->has('per_minute_rate')) {
                    $astrologerData['per_minute_rate'] = $request->per_minute_rate;
                }

                Astrologer::create($astrologerData);
            }

            DB::commit();

            $token = $user->createToken('auth-token')->plainTextToken;

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ];

            // Include astrologer data if applicable
            if ($user->role === 'astrologer' && $user->astrologerProfile) {
                $userData['astrologer_profile'] = [
                    'languages' => $user->astrologerProfile->languages,
                    'specialization' => $user->astrologerProfile->specialization,
                    'experience' => $user->astrologerProfile->experience,
                    'per_minute_rate' => $user->astrologerProfile->per_minute_rate,
                    'wallet_balance' => $user->astrologerProfile->wallet_balance,
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $userData,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user and return token
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Delete all existing tokens for this user
            $user->tokens()->delete();

            $token = $user->createToken('auth-token')->plainTextToken;

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ];

            // Include astrologer data if applicable
            if ($user->role === 'astrologer' && $user->astrologerProfile) {
                $userData['astrologer_profile'] = [
                    'languages' => $user->astrologerProfile->languages,
                    'specialization' => $user->astrologerProfile->specialization,
                    'experience' => $user->astrologerProfile->experience,
                    'per_minute_rate' => $user->astrologerProfile->per_minute_rate,
                    'wallet_balance' => $user->astrologerProfile->wallet_balance,
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'user' => $userData,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user and revoke token
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout successful'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user info
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];

            // Include astrologer data if applicable
            if ($user->role === 'astrologer' && $user->astrologerProfile) {
                $userData['astrologer_profile'] = [
                    'languages' => $user->astrologerProfile->languages,
                    'specialization' => $user->astrologerProfile->specialization,
                    'experience' => $user->astrologerProfile->experience,
                    'per_minute_rate' => $user->astrologerProfile->per_minute_rate,
                    'wallet_balance' => $user->astrologerProfile->wallet_balance,
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => $userData
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get user info',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Web-based login (session authentication)
     */
    public function webLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect based on user role
            switch ($user->role) {
                case 'user':
                    return redirect()->intended('/dashboard')->with('success', 'Welcome back!');
                case 'astrologer':
                    return redirect()->intended('/astrologer/dashboard')->with('success', 'Welcome back!');
                case 'admin':
                    return redirect()->intended('/admin/dashboard')->with('success', 'Welcome back!');
                default:
                    return redirect('/')->with('success', 'Welcome back!');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    /**
     * Web-based registration (session authentication)
     */
    public function webRegister(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:user,astrologer',
        ];

        // Add astrologer-specific validation rules
        if ($request->role === 'astrologer') {
            $validationRules = array_merge($validationRules, [
                'experience' => 'required|integer|min:0|max:50',
                'education' => 'required|string|max:255',
                'specialization' => 'required|array|min:1',
                'specialization.*' => 'string',
                'languages' => 'required|array|min:1',
                'languages.*' => 'string',
                'per_minute_rate' => 'required|numeric|min:10|max:1000',
                'bio' => 'nullable|string|max:1000',
                'certifications' => 'nullable|string|max:1000',
            ]);
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // If registering as astrologer, create astrologer profile with full details
            if ($request->role === 'astrologer') {
                Astrologer::create([
                    'user_id' => $user->id,
                    'specialization' => $request->specialization,
                    'languages' => $request->languages,
                    'experience' => $request->experience,
                    'per_minute_rate' => $request->per_minute_rate,
                    'education' => $request->education,
                    'bio' => $request->bio,
                    'certifications' => $request->certifications,
                    'status' => 'pending', // Pending approval by admin
                    'wallet_balance' => 0,
                ]);
            }

            DB::commit();

            Auth::login($user);

            // Redirect based on user role
            switch ($user->role) {
                case 'user':
                    return redirect('/dashboard')->with('success', 'Registration successful! Welcome to AstroConnect!');
                case 'astrologer':
                    return redirect('/astrologer/dashboard')->with('success', 'Registration successful! Your profile is pending admin approval.');
                default:
                    return redirect('/')->with('success', 'Registration successful!');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Admin login (session authentication)
     */
    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if user is admin
            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['error' => 'Access denied. Administrator privileges required.'])->withInput();
            }

            $request->session()->regenerate();
            
            return redirect()->intended('/admin/dashboard')->with('success', 'Admin login successful!');
        }

        return back()->withErrors(['error' => 'Invalid credentials. Please try again.'])->withInput();
    }
}
