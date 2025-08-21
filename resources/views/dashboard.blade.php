@extends('layouts.auth-guard')

@section('title', 'Dashboard - AstroConnect')

@section('protected-content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Welcome to Your Dashboard!</h1>
            <p class="text-xl text-gray-600 mb-8">You have successfully logged in to AstroConnect</p>
            
            <div class="bg-white card-shadow rounded-lg p-8 max-w-md mx-auto">
                <div class="text-6xl mb-4"><i class="fas fa-check-circle text-green-500"></i></div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">Login Successful!</h2>
                <p class="text-gray-600 mb-6">Your dashboard functionality is coming soon.</p>
                
                <div class="space-y-3">
                    <a href="{{ url('/') }}" class="block w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition-colors">
                        Back to Home
                    </a>
                    <a href="{{ url('/astrologers') }}" class="block w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 transition-colors">
                        Browse Astrologers
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
