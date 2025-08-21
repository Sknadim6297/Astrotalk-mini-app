@extends('layouts.astrologer-auth-guard')

@section('title', 'Manage Availability - AstroConnect')
@section('page-title', 'Availability Settings')

@section('astrologer-content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-xl p-6 text-white">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold mb-2">Manage Your Availability</h1>
                    <p class="text-indigo-200">Set your working hours and let clients know when you're available</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Availability Form -->
    <div x-data="availabilityManager()" x-init="loadAvailability()">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Availability Schedule -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-calendar-alt text-indigo-600 mr-2"></i>Weekly Schedule
                        </h3>
                    </div>

                    <div class="p-6">
                        <template x-for="(day, dayName) in schedule" :key="dayName">
                            <div class="mb-6 border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               x-model="day.enabled" 
                                               :id="'day-' + dayName"
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <label :for="'day-' + dayName" 
                                               class="ml-3 text-lg font-medium text-gray-900 capitalize" 
                                               x-text="dayName"></label>
                                    </div>
                                    <span x-show="!day.enabled" class="text-sm text-gray-500">Not Available</span>
                                </div>

                                <div x-show="day.enabled" class="space-y-3">
                                    <template x-for="(slot, slotIndex) in day.slots" :key="slotIndex">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-1 grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                                    <input type="time" 
                                                           x-model="slot.start_time"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                                    <input type="time" 
                                                           x-model="slot.end_time"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                </div>
                                            </div>
                                            <button @click="removeSlot(dayName, slotIndex)"
                                                    x-show="day.slots.length > 1"
                                                    class="mt-6 p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </template>

                                    <button @click="addSlot(dayName)"
                                            class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                        <i class="fas fa-plus mr-1"></i>Add Time Slot
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Quick Actions -->
                        <div class="flex flex-wrap gap-2 mt-6 pt-6 border-t border-gray-200">
                            <button @click="setWeekdaySchedule()"
                                    class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 text-sm font-medium">
                                Set Weekday Schedule (9 AM - 6 PM)
                            </button>
                            <button @click="setWeekendSchedule()"
                                    class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 text-sm font-medium">
                                Set Weekend Schedule (10 AM - 4 PM)
                            </button>
                            <button @click="clearAllSchedule()"
                                    class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-sm font-medium">
                                Clear All
                            </button>
                        </div>

                        <!-- Save Button -->
                        <div class="mt-8 flex justify-end">
                            <button @click="updateAvailability()" :disabled="loading"
                                    :class="loading ? 'bg-gray-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                                    class="px-6 py-3 text-white rounded-lg font-medium transition-colors">
                                <span x-show="!loading">
                                    <i class="fas fa-save mr-2"></i>Save Availability
                                </span>
                                <span x-show="loading">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Saving...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Settings -->
            <div class="space-y-6">
                <!-- Online Status -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-power-off text-green-600 mr-2"></i>Status
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-gray-700 font-medium">Currently Available</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="isOnline" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                        <p class="text-sm text-gray-600">
                            <span x-show="isOnline" class="text-green-600 font-medium">
                                <i class="fas fa-circle text-green-500 mr-1"></i>Online - Accepting new bookings
                            </span>
                            <span x-show="!isOnline" class="text-red-600 font-medium">
                                <i class="fas fa-circle text-red-500 mr-1"></i>Offline - Not accepting bookings
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Timezone -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-globe text-blue-600 mr-2"></i>Timezone
                        </h3>
                    </div>
                    <div class="p-6">
                        <select x-model="timezone" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Asia/Kolkata">India Standard Time (IST)</option>
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">Eastern Time (EST/EDT)</option>
                            <option value="America/Los_Angeles">Pacific Time (PST/PDT)</option>
                            <option value="Europe/London">British Time (GMT/BST)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-2">All times will be displayed in this timezone</p>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-chart-bar text-yellow-600 mr-2"></i>This Week
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Available Hours:</span>
                            <span class="font-semibold text-gray-900" x-text="getTotalAvailableHours() + ' hrs'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Active Days:</span>
                            <span class="font-semibold text-gray-900" x-text="getActiveDays() + ' days'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function availabilityManager() {
    return {
        loading: false,
        isOnline: true,
        timezone: 'Asia/Kolkata',
        schedule: {
            monday: { enabled: true, slots: [{ start_time: '09:00', end_time: '18:00' }] },
            tuesday: { enabled: true, slots: [{ start_time: '09:00', end_time: '18:00' }] },
            wednesday: { enabled: true, slots: [{ start_time: '09:00', end_time: '18:00' }] },
            thursday: { enabled: true, slots: [{ start_time: '09:00', end_time: '18:00' }] },
            friday: { enabled: true, slots: [{ start_time: '09:00', end_time: '18:00' }] },
            saturday: { enabled: true, slots: [{ start_time: '10:00', end_time: '16:00' }] },
            sunday: { enabled: false, slots: [{ start_time: '10:00', end_time: '16:00' }] }
        },

        async loadAvailability() {
            try {
                // Data is already available from server-side since we're using session auth
                const user = @json(Auth::user());
                const astrologer = @json(Auth::user()->astrologerProfile);
                
                if (astrologer) {
                    if (astrologer.availability) {
                        // Server may return availability as either an array of {day,..} or an object keyed by day names.
                        const serverAvail = astrologer.availability;

                        if (Array.isArray(serverAvail)) {
                            // convert array -> map keyed by lowercase day name
                            const map = {};
                            serverAvail.forEach(item => {
                                const dayKey = (item.day || '').toLowerCase();
                                if (!dayKey) return;
                                map[dayKey] = {
                                    enabled: item.enabled === true || item.enabled === 'true' ? true : !!item.enabled,
                                    slots: Array.isArray(item.slots) && item.slots.length ? item.slots : [{ start_time: '09:00', end_time: '18:00' }]
                                };
                            });
                            // ensure all weekdays present
                            this.schedule = Object.assign({}, this.schedule, map);
                        } else if (typeof serverAvail === 'object' && serverAvail !== null) {
                            // object keyed by day names - normalize keys and slot defaults
                            const map = {};
                            Object.entries(serverAvail).forEach(([k, v]) => {
                                const dayKey = String(k).toLowerCase();
                                map[dayKey] = {
                                    enabled: v && (v.enabled === true || v.enabled === 'true') ? true : !!(v && v.enabled),
                                    slots: v && Array.isArray(v.slots) && v.slots.length ? v.slots : [{ start_time: '09:00', end_time: '18:00' }]
                                };
                            });
                            this.schedule = Object.assign({}, this.schedule, map);
                        }
                    }

                    this.timezone = astrologer.timezone || 'Asia/Kolkata';
                    this.isOnline = astrologer.is_online !== false; // default to true
                }
            } catch (error) {
                console.error('Error loading availability:', error);
                showToast('Failed to load availability data', 'error');
            }
        },

        addSlot(dayName) {
            this.schedule[dayName].slots.push({ start_time: '09:00', end_time: '18:00' });
        },

        removeSlot(dayName, slotIndex) {
            if (this.schedule[dayName].slots.length > 1) {
                this.schedule[dayName].slots.splice(slotIndex, 1);
            }
        },

        setWeekdaySchedule() {
            const weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
            weekdays.forEach(day => {
                this.schedule[day] = {
                    enabled: true,
                    slots: [{ start_time: '09:00', end_time: '18:00' }]
                };
            });
        },

        setWeekendSchedule() {
            const weekends = ['saturday', 'sunday'];
            weekends.forEach(day => {
                this.schedule[day] = {
                    enabled: true,
                    slots: [{ start_time: '10:00', end_time: '16:00' }]
                };
            });
        },

        clearAllSchedule() {
            Object.keys(this.schedule).forEach(day => {
                this.schedule[day].enabled = false;
            });
        },

        getTotalAvailableHours() {
            let total = 0;
            Object.values(this.schedule).forEach(day => {
                if (day.enabled) {
                    day.slots.forEach(slot => {
                        const start = new Date(`2000-01-01T${slot.start_time}`);
                        const end = new Date(`2000-01-01T${slot.end_time}`);
                        const diff = (end - start) / (1000 * 60 * 60); // hours
                        total += diff;
                    });
                }
            });
            return Math.round(total);
        },

        getActiveDays() {
            return Object.values(this.schedule).filter(day => day.enabled).length;
        },

        async updateAvailability() {
            this.loading = true;

            try {
                // Validate schedule
                for (const [dayName, day] of Object.entries(this.schedule)) {
                    if (day.enabled) {
                        for (const slot of day.slots) {
                            if (!slot.start_time || !slot.end_time) {
                                throw new Error(`Please fill in all time slots for ${dayName}`);
                            }
                            if (slot.start_time >= slot.end_time) {
                                throw new Error(`End time must be after start time for ${dayName}`);
                            }
                        }
                    }
                }

                const availability = Object.entries(this.schedule).map(([day, config]) => ({
                    // ensure day is a normalized lowercase weekday string
                    day: String(day).toLowerCase(),
                    enabled: !!config.enabled,
                    slots: Array.isArray(config.slots) ? config.slots : []
                }));

                const response = await fetch('/astrologer/update-availability', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        availability: availability,
                        timezone: this.timezone,
                        is_online: this.isOnline
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Availability updated successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Failed to update availability');
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
