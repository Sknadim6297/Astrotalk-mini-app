@extends('layouts.admin')

@section('content')
<!-- Since admin routes are protected by middleware, just show the content -->
@if(Auth::check() && Auth::user()->role === 'admin')
    @yield('admin-content')
@else
    <!-- This should never show due to middleware, but as fallback -->
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center max-w-md mx-auto p-6">
            <div class="text-6xl mb-4"><i class="fas fa-lock text-gray-400"></i></div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Admin Access Required</h2>
            <p class="text-gray-600 mb-6">You need to be logged in as an administrator to access this page.</p>
            <a href="{{ url('/admin/login') }}" class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition-colors">
                Admin Login
            </a>
        </div>
    </div>
@endif
@endsection
