@extends('layouts.app')

@section('content')
<div x-data="authGuard()" x-show="!loading">
    <!-- Loading state -->
    <div x-show="loading" class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Loading...</p>
        </div>
    </div>

    <!-- Content when authenticated -->
    <div x-show="!loading && isAuthenticated">
        @yield('protected-content')
    </div>

    <!-- Redirect message when not authenticated -->
    <div x-show="!loading && !isAuthenticated" class="min-h-screen flex items-center justify-center">
        <div class="text-center max-w-md mx-auto p-6">
            <div class="text-6xl mb-4"><i class="fas fa-lock text-gray-400"></i></div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Authentication Required</h2>
            <p class="text-gray-600 mb-6">You need to be logged in to access this page.</p>
            <a href="{{ route('login') }}" class="bg-purple-600 text-white px-6 py-2 rounded-md hover:bg-purple-700 transition-colors">
                Login Now
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function authGuard() {
    return {
        loading: true,
        isAuthenticated: false,
        user: null,
        requiredRole: @json($requiredRole ?? null),

        init() {
            this.checkAuth();
        },

        async checkAuth() {
            // First check if user is authenticated via Laravel session
            try {
                const response = await fetch('/debug-auth', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.authenticated && data.user) {
                    this.user = data.user;
                    
                    // Check role if required
                    if (this.requiredRole && this.user.role !== this.requiredRole) {
                        this.redirectToUnauthorized();
                        return;
                    }
                    
                    this.isAuthenticated = true;
                    
                    // Update navbar
                    if (window.refreshAuthStatus) {
                        window.refreshAuthStatus();
                    }
                } else {
                    this.redirectToLogin();
                }
            } catch (error) {
                console.error('Auth check failed:', error);
                this.redirectToLogin();
            } finally {
                this.loading = false;
            }
        },

        redirectToLogin() {
            showToast('Please login to continue', 'error');
            const currentUrl = encodeURIComponent(window.location.pathname);
            setTimeout(() => {
                window.location.href = `/login?redirect=${currentUrl}`;
            }, 1500);
        },

        redirectToUnauthorized() {
            showToast('You do not have permission to access this page', 'error');
            setTimeout(() => {
                if (this.user.role === 'admin') {
                    window.location.href = '/admin/dashboard';
                } else if (this.user.role === 'astrologer') {
                    window.location.href = '/astrologer/dashboard';
                } else {
                    window.location.href = '/dashboard';
                }
            }, 2000);
        }
    }
}
</script>
@endsection
