@extends('layouts.app')

@section('title', '{{ $astrologer->user->name }} - Profile')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="/astrologers" class="inline-flex items-center text-purple-600 hover:text-purple-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Astrologers
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Profile -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Header -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 p-6 text-white">
                        <div class="flex items-start space-x-6">
                            <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-4xl text-white"></i>
                            </div>
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold mb-2">{{ $astrologer->user->name }}</h1>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @if($astrologer->specialization)
                                        @foreach($astrologer->specialization as $spec)
                                            <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">{{ $spec }}</span>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="flex items-center space-x-6 text-white/90">
                                    <span class="flex items-center">
                                        <i class="fas fa-clock mr-2"></i>
                                        {{ $astrologer->experience }} years experience
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-star mr-2"></i>
                                        {{ number_format($averageRating, 1) }} ({{ $totalReviews }} reviews)
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-rupee-sign mr-2"></i>
                                        ₹{{ $astrologer->per_minute_rate }}/min
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Bar -->
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                @if($astrologer->is_online)
                                    <span class="flex items-center text-green-600 font-medium">
                                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                        Online Now
                                    </span>
                                @else
                                    <span class="flex items-center text-gray-500 font-medium">
                                        <div class="w-3 h-3 bg-gray-400 rounded-full mr-2"></div>
                                        Offline
                                    </span>
                                @endif
                                
                                @if($astrologer->languages)
                                    <span class="text-gray-600">
                                        <i class="fas fa-language mr-1"></i>
                                        {{ implode(', ', array_slice($astrologer->languages, 0, 3)) }}
                                        @if(count($astrologer->languages) > 3)
                                            +{{ count($astrologer->languages) - 3 }} more
                                        @endif
                                    </span>
                                @endif
                            </div>
                            
                            <div class="flex space-x-3">
                                <a href="/book-astrologer/{{ $astrologer->user_id }}" 
                                   class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                                    <i class="fas fa-comments mr-2"></i>Chat Now
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- About Section -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">About</h3>
                        <p class="text-gray-700 leading-relaxed">
                            {{ $astrologer->bio ?? 'Experienced astrologer providing accurate predictions and guidance for all life matters. Book a session to get personalized insights about your future.' }}
                        </p>
                        
                        @if($astrologer->education || $astrologer->certifications)
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if($astrologer->education)
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-2">
                                            <i class="fas fa-graduation-cap text-blue-600 mr-2"></i>Education
                                        </h4>
                                        <p class="text-gray-700">{{ $astrologer->education }}</p>
                                    </div>
                                @endif
                                
                                @if($astrologer->certifications)
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-2">
                                            <i class="fas fa-certificate text-yellow-600 mr-2"></i>Certifications
                                        </h4>
                                        <p class="text-gray-700">{{ $astrologer->certifications }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-comments text-blue-600 mr-2"></i>
                            Client Reviews ({{ $totalReviews }})
                        </h3>
                    </div>

                    @if($totalReviews > 0)
                        <!-- Rating Summary -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-start space-x-6">
                                <div class="text-center">
                                    <div class="text-4xl font-bold text-gray-900 mb-1">{{ number_format($averageRating, 1) }}</div>
                                    <div class="flex items-center justify-center mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($averageRating))
                                                <i class="fas fa-star text-yellow-500"></i>
                                            @elseif($i <= ceil($averageRating))
                                                <i class="fas fa-star-half-alt text-yellow-500"></i>
                                            @else
                                                <i class="far fa-star text-gray-300"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <div class="text-sm text-gray-600">{{ $totalReviews }} reviews</div>
                                </div>
                                
                                <div class="flex-1 space-y-2">
                                    @for($rating = 5; $rating >= 1; $rating--)
                                        <div class="flex items-center space-x-3">
                                            <span class="text-sm text-gray-600 w-8">{{ $rating }}★</span>
                                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                <div class="bg-yellow-500 h-2 rounded-full" 
                                                     style="width: {{ $totalReviews > 0 ? ($ratingBreakdown[$rating] / $totalReviews) * 100 : 0 }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600 w-8">{{ $ratingBreakdown[$rating] }}</span>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Individual Reviews -->
                        <div class="divide-y divide-gray-200">
                            @foreach($astrologer->reviews()->with('user')->orderBy('created_at', 'desc')->limit(10)->get() as $review)
                                <div class="p-6">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-purple-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <div>
                                                    <h4 class="font-semibold text-gray-900">{{ $review->user->name }}</h4>
                                                    <div class="flex items-center space-x-2">
                                                        <div class="flex">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= $review->rating)
                                                                    <i class="fas fa-star text-yellow-500 text-sm"></i>
                                                                @else
                                                                    <i class="far fa-star text-gray-300 text-sm"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                        <span class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
                                                        @if($review->is_verified)
                                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Verified</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if($review->comment)
                                                <p class="text-gray-700 leading-relaxed">{{ $review->comment }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($totalReviews > 10)
                            <div class="p-6 text-center border-t border-gray-200">
                                <button class="text-purple-600 hover:text-purple-700 font-medium">
                                    Load More Reviews
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="p-12 text-center">
                            <i class="fas fa-star text-gray-300 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Reviews Yet</h3>
                            <p class="text-gray-600">Be the first to book and review this astrologer!</p>
                        </div>
                    @endif
                </div>

                <!-- Add Review Section (for authenticated users) -->
                @auth
                <div class="bg-white rounded-xl shadow-lg overflow-hidden" x-data="reviewForm()">
                    <div class="px-6 py-4 bg-purple-50 border-b border-purple-200">
                        <h3 class="text-lg font-semibold text-purple-900">
                            <i class="fas fa-star text-purple-600 mr-2"></i>Write a Review
                        </h3>
                    </div>
                    <div class="p-6">
                        <form @submit.prevent="submitReview()">
                            <!-- Rating -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                <div class="flex items-center space-x-1">
                                    <template x-for="star in 5" :key="star">
                                        <button type="button" @click="rating = star" 
                                                class="focus:outline-none transition-colors"
                                                :class="star <= rating ? 'text-yellow-500' : 'text-gray-300'">
                                            <i class="fas fa-star text-2xl"></i>
                                        </button>
                                    </template>
                                    <span class="ml-3 text-sm text-gray-600" x-show="rating > 0">
                                        <span x-text="rating"></span> out of 5 stars
                                    </span>
                                </div>
                                <div x-show="errors.rating" class="text-red-500 text-sm mt-1" x-text="errors.rating"></div>
                            </div>

                            <!-- Comment -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Review (Optional)</label>
                                <textarea x-model="comment" rows="4" 
                                          placeholder="Share your experience with this astrologer..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-purple-500 focus:ring-purple-500"></textarea>
                                <div class="text-sm text-gray-500 mt-1">
                                    <span x-text="comment.length"></span>/1000 characters
                                </div>
                                <div x-show="errors.comment" class="text-red-500 text-sm mt-1" x-text="errors.comment"></div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button type="submit" :disabled="loading || rating === 0"
                                        class="bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                    <span x-show="!loading">Submit Review</span>
                                    <span x-show="loading">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Submitting...
                                    </span>
                                </button>
                            </div>

                            <!-- Success Message -->
                            <div x-show="showSuccess" class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                                <i class="fas fa-check-circle mr-2"></i>
                                Review submitted successfully!
                            </div>

                            <!-- Error Message -->
                            <div x-show="showError" class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <span x-text="errorMessage"></span>
                            </div>
                        </form>
                    </div>
                </div>
                @endauth
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Book -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-purple-50 border-b border-purple-200">
                        <h3 class="text-lg font-semibold text-purple-900">
                            <i class="fas fa-bolt text-purple-600 mr-2"></i>Quick Book
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-4">
                            <div class="text-2xl font-bold text-gray-900 mb-1">₹{{ $astrologer->per_minute_rate }}</div>
                            <div class="text-sm text-gray-600">per minute</div>
                        </div>
                        
                        <div class="space-y-3 mb-6 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Booking Fee:</span>
                                <span class="font-semibold">₹10</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Response Time:</span>
                                <span class="font-semibold text-green-600">< 2 mins</span>
                            </div>
                        </div>

                        <a href="/book-astrologer/{{ $astrologer->user_id }}" 
                           class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-semibold text-center block transition-colors">
                            <i class="fas fa-comments mr-2"></i>Start Chat Now
                        </a>
                    </div>
                </div>

                <!-- Specializations -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-star text-yellow-600 mr-2"></i>Specializations
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($astrologer->specialization)
                            <div class="space-y-2">
                                @foreach($astrologer->specialization as $spec)
                                    <div class="flex items-center text-sm text-gray-700">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        {{ $spec }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Languages -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-language text-blue-600 mr-2"></i>Languages
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($astrologer->languages)
                            <div class="flex flex-wrap gap-2">
                                @foreach($astrologer->languages as $language)
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">{{ $language }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Stats -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-chart-bar text-green-600 mr-2"></i>Stats
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Sessions:</span>
                            <span class="font-semibold">{{ $astrologer->bookings()->where('status', 'completed')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Avg Rating:</span>
                            <span class="font-semibold">{{ number_format($averageRating, 1) }}/5.0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Response Rate:</span>
                            <span class="font-semibold text-green-600">98%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function reviewForm() {
    return {
        rating: 0,
        comment: '',
        loading: false,
        showSuccess: false,
        showError: false,
        errorMessage: '',
        errors: {},

        async submitReview() {
            this.loading = true;
            this.errors = {};
            this.showSuccess = false;
            this.showError = false;

            try {
                const response = await fetch('/astrologer/review/{{ $astrologer->user_id }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        rating: this.rating,
                        comment: this.comment
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showSuccess = true;
                    this.rating = 0;
                    this.comment = '';
                    
                    // Reload page after 2 seconds to show new review
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    this.showError = true;
                    this.errorMessage = data.message || 'Failed to submit review';
                    
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                }
            } catch (error) {
                this.showError = true;
                this.errorMessage = 'Network error. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
