@extends('layouts.app')

@section('title', 'Register - AstroConnect')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-purple-100">
                <span class="text-2xl">✨</span>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Join our astrology community
            </p>
            
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ route('register.post') }}">
            @csrf
            <!-- Role Selection -->
            <div class="space-y-3">
                <label class="text-sm font-medium text-gray-700">Choose your role</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" value="user" name="role" 
                               class="sr-only role-input" {{ old('role', 'user') == 'user' ? 'checked' : '' }}>
                        <div class="p-3 text-center border-2 rounded-lg transition-all role-option" 
                             data-role="user">
                            <div class="text-2xl mb-1"><i class="fas fa-user text-purple-600"></i></div>
                            <div class="text-sm font-medium">User</div>
                        </div>
                    </label>
                    
                    <label class="cursor-pointer">
                        <input type="radio" value="astrologer" name="role" 
                               class="sr-only role-input" {{ old('role') == 'astrologer' ? 'checked' : '' }}>
                        <div class="p-3 text-center border-2 rounded-lg transition-all role-option" 
                             data-role="astrologer">
                            <div class="text-2xl mb-1"><i class="fas fa-star text-purple-600"></i></div>
                            <div class="text-sm font-medium">Astrologer</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="space-y-4">
                <div>
                    <label for="name" class="sr-only">Full Name</label>
                    <input id="name" name="name" type="text" required
                           value="{{ old('name') }}"
                           class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                           placeholder="Full Name">
                </div>

                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" required
                           value="{{ old('email') }}"
                           class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                           placeholder="Email address">
                </div>

                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required
                           class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                           placeholder="Password">
                </div>

                <div>
                    <label for="password_confirmation" class="sr-only">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"
                           placeholder="Confirm Password">
                </div>
            </div>

            <!-- Astrologer Profile Fields (shown when astrologer is selected) -->
            <div id="astrologer-fields" class="space-y-4" style="display: none;">
                <div class="border-t pt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Professional Details</h3>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="experience" class="block text-sm font-medium text-gray-700">Years of Experience</label>
                            <input id="experience" name="experience" type="number" min="0" max="50"
                                   value="{{ old('experience') }}"
                                   class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                   placeholder="Years of experience">
                        </div>

                        <div>
                            <label for="education" class="block text-sm font-medium text-gray-700">Education</label>
                            <input id="education" name="education" type="text"
                                   value="{{ old('education') }}"
                                   class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                   placeholder="Your educational background">
                        </div>

                        <div>
                            <label for="certifications" class="block text-sm font-medium text-gray-700">Certifications</label>
                            <textarea id="certifications" name="certifications" rows="3"
                                      class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                      placeholder="List your certifications...">{{ old('certifications') }}</textarea>
                        </div>

                        <div>
                            <label for="specialization" class="block text-sm font-medium text-gray-700">Specialization</label>
                            <select id="specialization" name="specialization[]" multiple
                                    class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                <option value="vedic">Vedic Astrology</option>
                                <option value="western">Western Astrology</option>
                                <option value="numerology">Numerology</option>
                                <option value="tarot">Tarot Reading</option>
                                <option value="palmistry">Palmistry</option>
                                <option value="vastu">Vastu Shastra</option>
                                <option value="horoscope">Horoscope Reading</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                        </div>

                        <div>
                            <label for="languages" class="block text-sm font-medium text-gray-700">Languages</label>
                            <select id="languages" name="languages[]" multiple
                                    class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                <option value="hindi">Hindi</option>
                                <option value="english">English</option>
                                <option value="bengali">Bengali</option>
                                <option value="gujarati">Gujarati</option>
                                <option value="marathi">Marathi</option>
                                <option value="tamil">Tamil</option>
                                <option value="telugu">Telugu</option>
                                <option value="punjabi">Punjabi</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                        </div>

                        <div>
                            <label for="per_minute_rate" class="block text-sm font-medium text-gray-700">Per Minute Rate (₹)</label>
                            <input id="per_minute_rate" name="per_minute_rate" type="number" min="10" max="1000" step="0.01"
                                   value="{{ old('per_minute_rate') }}"
                                   class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                   placeholder="Rate per minute">
                        </div>

                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                            <textarea id="bio" name="bio" rows="4"
                                      class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                      placeholder="Tell us about yourself and your expertise...">{{ old('bio') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Create Account
                </button>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ url('/auth/login') }}" class="font-medium text-purple-600 hover:text-purple-500">
                        Sign in
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Handle role selection visual feedback
document.addEventListener('DOMContentLoaded', function() {
    const roleInputs = document.querySelectorAll('.role-input');
    const roleOptions = document.querySelectorAll('.role-option');
    const astrologerFields = document.getElementById('astrologer-fields');
    
    function updateRoleSelection() {
        let isAstrologer = false;
        
        roleOptions.forEach(option => {
            const radio = option.parentElement.querySelector('.role-input');
            if (radio.checked) {
                option.classList.add('border-purple-500', 'bg-purple-50');
                option.classList.remove('border-gray-300');
                
                if (radio.value === 'astrologer') {
                    isAstrologer = true;
                }
            } else {
                option.classList.remove('border-purple-500', 'bg-purple-50');
                option.classList.add('border-gray-300');
            }
        });
        
        // Show/hide astrologer fields
        if (isAstrologer) {
            astrologerFields.style.display = 'block';
            // Make astrologer fields required
            astrologerFields.querySelectorAll('input, textarea, select').forEach(field => {
                if (field.name !== 'certifications' && field.name !== 'bio') {
                    field.required = true;
                }
            });
        } else {
            astrologerFields.style.display = 'none';
            // Remove required from astrologer fields
            astrologerFields.querySelectorAll('input, textarea, select').forEach(field => {
                field.required = false;
            });
        }
    }
    
    roleInputs.forEach(input => {
        input.addEventListener('change', updateRoleSelection);
    });
    
    // Initial state
    updateRoleSelection();
});
</script>
@endsection
