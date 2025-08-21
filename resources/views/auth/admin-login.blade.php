@extends('layouts.app')

@section('title', 'Admin Login - AstroConnect')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-red-100">
                <i class="fas fa-bolt text-2xl text-red-600"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Administrator Access
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Authorized personnel only
            </p>
        </div>

        <!-- Alert for unauthorized access -->
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Restricted Area
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>This is a secure admin login. Only authorized administrators can access this area.</p>
                    </div>
                </div>
            </div>
        </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Administrator Email
                    </label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           value="{{ old('email') }}"
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                           placeholder="admin@gmail.com">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                           placeholder="Password">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-lock mr-2"></i>Administrator Login
                </button>
            </div>

            <!-- Back to main site -->
            <div class="text-center">
                <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-gray-500">
                    ← Back to AstroConnect
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<!-- No additional scripts needed for basic form submission -->
@endsection
