<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Astrologer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AstrologerController extends Controller
{
    /**
     * Get list of astrologers with optional filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'language' => 'nullable|string',
                'specialization' => 'nullable|string',
                'min_experience' => 'nullable|integer|min:0',
                'max_price' => 'nullable|numeric|min:0',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = User::where('role', 'astrologer')
                        ->with('astrologerProfile')
                        ->whereHas('astrologerProfile', function ($q) {
                            $q->where('status', 'approved');
                        });

            // Apply filters
            if ($request->filled('language')) {
                $query->whereHas('astrologerProfile', function ($q) use ($request) {
                    $q->whereJsonContains('languages', $request->language);
                });
            }

            if ($request->filled('specialization')) {
                $query->whereHas('astrologerProfile', function ($q) use ($request) {
                    $q->whereJsonContains('specialization', $request->specialization);
                });
            }

            if ($request->filled('min_experience')) {
                $query->whereHas('astrologerProfile', function ($q) use ($request) {
                    $q->where('experience', '>=', $request->min_experience);
                });
            }

            if ($request->filled('max_price')) {
                $query->whereHas('astrologerProfile', function ($q) use ($request) {
                    $q->where('per_minute_rate', '<=', $request->max_price);
                });
            }

            $perPage = $request->get('per_page', 12);
            $astrologers = $query->paginate($perPage);

            $astrologerData = $astrologers->getCollection()->map(function ($user) {
                $profile = $user->astrologerProfile;

                // normalize arrays
                $languages = [];
                if (is_array($profile->languages)) {
                    $languages = $profile->languages;
                } elseif (is_string($profile->languages)) {
                    $languages = array_filter(array_map('trim', explode(',', $profile->languages)));
                }

                $specializations = [];
                if (is_array($profile->specialization)) {
                    $specializations = $profile->specialization;
                } elseif (is_string($profile->specialization)) {
                    $specializations = array_filter(array_map('trim', explode(',', $profile->specialization)));
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'languages' => $languages,
                    'specializations' => $specializations,
                    'experience' => $profile->experience ?? 0,
                    'per_minute_rate' => $profile->per_minute_rate ?? 0,
                    'rate' => $profile->per_minute_rate ?? 0,
                    'wallet_balance' => $profile->wallet_balance ?? 0,
                    'is_online' => $profile->is_online ?? true,
                    'rating' => $profile->reviews()->avg('rating') ?? 0,
                    'reviews' => $profile->reviews()->count() ?? 0,
                    'created_at' => $user->created_at,
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $astrologerData,
                'pagination' => [
                    'current_page' => $astrologers->currentPage(),
                    'last_page' => $astrologers->lastPage(),
                    'per_page' => $astrologers->perPage(),
                    'total' => $astrologers->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch astrologers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single astrologer by ID
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $user = User::where('role', 'astrologer')
                       ->where('id', $id)
                       ->with('astrologerProfile')
                       ->whereHas('astrologerProfile', function ($q) {
                           $q->where('status', 'approved');
                       })
                       ->first();

            if (!$user || !$user->astrologerProfile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Astrologer not found'
                ], 404);
            }

            $astrologerData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'languages' => $user->astrologerProfile->languages ?? [],
                'specialization' => $user->astrologerProfile->specialization ?? [],
                'experience' => $user->astrologerProfile->experience ?? 0,
                'per_minute_rate' => $user->astrologerProfile->per_minute_rate ?? 0,
                'wallet_balance' => $user->astrologerProfile->wallet_balance ?? 0,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $astrologerData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch astrologer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update astrologer profile (Admin only)
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'languages' => 'nullable|array',
                'languages.*' => 'string',
                'specialization' => 'nullable|array',
                'specialization.*' => 'string',
                'experience' => 'nullable|integer|min:0',
                'per_minute_rate' => 'nullable|numeric|min:0',
                'wallet_balance' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('role', 'astrologer')
                       ->where('id', $id)
                       ->with('astrologerProfile')
                       ->first();

            if (!$user || !$user->astrologerProfile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Astrologer not found'
                ], 404);
            }

            $updateData = array_filter($request->only([
                'languages', 'specialization', 'experience', 'per_minute_rate', 'wallet_balance'
            ]), function ($value) {
                return $value !== null;
            });

            $user->astrologerProfile->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Astrologer profile updated successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'languages' => $user->astrologerProfile->languages,
                    'specialization' => $user->astrologerProfile->specialization,
                    'experience' => $user->astrologerProfile->experience,
                    'per_minute_rate' => $user->astrologerProfile->per_minute_rate,
                    'wallet_balance' => $user->astrologerProfile->wallet_balance,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update astrologer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete astrologer (Admin only)
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $user = User::where('role', 'astrologer')
                       ->where('id', $id)
                       ->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Astrologer not found'
                ], 404);
            }

            $user->delete(); // This will cascade delete the astrologer profile too

            return response()->json([
                'status' => 'success',
                'message' => 'Astrologer deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete astrologer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get availability status for a specific astrologer (public)
     */
    public function getAvailabilityStatus($id): JsonResponse
    {
        try {
            $astrologer = Astrologer::where('id', $id)
                                   ->where('status', 'approved')
                                   ->with('user')
                                   ->first();

            if (!$astrologer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Astrologer not found'
                ], 404);
            }

            $availabilityStatus = $astrologer->getAvailabilityStatus();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'astrologer_id' => $astrologer->id,
                    'name' => $astrologer->user->name,
                    'is_online' => $astrologer->is_online,
                    'is_available_now' => $astrologer->isAvailableNow(),
                    'availability_status' => $availabilityStatus,
                    'last_seen_at' => $astrologer->last_seen_at?->diffForHumans(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get availability status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle online status for authenticated astrologer
     */
    public function toggleOnlineStatus(Request $request): JsonResponse
    {
        try {
            $astrologer = $request->user()->astrologerProfile;

            if (!$astrologer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Astrologer profile not found'
                ], 404);
            }

            $isOnline = $request->input('is_online');
            $astrologer->toggleOnlineStatus($isOnline);

            return response()->json([
                'status' => 'success',
                'message' => 'Online status updated successfully',
                'data' => [
                    'is_online' => $astrologer->fresh()->is_online,
                    'last_seen_at' => $astrologer->fresh()->last_seen_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update online status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set today's availability schedule
     */
    public function setTodayAvailability(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'slots' => 'required|array',
                'slots.*.start_time' => 'required|date_format:H:i',
                'slots.*.end_time' => 'required|date_format:H:i|after:slots.*.start_time',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $astrologer = $request->user()->astrologerProfile;

            if (!$astrologer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Astrologer profile not found'
                ], 404);
            }

            $astrologer->setTodayAvailability($request->input('slots'));

            return response()->json([
                'status' => 'success',
                'message' => 'Today\'s availability updated successfully',
                'data' => [
                    'today_availability' => $astrologer->fresh()->today_availability
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update today\'s availability',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle "available now" status
     */
    public function toggleAvailableNow(Request $request): JsonResponse
    {
        try {
            $astrologer = $request->user()->astrologerProfile;

            if (!$astrologer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Astrologer profile not found'
                ], 404);
            }

            $isAvailableNow = $request->input('is_available_now', !$astrologer->is_available_now);

            $astrologer->update([
                'is_available_now' => $isAvailableNow,
                'last_seen_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Available now status updated successfully',
                'data' => [
                    'is_available_now' => $astrologer->fresh()->is_available_now,
                    'is_online' => $astrologer->is_online,
                    'availability_status' => $astrologer->fresh()->getAvailabilityStatus()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update available now status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user's availability status (for astrologers)
     */
    public function getMyAvailabilityStatus(Request $request): JsonResponse
    {
        try {
            $astrologer = $request->user()->astrologerProfile;

            if (!$astrologer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Astrologer profile not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'astrologer_id' => $astrologer->id,
                    'is_online' => $astrologer->is_online,
                    'is_available_now' => $astrologer->is_available_now,
                    'availability_status' => $astrologer->getAvailabilityStatus(),
                    'today_availability' => $astrologer->getTodayAvailability(),
                    'weekly_availability' => $astrologer->weekly_availability,
                    'last_seen_at' => $astrologer->last_seen_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get availability status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
