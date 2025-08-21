<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Astrologer Panel - AstroConnect')</title>
    <script>
        // Expose astrologerNavbar on window so Alpine can access it even if it initialises early
        window.astrologerNavbar = function() {
            return {
                user: @json(Auth::user()),
                mobileSidebarOpen: false,

                init() {
                    console.log('Astrologer user loaded:', this.user);
                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.mobileSidebarOpen) this.mobileSidebarOpen = false;
                    });
                },

                async logout() {
                    try {
                        const response = await fetch('/logout', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            window.location.href = '/';
                        } else {
                            window.location.href = '/';
                        }
                    } catch (error) {
                        console.error('Logout failed:', error);
                        window.location.href = '/';
                    }
                }
            }
        }
    // Provide an identifier-level wrapper so x-data="astrologerNavbar()" resolves reliably
    function astrologerNavbar() { return window.astrologerNavbar(); }
    </script>
    
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
        .astrologer-gradient {
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
        }
        .card-shadow {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .sidebar-gradient {
            background: linear-gradient(180deg, #1e1b4b 0%, #1e3a8a 100%);
        }
        .nav-link-active {
            background: linear-gradient(90deg, #7c3aed 0%, #8b5cf6 100%);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        }
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }
        .nav-link {
            transition: all 0.3s ease;
        }
        .status-online { color: #10b981; }
        .status-offline { color: #ef4444; }
        .status-busy { color: #f59e0b; }
    </style>
</head>
<body class="min-h-screen bg-purple-50 font-sans" x-data="astrologerNavbar()">
    <!-- Modern Astrologer Layout -->
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-72">
                <div class="sidebar-gradient min-h-0 flex-1 flex flex-col">
                    <!-- Logo -->
                    <div class="flex items-center h-16 px-6 border-b border-blue-600">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-star text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h2 class="text-white font-bold text-lg">AstroConnect</h2>
                                <p class="text-purple-300 text-xs font-medium">Astrologer Pro</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status Toggle -->
                    <div class="px-6 py-4 border-b border-blue-600" x-data="{ status: 'online' }">
                        <div class="flex items-center justify-between">
                            <span class="text-white text-sm font-medium">Status:</span>
                            <div class="relative">
                                <select x-model="status" class="bg-blue-800 text-white text-sm rounded-lg px-3 py-1 border border-blue-600 focus:outline-none">
                                    <option value="online">🟢 Online</option>
                                    <option value="busy">🟡 Busy</option>
                                    <option value="offline">🔴 Offline</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav class="mt-6 flex-1 px-4 space-y-2">
                        <a href="/astrologer/dashboard" class="nav-link flex items-center px-4 py-3 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
                            Dashboard
                        </a>
                        <a href="/astrologer/bookings" class="nav-link flex items-center px-4 py-3 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-calendar-check w-5 h-5 mr-3"></i>
                            My Bookings
                        </a>
                        <a href="/astrologer/sessions" class="nav-link flex items-center px-4 py-3 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-comments w-5 h-5 mr-3"></i>
                            Active Sessions
                        </a>
                        <a href="/astrologer/earnings" class="nav-link flex items-center px-4 py-3 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-wallet w-5 h-5 mr-3"></i>
                            Earnings & Wallet
                        </a>
                        <a href="/astrologer/reviews" class="nav-link flex items-center px-4 py-3 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-star-half-alt w-5 h-5 mr-3"></i>
                            Reviews & Ratings
                        </a>
                        <a href="/astrologer/edit-profile" class="nav-link flex items-center px-4 py-3 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-user-edit w-5 h-5 mr-3"></i>
                            Edit Profile
                        </a>
                        <a href="/astrologer/availability" class="nav-link flex items-center px-4 py-3 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-clock w-5 h-5 mr-3"></i>
                            Availability
                        </a>
                        
                        <!-- Divider -->
                        <div class="border-t border-blue-600 pt-4 mt-6">
                            <a href="/" class="nav-link flex items-center px-4 py-3 text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-home w-5 h-5 mr-3"></i>
                                Main Website
                            </a>
                            <a href="/astrologer/help" class="nav-link flex items-center px-4 py-3 text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-question-circle w-5 h-5 mr-3"></i>
                                Help & Support
                            </a>
                        </div>
                    </nav>

                    <!-- Astrologer Profile (Bottom) -->
                    <template x-if="user">
                        <div class="flex-shrink-0 flex border-t border-blue-600 p-4">
                            <div class="flex items-center w-full">
                                <div class="relative">
                                    <img :src="user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=7c3aed&color=fff&size=40'" 
                                         :alt="user.name" 
                                         class="w-10 h-10 rounded-full">
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-white text-sm font-medium" x-text="user.name"></p>
                                    <p class="text-purple-300 text-xs">⭐ Professional Astrologer</p>
                                </div>
                                <button @click="logout()" class="ml-3 text-gray-400 hover:text-white">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <!-- Mobile Sidebar (hidden on lg+) -->
        <div x-show="mobileSidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden" aria-hidden="true">
            <div @click="mobileSidebarOpen = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

            <div x-show="mobileSidebarOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="absolute inset-y-0 left-0 w-64 bg-gradient-to-b from-purple-900 to-purple-800 text-white shadow-xl">
                <div class="flex flex-col h-full">
                    <div class="flex items-center h-16 px-4 border-b border-purple-700">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-star text-white text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h2 class="text-white font-bold text-base">AstroConnect</h2>
                                <p class="text-purple-300 text-xs">Astrologer Pro</p>
                            </div>
                        </div>
                        <button @click="mobileSidebarOpen = false" class="ml-auto text-gray-300 hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <nav class="mt-4 px-2 space-y-1 overflow-y-auto">
                        <a href="/astrologer/dashboard" class="block nav-link px-4 py-3 text-white rounded-lg text-sm font-medium">Dashboard</a>
                        <a href="/astrologer/bookings" class="block nav-link px-4 py-3 text-white rounded-lg text-sm font-medium">My Bookings</a>
                        <a href="/astrologer/sessions" class="block nav-link px-4 py-3 text-white rounded-lg text-sm font-medium">Active Sessions</a>
                        <a href="/astrologer/earnings" class="block nav-link px-4 py-3 text-white rounded-lg text-sm font-medium">Earnings & Wallet</a>
                        <a href="/astrologer/reviews" class="block nav-link px-4 py-3 text-white rounded-lg text-sm font-medium">Reviews & Ratings</a>
                        <a href="/astrologer/edit-profile" class="block nav-link px-4 py-3 text-white rounded-lg text-sm font-medium">Edit Profile</a>
                        <a href="/astrologer/availability" class="block nav-link px-4 py-3 text-white rounded-lg text-sm font-medium">Availability</a>
                        <div class="border-t border-purple-700 pt-4 mt-4">
                            <a href="/" class="block nav-link px-4 py-3 text-white rounded-lg text-sm font-medium">Main Website</a>
                            <a href="/astrologer/help" class="block nav-link px-4 py-3 text-white rounded-lg text-sm font-medium">Help & Support</a>
                        </div>
                    </nav>

                    <template x-if="user">
                        <div class="flex-shrink-0 flex border-t border-purple-700 p-4">
                            <div class="flex items-center w-full">
                                <img :src="user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=7c3aed&color=fff&size=40'" 
                                     :alt="user.name" 
                                     class="w-10 h-10 rounded-full">
                                <div class="ml-3 flex-1">
                                    <p class="text-white text-sm font-medium" x-text="user.name"></p>
                                    <p class="text-purple-300 text-xs">⭐ Professional Astrologer</p>
                                </div>
                                <button @click="logout(); mobileSidebarOpen = false" class="ml-3 text-gray-300 hover:text-white">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <!-- Main Content Area -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Top Header -->
            <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                <!-- Mobile menu button -->
                <button @click="mobileSidebarOpen = true" type="button" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500 lg:hidden" aria-label="Open sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="flex-1 px-4 flex justify-between items-center">
                    <div class="flex-1 flex items-center">
                        <h1 class="text-xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    
                    <div class="ml-4 flex items-center lg:ml-6 space-x-4">
                        <!-- (Removed earnings and sessions badges for mobile/header per request) -->

                        <!-- Notifications -->
                        <button class="relative bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <i class="fas fa-bell h-6 w-6"></i>
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center">
                                <span class="text-xs text-white font-medium">3</span>
                            </span>
                        </button>

                        <!-- Astrologer Profile (Mobile/Desktop) -->
                        <template x-if="user">
                            <div class="relative" x-data="{ showDropdown: false }">
                                <button @click="showDropdown = !showDropdown" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <img :src="user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=7c3aed&color=fff&size=32'" 
                                         :alt="user.name" 
                                         class="h-8 w-8 rounded-full border-2 border-purple-200">
                                </button>

                                <div x-show="showDropdown" 
                                     @click.away="showDropdown = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <a href="/astrologer/edit-profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                                    <!-- Settings removed per request -->
                                    <a href="/astrologer/earnings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Earnings</a>
                                    <button @click="logout(); showDropdown = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none bg-purple-50">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Show toast notifications
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
    </script>
    
    @yield('scripts')
</body>
</html>
