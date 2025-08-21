@extends('layouts.app')

@section('title', 'Welcome to AstroConnect')

@section('content')
<!-- Hero Section -->
<div class="gradient-bg star-pattern">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                ✨ Discover Your Destiny
            </h1>
            <p class="text-xl md:text-2xl text-white mb-8 max-w-3xl mx-auto">
                Connect with experienced astrologers from around the world. Get personalized readings, 
                guidance, and insights to navigate your life's journey.
            </p>
            <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                <a href="{{ url('/astrologers') }}" 
                   class="inline-block bg-white text-purple-600 font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition-colors">
                    Find an Astrologer
                </a>
                <a href="{{ url('/auth/register') }}" 
                   class="inline-block bg-purple-800 text-white font-bold py-3 px-8 rounded-lg hover:bg-purple-900 transition-colors">
                    Join as Astrologer
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Choose AstroConnect?</h2>
            <p class="text-xl text-gray-600">Your trusted platform for spiritual guidance and cosmic insights</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-star text-3xl text-purple-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Expert Astrologers</h3>
                <p class="text-gray-600">Verified professionals with years of experience in various astrological traditions</p>
            </div>

            <!-- Feature 2 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl">🌟</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Personalized Readings</h3>
                <p class="text-gray-600">Get customized insights based on your birth chart, questions, and life situation</p>
            </div>

            <!-- Feature 3 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl">💫</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Instant Chat</h3>
                <p class="text-gray-600">Connect with astrologers instantly via chat for real-time guidance</p>
            </div>
        </div>
    </div>
</div>

<!-- Services Section -->
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Services</h2>
            <p class="text-xl text-gray-600">Comprehensive astrological guidance for all aspects of life</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Service 1 -->
            <div class="bg-white rounded-lg p-6 text-center shadow-md hover:shadow-lg transition-shadow">
                <div class="text-4xl mb-4">🌙</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Birth Chart Reading</h3>
                <p class="text-gray-600 text-sm">Detailed analysis of your natal chart and personality traits</p>
            </div>

            <!-- Service 2 -->
            <div class="bg-white rounded-lg p-6 text-center shadow-md hover:shadow-lg transition-shadow">
                <div class="text-4xl mb-4">🃏</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Tarot Reading</h3>
                <p class="text-gray-600 text-sm">Gain insights into your future through ancient card wisdom</p>
            </div>

            <!-- Service 3 -->
            <div class="bg-white rounded-lg p-6 text-center shadow-md hover:shadow-lg transition-shadow">
                <div class="text-4xl mb-4">💝</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Love & Relationships</h3>
                <p class="text-gray-600 text-sm">Understand compatibility and relationship dynamics</p>
            </div>

            <!-- Service 4 -->
            <div class="bg-white rounded-lg p-6 text-center shadow-md hover:shadow-lg transition-shadow">
                <div class="text-4xl mb-4">💼</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Career Guidance</h3>
                <p class="text-gray-600 text-sm">Find your ideal career path and timing for success</p>
            </div>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
            <p class="text-xl text-gray-600">Simple steps to get your astrological guidance</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Step 1 -->
            <div class="text-center">
                <div class="w-12 h-12 bg-purple-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-lg font-bold">1</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Choose Astrologer</h3>
                <p class="text-gray-600">Browse and select from our verified astrologers</p>
            </div>

            <!-- Step 2 -->
            <div class="text-center">
                <div class="w-12 h-12 bg-purple-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-lg font-bold">2</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Start Chat</h3>
                <p class="text-gray-600">Begin your instant consultation chat</p>
            </div>

            <!-- Step 3 -->
            <div class="text-center">
                <div class="w-12 h-12 bg-purple-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-lg font-bold">3</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Get Reading</h3>
                <p class="text-gray-600">Receive personalized insights and guidance</p>
            </div>

            <!-- Step 4 -->
            <div class="text-center">
                <div class="w-12 h-12 bg-purple-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-lg font-bold">4</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Transform Life</h3>
                <p class="text-gray-600">Apply the wisdom to transform your journey</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="gradient-bg star-pattern">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <h2 class="text-4xl font-bold text-white mb-4">Ready to Discover Your Path?</h2>
            <p class="text-xl text-white mb-8">Join thousands who have found clarity and direction</p>
            <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                <a href="{{ url('/auth/register') }}" 
                   class="inline-block bg-white text-purple-600 font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition-colors">
                    Get Started Free
                </a>
                <a href="{{ url('/astrologers') }}" 
                   class="inline-block border-2 border-white text-white font-bold py-3 px-8 rounded-lg hover:bg-white hover:text-purple-600 transition-colors">
                    Browse Astrologers
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
