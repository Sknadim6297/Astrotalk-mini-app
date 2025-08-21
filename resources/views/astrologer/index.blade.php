@extends('layouts.app')

@section('title', 'Our Astrologers')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 py-8" x-data="astrologersList()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Our Expert Astrologers</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Connect with certified astrologers and get personalized insights about your future, relationships, career, and more.
            </p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Specialization Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
                    <select x-model="filters.specialization" @change="filterAstrologers()" 
                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        <option value="">All Specializations</option>
                        <option value="Vedic Astrology">Vedic Astrology</option>
                        <option value="Numerology">Numerology</option>
                        <option value="Tarot Reading">Tarot Reading</option>
                        <option value="Palmistry">Palmistry</option>
                        <option value="Vastu Shastra">Vastu Shastra</option>
                        <option value="Love & Relationships">Love & Relationships</option>
                        <option value="Career & Finance">Career & Finance</option>
                        <option value="Health & Wellness">Health & Wellness</option>
                    </select>
                </div>

                <!-- Language Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                    <select x-model="filters.language" @change="filterAstrologers()" 
                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        <option value="">All Languages</option>
                        <option value="Hindi">Hindi</option>
                        <option value="English">English</option>
                        <option value="Bengali">Bengali</option>
                        <option value="Tamil">Tamil</option>
                        <option value="Telugu">Telugu</option>
                        <option value="Gujarati">Gujarati</option>
                        <option value="Marathi">Marathi</option>
                        <option value="Punjabi">Punjabi</option>
                    </select>
                </div>

                <!-- Experience Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Experience</label>
                    <select x-model="filters.experience" @change="filterAstrologers()" 
                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        <option value="">Any Experience</option>
                        <option value="1-5">1-5 years</option>
                        <option value="5-10">5-10 years</option>
                        <option value="10-20">10-20 years</option>
                        <option value="20+">20+ years</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select x-model="sortBy" @change="sortAstrologers()" 
                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        <option value="rating">Highest Rated</option>
                        <option value="experience">Most Experienced</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="online">Online First</option>
                    </select>
                </div>
            </div>

            <!-- Search -->
            <div class="mt-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" x-model="searchQuery" @input="filterAstrologers()" 
                           placeholder="Search astrologer by name or specialization..."
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:border-purple-500 focus:ring-purple-500">
                </div>
            </div>
        </div>

        <script>
        // ensure apiCall helper exists (fallback if layout doesn't provide it)
        if (typeof window.apiCall !== 'function') {
            window.apiCall = async function(endpoint, method = 'GET', data = null) {
                const options = { method, headers: { 'Content-Type': 'application/json' } };
                // send CSRF token if available
                const meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) options.headers['X-CSRF-TOKEN'] = meta.getAttribute('content');
                if (data) options.body = JSON.stringify(data);
                const token = localStorage.getItem('api_token');
                if (token) options.headers['Authorization'] = `Bearer ${token}`;
                const res = await fetch('/api' + endpoint, options);
                return await res.json();
            }
        }

        function astrologersList() {
            return {
                loading: true,
                astrologers: [],
                filteredAstrologers: [],
                page: 1,
                lastPage: 1,
                hasMore: false,
                itemsPerPage: 9,
                sortBy: 'rating',
                searchQuery: '',
                filters: {
                    language: '',
                    specialization: '',
                    min_experience: '',
                    max_price: ''
                },
                availableLanguages: ['English', 'Hindi', 'Bengali', 'Tamil', 'Telugu', 'Gujarati', 'Marathi', 'Punjabi'],
                availableSpecializations: ['Vedic Astrology', 'Western Astrology', 'Tarot Reading', 'Numerology', 'Palmistry', 'Vastu Shastra', 'Love & Relationships', 'Career & Finance'],

                async init() {
                    await this.loadPage(1);
                },

                async loadPage(page = 1) {
                    this.loading = true;
                    try {
                        const res = await apiCall('/astrologers?page=' + page);

                        // payload normalization
                        const payload = res.data ?? res;

                        const items = Array.isArray(payload.data) ? payload.data : (Array.isArray(payload) ? payload : []);

                        const normalized = items.map(a => ({
                            id: a.id,
                            name: a.name,
                            specializations: Array.isArray(a.specializations) ? a.specializations : (a.specialization ? (Array.isArray(a.specialization) ? a.specialization : String(a.specialization).split(',').map(s=>s.trim()).filter(Boolean)) : []),
                            languages: Array.isArray(a.languages) ? a.languages : (a.language ? [a.language] : []),
                            rate: a.rate ?? a.per_minute_rate ?? 0,
                            is_online: Boolean(a.is_online),
                            rating: a.rating ?? 0,
                            reviews: a.reviews ?? 0,
                            experience: a.experience ?? 0,
                            bio: a.bio ?? '',
                        }));

                        if (page === 1) {
                            this.astrologers = normalized;
                        } else {
                            this.astrologers = this.astrologers.concat(normalized);
                        }

                        this.filteredAstrologers = [...this.astrologers];

                        // pagination
                        if (payload.meta) {
                            this.page = payload.meta.current_page ?? page;
                            this.lastPage = payload.meta.last_page ?? this.lastPage;
                        } else if (payload.current_page) {
                            this.page = payload.current_page;
                            this.lastPage = payload.last_page ?? this.lastPage;
                        }

                        this.hasMore = this.page < this.lastPage;

                    } catch (err) {
                        console.error(err);
                        // fallback to demo data
                        this.astrologers = this.generateDemoData();
                        this.filteredAstrologers = [...this.astrologers];
                        this.hasMore = false;
                    } finally {
                        this.loading = false;
                    }
                },

                async loadMore() {
                    if (!this.hasMore) return;
                    await this.loadPage(this.page + 1);
                },

                generateDemoData() {
                    const names = ['Priya Sharma', 'Raj Kumar', 'Anita Gupta', 'Vikram Singh', 'Meera Patel', 'Arjun Reddy', 'Kavya Nair', 'Rohit Joshi'];
                    const languages = this.availableLanguages;
                    const specializations = this.availableSpecializations;

                    return Array.from({ length: 24 }, (_, i) => ({
                        id: i + 1,
                        name: names[i % names.length],
                        experience: Math.floor(Math.random() * 15) + 1,
                        languages: this.getRandomItems(languages, Math.floor(Math.random() * 4) + 1),
                        specializations: this.getRandomItems(specializations, Math.floor(Math.random() * 3) + 1),
                        rate: (Math.random() * 8 + 1).toFixed(2),
                        wallet_balance: (Math.random() * 1000).toFixed(2),
                        is_online: Math.random() > 0.4,
                        rating: (Math.random() * 5).toFixed(1),
                        reviews: Math.floor(Math.random() * 120)
                    }));
                },

                getRandomItems(arr, count) {
                    const shuffled = [...arr].sort(() => 0.5 - Math.random());
                    return shuffled.slice(0, count);
                },

                filterAstrologers() {
                    const q = String(this.searchQuery || '').trim().toLowerCase();
                    this.filteredAstrologers = this.astrologers.filter(astrologer => {
                        // Language filter
                        if (this.filters.language && !astrologer.languages.includes(this.filters.language)) {
                            return false;
                        }

                        // Specialization filter
                        if (this.filters.specialization && !astrologer.specializations.includes(this.filters.specialization)) {
                            return false;
                        }

                        // Experience filter
                        if (this.filters.min_experience && astrologer.experience < parseInt(this.filters.min_experience)) {
                            return false;
                        }

                        // Price filter
                        if (this.filters.max_price && parseFloat(astrologer.rate) > parseFloat(this.filters.max_price)) {
                            return false;
                        }

                        // Search by name or specialization
                        if (q) {
                            const inName = astrologer.name && astrologer.name.toLowerCase().includes(q);
                            const inSpecs = astrologer.specializations && astrologer.specializations.join(' ').toLowerCase().includes(q);
                            if (!inName && !inSpecs) return false;
                        }

                        return true;
                    });

                    this.page = 1; // Reset to first page when filtering
                    this.hasMore = false;
                    // apply sort after filtering
                    this.sortAstrologers();
                },

                sortAstrologers() {
                    // ensure filteredAstrologers exists
                    if (!Array.isArray(this.filteredAstrologers)) return;

                    if (this.sortBy === 'rating') {
                        this.filteredAstrologers.sort((a, b) => (parseFloat(b.rating) || 0) - (parseFloat(a.rating) || 0));
                    } else if (this.sortBy === 'experience') {
                        this.filteredAstrologers.sort((a, b) => (b.experience || 0) - (a.experience || 0));
                    } else if (this.sortBy === 'price_low') {
                        this.filteredAstrologers.sort((a, b) => (parseFloat(a.rate) || 0) - (parseFloat(b.rate) || 0));
                    } else if (this.sortBy === 'price_high') {
                        this.filteredAstrologers.sort((a, b) => (parseFloat(b.rate) || 0) - (parseFloat(a.rate) || 0));
                    } else if (this.sortBy === 'online') {
                        this.filteredAstrologers.sort((a, b) => (b.is_online === true ? 1 : 0) - (a.is_online === true ? 1 : 0));
                    }
                },

                clearFilters() {
                    this.filters = {
                        language: '',
                        specialization: '',
                        min_experience: '',
                        max_price: ''
                    };
                    this.filteredAstrologers = [...this.astrologers];
                    this.page = 1;
                    this.hasMore = this.page < this.lastPage;
                },

                viewProfile(astrologerId) {
                    window.location.href = `/astrologer/profile/${astrologerId}`;
                },

                startConsultation(astrologerId) {
                    window.location.href = `/book-astrologer/${astrologerId}`;
                },

                async showBookingPopup(astrologer) {
                    // For now, redirect to booking page - can implement popup later
                    window.location.href = `/book-astrologer/${astrologer.id}`;
                }
            }
        }
        </script>

        <!-- Results Count -->
        <div class="mb-6">
            <p class="text-gray-600" x-show="filteredAstrologers.length > 0">
                Showing <span x-text="filteredAstrologers.length"></span> astrologers
            </p>
            <p class="text-gray-600" x-show="filteredAstrologers.length === 0">
                No astrologers found matching your criteria
            </p>
        </div>

        <!-- Astrologers Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-show="loading">
            <!-- Loading Skeletons -->
            <template x-for="i in 6">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden animate-pulse">
                    <div class="h-32 bg-gray-200"></div>
                    <div class="p-6">
                        <div class="h-4 bg-gray-200 rounded mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded mb-4 w-3/4"></div>
                        <div class="space-y-2">
                            <div class="h-3 bg-gray-200 rounded"></div>
                            <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-show="!loading">
            <template x-for="astrologer in filteredAstrologers" :key="astrologer.id">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <!-- Header with gradient -->
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 p-6 text-white relative">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold mb-1" x-text="astrologer.name"></h3>
                                <div class="flex items-center space-x-4 text-white/90 text-sm">
                                    <span class="flex items-center">
                                        <i class="fas fa-star mr-1"></i>
                                        <span x-text="astrologer.rating"></span>
                                        <span class="ml-1">(<span x-text="astrologer.reviews"></span>)</span>
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span x-text="astrologer.experience"></span>y exp
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">₹<span x-text="astrologer.rate"></span></div>
                                <div class="text-xs text-white/80">per minute</div>
                            </div>
                        </div>

                        <!-- Online Status -->
                        <div class="absolute top-4 right-4">
                            <div class="flex items-center" x-show="astrologer.is_online">
                                <div class="w-3 h-3 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                                <span class="text-xs text-white/90">Online</span>
                            </div>
                            <div class="flex items-center" x-show="!astrologer.is_online">
                                <div class="w-3 h-3 bg-gray-400 rounded-full mr-2"></div>
                                <span class="text-xs text-white/90">Offline</span>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <!-- Specializations -->
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-1">
                                <template x-for="spec in astrologer.specializations.slice(0, 3)">
                                    <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs" x-text="spec"></span>
                                </template>
                                <span x-show="astrologer.specializations.length > 3" 
                                      class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                                    +<span x-text="astrologer.specializations.length - 3"></span> more
                                </span>
                            </div>
                        </div>

                        <!-- Languages -->
                        <div class="mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-language mr-2"></i>
                                <span x-text="astrologer.languages.slice(0, 2).join(', ')"></span>
                                <span x-show="astrologer.languages.length > 2">
                                    +<span x-text="astrologer.languages.length - 2"></span> more
                                </span>
                            </div>
                        </div>

                        <!-- Bio preview -->
                        <p class="text-gray-700 text-sm mb-4 line-clamp-2" x-text="astrologer.bio"></p>

                        <!-- Actions -->
                        <div class="flex space-x-3">
                            <a :href="'/astrologer/profile/' + astrologer.id" 
                               class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded-lg text-center font-medium transition-colors">
                                View Profile
                            </a>
                            <a :href="'/book-astrologer/' + astrologer.id" 
                               class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg text-center font-medium transition-colors"
                               x-show="astrologer.is_online"
                               @click.prevent="showBookingPopup(astrologer)">
                                Book Now
                            </a>
                            <button class="flex-1 bg-gray-400 text-white py-2 px-4 rounded-lg text-center font-medium cursor-not-allowed"
                                    x-show="!astrologer.is_online" disabled>
                                Offline
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Load More -->
        <div class="text-center mt-12" x-show="hasMore && !loading">
            <button @click="loadMore()" 
                    class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                Load More Astrologers
            </button>
        </div>

        <!-- Empty State -->
        <div class="text-center py-12" x-show="filteredAstrologers.length === 0 && !loading">
            <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No astrologers found</h3>
            <p class="text-gray-600 mb-6">Try adjusting your filters or search terms</p>
            <button @click="clearFilters()" 
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                Clear All Filters
            </button>
        </div>
    </div>
</div>
@endsection
