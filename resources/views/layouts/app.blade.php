<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Astrology Platform')</title>
    
    <!-- Poppins Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js for interactive components -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        /* Custom responsive breakpoint for very small screens */
        @media (min-width: 320px) {
            .xs\:inline {
                display: inline;
            }
            .xs\:hidden {
                display: none;
            }
        }
        @media (max-width: 319px) {
            .xs\:inline {
                display: none;
            }
            .xs\:hidden {
                display: inline;
            }
        }
        /* Ensure mobile menu doesn't interfere with page content */
        .mobile-menu-backdrop {
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 font-sans">
    <!-- Navigation -->
    <nav class="gradient-bg shadow-lg" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl sm:text-2xl font-bold text-white">
                            <i class="fas fa-star-and-crescent mr-1 sm:mr-2"></i>
                            <span class="hidden xs:inline">AstroConnect</span>
                            <span class="xs:hidden">Astro</span>
                        </h1>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Home</a>
                    <a href="{{ route('astrologers.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Find Astrologers</a>
                    
                    <!-- Desktop Authentication State -->
                    @guest
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('login') }}" class="bg-white text-purple-600 hover:bg-gray-100 px-4 py-2 rounded-md text-sm font-medium transition-colors">Login</a>
                            <a href="{{ route('register') }}" class="bg-purple-800 text-white hover:bg-purple-900 px-4 py-2 rounded-md text-sm font-medium transition-colors">Register</a>
                        </div>
                    @endguest
                    
                    <!-- Desktop Logged In User Profile -->
                    @auth
                        <div class="relative" x-data="{ showDropdown: false }">
                            <!-- Profile Display - Clickable -->
                            <div @click="showDropdown = !showDropdown" class="flex items-center space-x-3 cursor-pointer hover:bg-white/10 px-3 py-2 rounded-lg transition-colors">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <div class="hidden lg:block">
                                    <div class="text-white text-sm font-medium">{{ Auth::user()->name }}</div>
                                    <div class="text-purple-200 text-xs capitalize">{{ Auth::user()->role }}</div>
                                </div>
                                <!-- Dropdown Arrow -->
                                <i class="fas fa-chevron-down text-white text-xs transition-transform duration-200" 
                                   :class="showDropdown ? 'rotate-180' : ''" 
                                   x-cloak></i>
                            </div>

                            <!-- Dropdown Menu -->
                            <div x-show="showDropdown" 
                                 @click.away="showDropdown = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                
                                <!-- User Menu (Regular Users) -->
                                @if(Auth::user()->role === 'user')
                                    <div>
                                        <a href="{{ route('user.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-user w-5 h-5 mr-3"></i>
                                            Profile
                                        </a>
                                        <a href="{{ route('wallet.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-wallet w-5 h-5 mr-3"></i>
                                            Wallet
                                        </a>
                                        <a href="{{ route('user.appointments') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-calendar-alt w-5 h-5 mr-3"></i>
                                            Appointments
                                        </a>
                                        <hr class="my-1 border-gray-200">
                                        <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                                            Logout
                                        </button>
                                    </div>
                                @endif

                                <!-- Astrologer Menu -->
                                @if(Auth::user()->role === 'astrologer')
                                    <div>
                                        <a href="{{ route('astrologer.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
                                            Dashboard
                                        </a>
                                        <a href="{{ route('astrologer.bookings') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-book-open w-5 h-5 mr-3"></i>
                                            Bookings
                                        </a>
                                        <a href="{{ route('astrologer.edit-profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-user w-5 h-5 mr-3"></i>
                                            Edit Profile
                                        </a>
                                        <a href="{{ route('astrologer.availability') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-clock w-5 h-5 mr-3"></i>
                                            Availability
                                        </a>
                                        <a href="{{ route('astrologer.reviews') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-star w-5 h-5 mr-3"></i>
                                            Reviews
                                        </a>
                                        <hr class="my-1 border-gray-200">
                                        <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                                            Logout
                                        </button>
                                    </div>
                                @endif

                                <!-- Admin Menu -->
                                @if(Auth::user()->role === 'admin')
                                    <div>
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-bolt w-5 h-5 mr-3"></i>
                                            Admin Panel
                                        </a>
                                        <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-users w-5 h-5 mr-3"></i>
                                            Manage Users
                                        </a>
                                        <a href="{{ route('admin.astrologers') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-star w-5 h-5 mr-3"></i>
                                            Manage Astrologers
                                        </a>
                                        <hr class="my-1 border-gray-200">
                                        <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                                            Logout
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Hidden logout form -->
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    @auth
                        <!-- Mobile User Avatar -->
                        <div class="mr-2">
                            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                        </div>
                    @endauth
                    
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            type="button" 
                            class="text-white hover:text-gray-200 focus:outline-none focus:text-gray-200 transition-colors"
                            aria-label="Toggle mobile menu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-white/10 backdrop-blur-sm">
                    <!-- Mobile Navigation Links -->
                    <a href="{{ route('home') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">Home</a>
                    <a href="{{ route('astrologers.index') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">Find Astrologers</a>
                    
                    @guest
                        <!-- Mobile Guest Links -->
                        <hr class="border-white/20 my-2">
                        <a href="{{ route('login') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                            <i class="fas fa-sign-in-alt w-5 h-5 mr-2 inline"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                            <i class="fas fa-user-plus w-5 h-5 mr-2 inline"></i>Register
                        </a>
                    @endguest

                    @auth
                        <!-- Mobile User Info -->
                        <div class="px-3 py-2">
                            <div class="text-white text-sm font-medium">{{ Auth::user()->name }}</div>
                            <div class="text-purple-200 text-xs capitalize">{{ Auth::user()->role }}</div>
                        </div>
                        
                        <hr class="border-white/20 my-2">
                        
                        <!-- Mobile User Menu Items -->
                        @if(Auth::user()->role === 'user')
                            <a href="{{ route('user.profile') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                                <i class="fas fa-user w-5 h-5 mr-2 inline"></i>Profile
                            </a>
                            <a href="{{ route('wallet.index') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                                <i class="fas fa-wallet w-5 h-5 mr-2 inline"></i>Wallet
                            </a>
                            <a href="{{ route('user.appointments') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                                <i class="fas fa-calendar-alt w-5 h-5 mr-2 inline"></i>Appointments
                            </a>
                        @endif

                        @if(Auth::user()->role === 'astrologer')
                            <a href="{{ route('astrologer.dashboard') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                                <i class="fas fa-chart-line w-5 h-5 mr-2 inline"></i>Dashboard
                            </a>
                            <a href="{{ route('astrologer.bookings') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                                <i class="fas fa-book-open w-5 h-5 mr-2 inline"></i>Bookings
                            </a>
                            <a href="{{ route('astrologer.edit-profile') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                                <i class="fas fa-user w-5 h-5 mr-2 inline"></i>Edit Profile
                            </a>
                            <a href="{{ route('astrologer.availability') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                                <i class="fas fa-clock w-5 h-5 mr-2 inline"></i>Availability
                            </a>
                            <a href="{{ route('astrologer.reviews') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                                <i class="fas fa-star w-5 h-5 mr-2 inline"></i>Reviews
                            </a>
                        @endif

                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                                <i class="fas fa-bolt w-5 h-5 mr-2 inline"></i>Admin Panel
                            </a>
                            <a href="{{ route('admin.users') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                                <i class="fas fa-users w-5 h-5 mr-2 inline"></i>Manage Users
                            </a>
                            <a href="{{ route('admin.astrologers') }}" class="block text-white hover:bg-white/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                                <i class="fas fa-star w-5 h-5 mr-2 inline"></i>Manage Astrologers
                            </a>
                        @endif
                        
                        <hr class="border-white/20 my-2">
                        <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="w-full text-left block text-white hover:bg-red-500/20 px-3 py-2 rounded-md text-base font-medium transition-colors">
                            <i class="fas fa-sign-out-alt w-5 h-5 mr-2 inline"></i>Logout
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="gradient-bg text-white py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h3 class="text-lg font-semibold mb-2"><i class="fas fa-star-and-crescent mr-2"></i>AstroConnect</h3>
                <p class="text-gray-200 mb-4">Connect with verified astrologers for personalized guidance</p>
                <div class="flex justify-center space-x-6 text-sm">
                    <a href="#" class="hover:text-gray-300">About Us</a>
                    <a href="#" class="hover:text-gray-300">Contact</a>
                    <a href="#" class="hover:text-gray-300">Privacy Policy</a>
                    <a href="#" class="hover:text-gray-300">Terms of Service</a>
                </div>
                <div class="mt-4 text-gray-300 text-sm">
                    © 2024 AstroConnect. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <!-- Show toast notifications -->
    <script>
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 p-4 rounded-md text-white z-50 transition-opacity duration-300 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Show flash messages
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    </script>
    
    @yield('scripts')
</body>
</html>
