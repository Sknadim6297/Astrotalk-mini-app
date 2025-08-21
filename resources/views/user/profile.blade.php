@extends('layouts.auth-guard')

@section('title', 'My Profile - AstroConnect')

@section('protected-content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white card-shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-user mr-2"></i>My Profile</h1>
                <p class="text-gray-600">Manage your personal information and preferences</p>
            </div>
            
            <div class="p-6">
                <div class="text-center py-12">
                    <div class="text-6xl mb-4"><i class="fas fa-user-circle text-purple-500"></i></div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Profile Management</h2>
                    <p class="text-gray-600 mb-6">Your profile settings will be available here soon.</p>
                    
                    <div class="max-w-sm mx-auto space-y-3">
                        <button class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition-colors">
                            Edit Profile
                        </button>
                        <button class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 transition-colors">
                            Change Password
                        </button>
                        <a href="{{ url('/dashboard') }}" class="block w-full bg-gray-50 text-gray-600 py-2 px-4 rounded-md hover:bg-gray-100 transition-colors">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
