@extends('layouts.astrologer-auth-guard')

@section('title', 'Edit Profile - AstroConnect')
@section('page-title', 'Edit Profile')

@section('astrologer-content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-xl p-6 text-white">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-user-edit text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold mb-2">Edit Your Profile</h1>
                    <p class="text-purple-200">Update your professional information and showcase your expertise</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Form -->
    <div x-data="profileManager()" x-init="loadProfile()">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Form Header -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-edit text-purple-600 mr-2"></i>Professional Information
                </h3>
            </div>

            <div class="p-6">
                <form @submit.prevent="updateProfile()">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="space-y-6">
                            <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                                Basic Information
                            </h4>

                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name *
                                </label>
                                <input type="text" x-model="form.name" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                       placeholder="Enter your full name">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address *
                                </label>
                                <input type="email" x-model="form.email" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                       placeholder="Enter your email">
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number
                                </label>
                                <input type="tel" x-model="form.phone"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                       placeholder="Enter your phone number">
                            </div>

                            <!-- Bio -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    About You
                                </label>
                                <textarea x-model="form.bio" rows="4"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                          placeholder="Tell clients about yourself, your approach to astrology, and your experience..."></textarea>
                                <p class="text-xs text-gray-500 mt-1">This will be shown on your public profile</p>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div class="space-y-6">
                            <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">
                                Professional Details
                            </h4>

                            <!-- Specialization -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Specializations *
                                </label>
                                <div class="space-y-2">
                                    <template x-for="(spec, index) in availableSpecializations" :key="index">
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   :value="spec"
                                                   @change="toggleSpecialization(spec)"
                                                   :checked="form.specialization.includes(spec)"
                                                   class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                            <span class="ml-2 text-sm text-gray-700" x-text="spec"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <!-- Languages -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Languages *
                                </label>
                                <div class="space-y-2">
                                    <template x-for="(lang, index) in availableLanguages" :key="index">
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   :value="lang"
                                                   @change="toggleLanguage(lang)"
                                                   :checked="form.languages.includes(lang)"
                                                   class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                            <span class="ml-2 text-sm text-gray-700" x-text="lang"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <!-- Experience -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Years of Experience *
                                </label>
                                <select x-model="form.experience" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Select experience</option>
                                    <template x-for="i in 50" :key="i">
                                        <option :value="i" x-text="i + (i === 1 ? ' year' : ' years')"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Per Minute Rate -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Per Minute Rate (₹) *
                                </label>
                                <input type="number" x-model="form.per_minute_rate" min="1" max="500" step="0.50" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                       placeholder="Enter your rate per minute">
                                <p class="text-xs text-gray-500 mt-1">Amount you charge per minute during consultations</p>
                            </div>

                            <!-- Education -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Education Background
                                </label>
                                <input type="text" x-model="form.education"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                       placeholder="Your educational qualifications">
                            </div>

                            <!-- Certifications -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Certifications & Awards
                                </label>
                                <textarea x-model="form.certifications" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                          placeholder="List your certifications, awards, or achievements in astrology..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" onclick="window.history.back()"
                                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="loading"
                                :class="loading ? 'bg-gray-400 cursor-not-allowed' : 'bg-purple-600 hover:bg-purple-700'"
                                class="px-6 py-3 text-white rounded-lg font-medium transition-colors">
                            <span x-show="!loading">
                                <i class="fas fa-save mr-2"></i>Save Changes
                            </span>
                            <span x-show="loading">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function profileManager() {
    return {
        loading: false,
        form: {
            name: '',
            email: '',
            phone: '',
            bio: '',
            specialization: [],
            languages: [],
            experience: '',
            per_minute_rate: '',
            education: '',
            certifications: ''
        },
        
        availableSpecializations: [
            'Vedic Astrology',
            'Western Astrology',
            'Tarot Reading',
            'Numerology',
            'Palmistry',
            'Vastu Shastra',
            'Face Reading',
            'Crystal Healing',
            'Birth Chart Analysis',
            'Relationship Compatibility',
            'Career Guidance',
            'Health Astrology'
        ],
        
        availableLanguages: [
            'English',
            'Hindi',
            'Bengali',
            'Tamil',
            'Telugu',
            'Marathi',
            'Gujarati',
            'Punjabi',
            'Kannada',
            'Malayalam',
            'Urdu',
            'Sanskrit'
        ],

        async loadProfile() {
            try {
                // Data is already available from server-side since we're using session auth
                const user = @json(Auth::user());
                const astrologer = @json(Auth::user()->astrologerProfile);
                
                if (user) {
                    this.form.name = user.name || '';
                    this.form.email = user.email || '';
                    this.form.phone = user.phone || '';
                    
                    if (astrologer) {
                        this.form.bio = astrologer.bio || '';
                        this.form.specialization = astrologer.specialization || [];
                        this.form.languages = astrologer.languages || [];
                        this.form.experience = astrologer.experience || '';
                        this.form.per_minute_rate = astrologer.per_minute_rate || '';
                        this.form.education = astrologer.education || '';
                        this.form.certifications = astrologer.certifications || '';
                    }
                }
            } catch (error) {
                console.error('Error loading profile:', error);
                showToast('Failed to load profile data', 'error');
            }
        },

        toggleSpecialization(spec) {
            const index = this.form.specialization.indexOf(spec);
            if (index > -1) {
                this.form.specialization.splice(index, 1);
            } else {
                this.form.specialization.push(spec);
            }
        },

        toggleLanguage(lang) {
            const index = this.form.languages.indexOf(lang);
            if (index > -1) {
                this.form.languages.splice(index, 1);
            } else {
                this.form.languages.push(lang);
            }
        },

        async updateProfile() {
            if (this.form.specialization.length === 0) {
                showToast('Please select at least one specialization', 'error');
                return;
            }

            if (this.form.languages.length === 0) {
                showToast('Please select at least one language', 'error');
                return;
            }

            this.loading = true;

            try {
                const response = await fetch('/astrologer/update-profile', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Profile updated successfully!', 'success');
                    // Optionally redirect or refresh data
                    setTimeout(() => {
                        window.location.href = '/astrologer/dashboard';
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Failed to update profile');
                }
            } catch (error) {
                console.error('Update error:', error);
                showToast('Error: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
