@extends('layouts.astrologer')

@section('title', 'Reviews & Ratings')
@section('page-title', 'Reviews & Ratings')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Reviews & Ratings</h1>
        <p class="text-gray-600">View and manage your client reviews and ratings</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Average Rating</h3>
                    <div class="text-2xl font-bold text-gray-900">4.8</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-comments text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Reviews</h3>
                    <div class="text-2xl font-bold text-gray-900">156</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <i class="fas fa-thumbs-up text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">5-Star Reviews</h3>
                    <div class="text-2xl font-bold text-gray-900">89</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">This Month</h3>
                    <div class="text-2xl font-bold text-gray-900">+12</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Breakdown -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Rating Breakdown</h2>
        <div class="space-y-4">
            <div class="flex items-center">
                <span class="text-sm w-12">5 star</span>
                <div class="flex-1 mx-4">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-yellow-500 h-3 rounded-full" style="width: 57%"></div>
                    </div>
                </div>
                <span class="text-sm w-8">89</span>
            </div>
            <div class="flex items-center">
                <span class="text-sm w-12">4 star</span>
                <div class="flex-1 mx-4">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-yellow-500 h-3 rounded-full" style="width: 25%"></div>
                    </div>
                </div>
                <span class="text-sm w-8">39</span>
            </div>
            <div class="flex items-center">
                <span class="text-sm w-12">3 star</span>
                <div class="flex-1 mx-4">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-yellow-500 h-3 rounded-full" style="width: 12%"></div>
                    </div>
                </div>
                <span class="text-sm w-8">19</span>
            </div>
            <div class="flex items-center">
                <span class="text-sm w-12">2 star</span>
                <div class="flex-1 mx-4">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-yellow-500 h-3 rounded-full" style="width: 4%"></div>
                    </div>
                </div>
                <span class="text-sm w-8">6</span>
            </div>
            <div class="flex items-center">
                <span class="text-sm w-12">1 star</span>
                <div class="flex-1 mx-4">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-yellow-500 h-3 rounded-full" style="width: 2%"></div>
                    </div>
                </div>
                <span class="text-sm w-8">3</span>
            </div>
        </div>
    </div>

    <!-- Recent Reviews -->
    <div class="bg-white rounded-xl shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Reviews</h2>
        </div>
        <div class="divide-y divide-gray-200">
            <!-- Sample Review -->
            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-purple-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h4 class="font-semibold text-gray-900">Priya Sharma</h4>
                                <div class="flex items-center space-x-2">
                                    <div class="flex">
                                        <i class="fas fa-star text-yellow-500"></i>
                                        <i class="fas fa-star text-yellow-500"></i>
                                        <i class="fas fa-star text-yellow-500"></i>
                                        <i class="fas fa-star text-yellow-500"></i>
                                        <i class="fas fa-star text-yellow-500"></i>
                                    </div>
                                    <span class="text-sm text-gray-500">2 days ago</span>
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Verified</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-3">Amazing accuracy in predictions! Really helped me make important life decisions. The astrologer was very patient and explained everything clearly.</p>
                        <div class="flex space-x-4 text-sm">
                            <button class="text-purple-600 hover:text-purple-700">Reply</button>
                            <button class="text-gray-500 hover:text-gray-700">Report</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- More sample reviews -->
            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-purple-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h4 class="font-semibold text-gray-900">Rajesh Kumar</h4>
                                <div class="flex items-center space-x-2">
                                    <div class="flex">
                                        <i class="fas fa-star text-yellow-500"></i>
                                        <i class="fas fa-star text-yellow-500"></i>
                                        <i class="fas fa-star text-yellow-500"></i>
                                        <i class="fas fa-star text-yellow-500"></i>
                                        <i class="far fa-star text-gray-300"></i>
                                    </div>
                                    <span class="text-sm text-gray-500">5 days ago</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-3">Very insightful session about career prospects. The predictions were detailed and practical advice was given.</p>
                        <div class="flex space-x-4 text-sm">
                            <button class="text-purple-600 hover:text-purple-700">Reply</button>
                            <button class="text-gray-500 hover:text-gray-700">Report</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-purple-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h4 class="font-semibold text-gray-900">Meera Iyer</h4>
                                <div class="flex items-center space-x-2">
                                    <div class="flex">
                                        <i class="fas fa-star text-yellow-500"></i>
                                        <i class="fas fa-star text-yellow-500"></i>
                                        <i class="fas fa-star text-yellow-500"></i>
                                        <i class="fas fa-star text-yellow-500"></i>
                                        <i class="fas fa-star text-yellow-500"></i>
                                    </div>
                                    <span class="text-sm text-gray-500">1 week ago</span>
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Verified</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-3">Excellent guidance for relationship matters. The astrologer understood my situation well and provided helpful remedies.</p>
                        <div class="flex space-x-4 text-sm">
                            <button class="text-purple-600 hover:text-purple-700">Reply</button>
                            <button class="text-gray-500 hover:text-gray-700">Report</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Load More -->
        <div class="px-6 py-4 border-t border-gray-200 text-center">
            <button class="text-purple-600 hover:text-purple-700 font-medium">Load More Reviews</button>
        </div>
    </div>
</div>
@endsection
