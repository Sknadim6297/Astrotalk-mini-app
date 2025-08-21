<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Astrologer;
use App\Models\Review;

class AstrologerController extends Controller
{
    /**
     * Display a listing of all approved astrologers
     */
    public function index()
    {
        $astrologers = Astrologer::with(['user', 'reviews'])
            ->where('status', 'approved')
            ->get()
            ->map(function ($astrologer) {
                // normalize specialization & languages to arrays
                $specializations = [];
                if (is_array($astrologer->specialization)) {
                    $specializations = $astrologer->specialization;
                } elseif (is_string($astrologer->specialization) && strlen(trim($astrologer->specialization)) > 0) {
                    $specializations = array_map('trim', explode(',', $astrologer->specialization));
                } else {
                    $specializations = ['Vedic Astrology'];
                }

                $languages = [];
                if (is_array($astrologer->languages)) {
                    $languages = $astrologer->languages;
                } elseif (is_string($astrologer->languages) && strlen(trim($astrologer->languages)) > 0) {
                    $languages = array_map('trim', explode(',', $astrologer->languages));
                } else {
                    $languages = ['Hindi', 'English'];
                }

                return [
                    'id' => $astrologer->user_id,
                    'name' => $astrologer->user->name,
                    'rating' => round($astrologer->reviews->avg('rating') ?: 0, 1),
                    'reviews' => $astrologer->reviews->count(),
                    'experience' => $astrologer->experience ?: 0,
                    'rate' => $astrologer->per_minute_rate ?: 25,
                    'is_online' => true, // Default to online for demo
                    'specializations' => $specializations,
                    'languages' => $languages,
                    'bio' => $astrologer->bio ?: 'Experienced astrologer providing accurate predictions and guidance for all life matters.'
                ];
            });

        // If frontend requested JSON (apiCall), return JSON data
        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $astrologers
            ]);
        }

        return view('astrologer.index', compact('astrologers'));
    }
    /**
     * Show astrologer profile edit form
     */
    public function editProfile()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'astrologer') {
            return redirect('/')->with('error', 'Access denied.');
        }

        $astrologer = $user->astrologerProfile;

        if (!$astrologer) {
            // Create astrologer profile if it doesn't exist
            $astrologer = Astrologer::create([
                'user_id' => $user->id,
                'specialization' => [],
                'languages' => [],
                'experience' => 0,
                'per_minute_rate' => 25.00
            ]);
        }

        return view('astrologer.edit-profile', compact('user', 'astrologer'));
    }

    /**
     * Update astrologer profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'astrologer') {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $astrologer = $user->astrologerProfile;

        if (!$astrologer) {
            // Create astrologer profile if it doesn't exist
            $astrologer = Astrologer::create([
                'user_id' => $user->id,
                'specialization' => [],
                'languages' => [],
                'experience' => 0,
                'per_minute_rate' => 25.00
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'specialization' => 'required|array|min:1',
            'languages' => 'required|array|min:1',
            'experience' => 'required|integer|min:0|max:50',
            'per_minute_rate' => 'required|numeric|min:1|max:500',
            'education' => 'nullable|string|max:500',
            'certifications' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update user details
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            // Update astrologer details
            $astrologer->update([
                'bio' => $request->bio,
                'specialization' => $request->specialization,
                'languages' => $request->languages,
                'experience' => $request->experience,
                'per_minute_rate' => $request->per_minute_rate,
                'education' => $request->education,
                'certifications' => $request->certifications,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'data' => [
                    'user' => $user->fresh(),
                    'astrologer' => $astrologer->fresh()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show availability management
     */
    public function availability()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'astrologer') {
            return redirect('/')->with('error', 'Access denied.');
        }

        $astrologer = $user->astrologerProfile;

        if (!$astrologer) {
            // Create astrologer profile if it doesn't exist
            $astrologer = Astrologer::create([
                'user_id' => $user->id,
                'specialization' => [],
                'languages' => [],
                'experience' => 0,
                'per_minute_rate' => 25.00
            ]);
        }

        return view('astrologer.availability', compact('astrologer'));
    }

    /**
     * Update availability
     */
    public function updateAvailability(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'astrologer') {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $astrologer = $user->astrologerProfile;

        if (!$astrologer) {
            // Create astrologer profile if it doesn't exist
            $astrologer = Astrologer::create([
                'user_id' => $user->id,
                'specialization' => [],
                'languages' => [],
                'experience' => 0,
                'per_minute_rate' => 25.00
            ]);
        }

        // Normalize incoming availability payload so validation accepts common formats
        $input = $request->all();
        if (!empty($input['availability']) && is_array($input['availability'])) {
            foreach ($input['availability'] as $idx => $item) {
                if (!is_array($item)) continue;
                // normalize day to lowercase string
                if (isset($item['day'])) {
                    $input['availability'][$idx]['day'] = strtolower((string)$item['day']);
                }
                // coerce enabled-like values to boolean
                if (isset($item['enabled'])) {
                    $input['availability'][$idx]['enabled'] = filter_var($item['enabled'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    // ensure boolean fallback
                    $input['availability'][$idx]['enabled'] = $input['availability'][$idx]['enabled'] === null ? false : $input['availability'][$idx]['enabled'];
                }
                // ensure slots is an array if present
                if (isset($item['slots']) && !is_array($item['slots'])) {
                    $input['availability'][$idx]['slots'] = [];
                }
                if (!isset($input['availability'][$idx]['slots'])) {
                    $input['availability'][$idx]['slots'] = [];
                }
            }
        }

        // Coerce is_online
        if (isset($input['is_online'])) {
            $input['is_online'] = filter_var($input['is_online'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            $input['is_online'] = $input['is_online'] === null ? false : $input['is_online'];
        }

        $validator = Validator::make($input, [
            'availability' => 'required|array',
            'availability.*.day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'availability.*.enabled' => 'required|boolean',
            'availability.*.slots' => 'required_if:availability.*.enabled,true|array',
            'availability.*.slots.*.start_time' => 'required_with:availability.*.slots|date_format:H:i',
            'availability.*.slots.*.end_time' => 'required_with:availability.*.slots|date_format:H:i|after:availability.*.slots.*.start_time',
            'timezone' => 'nullable|string|max:50',
            'is_online' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $astrologer->update([
                'availability' => $input['availability'],
                'timezone' => $input['timezone'] ?? ($request->timezone ?? 'Asia/Kolkata'),
                'is_online' => $input['is_online'] ?? ($request->is_online ?? false),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Availability updated successfully!',
                'data' => $astrologer->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show public astrologer profile with ratings and reviews
     */
    public function showProfile($id)
    {
        $astrologer = Astrologer::with(['user', 'reviews.user'])
            ->where('user_id', $id)
            ->where('status', 'approved')
            ->first();

        if (!$astrologer) {
            abort(404, 'Astrologer not found or not approved');
        }

        // Calculate rating statistics
        $totalReviews = $astrologer->reviews->count();
        $averageRating = $totalReviews > 0 ? $astrologer->reviews->avg('rating') : 0;
        
        $ratingBreakdown = [
            5 => $astrologer->reviews->where('rating', 5)->count(),
            4 => $astrologer->reviews->where('rating', 4)->count(),
            3 => $astrologer->reviews->where('rating', 3)->count(),
            2 => $astrologer->reviews->where('rating', 2)->count(),
            1 => $astrologer->reviews->where('rating', 1)->count(),
        ];

        return view('astrologer.public-profile', compact(
            'astrologer', 
            'totalReviews', 
            'averageRating', 
            'ratingBreakdown'
        ));
    }

    /**
     * Submit a review for an astrologer
     */
    public function submitReview(Request $request, $astrologerId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'booking_id' => 'nullable|exists:bookings,id'
        ]);

        try {
            $userId = Auth::id();
            
            // Check if user has already reviewed this astrologer for this booking
            if ($request->booking_id) {
                $existingReview = Review::where('user_id', $userId)
                    ->where('astrologer_id', $astrologerId)
                    ->where('booking_id', $request->booking_id)
                    ->first();
                    
                if ($existingReview) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already reviewed this session'
                    ], 400);
                }
            } else {
                // For general reviews, check if user has reviewed this astrologer in last 30 days
                $recentReview = Review::where('user_id', $userId)
                    ->where('astrologer_id', $astrologerId)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->first();
                    
                if ($recentReview) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You can only review an astrologer once per month'
                    ], 400);
                }
            }

            // Check if the booking exists and user is authorized
            $isVerified = false;
            if ($request->booking_id) {
                $booking = \App\Models\Booking::where('id', $request->booking_id)
                    ->where('user_id', $userId)
                    ->where('astrologer_id', $astrologerId)
                    ->where('status', 'completed')
                    ->first();
                    
                $isVerified = $booking ? true : false;
            }

            $review = Review::create([
                'user_id' => $userId,
                'astrologer_id' => $astrologerId,
                'booking_id' => $request->booking_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_verified' => $isVerified
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully!',
                'data' => $review->load('user')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review: ' . $e->getMessage()
            ], 500);
        }
    }
}
