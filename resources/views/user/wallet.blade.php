@extends('layouts.auth-guard')

@section('title', 'My Wallet - AstroConnect')

@section('protected-content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white card-shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-wallet mr-2"></i>My Wallet</h1>
                <p class="text-gray-600">Manage your account balance and payment history</p>
            </div>
            
            <div class="p-6">
                <div class="text-center py-12">
                    <div class="text-6xl mb-4"><i class="fas fa-wallet text-green-500"></i></div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Wallet Management</h2>
                    <p class="text-gray-600 mb-6">Your wallet balance and transaction history will be available here.</p>
                    
                    <div class="max-w-sm mx-auto space-y-3">
                        <button class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                            Add Funds
                        </button>
                        <button class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 transition-colors">
                            Transaction History
                        </button>
                        <a href="{{ url('/dashboard') }}" class="block w-full bg-gray-50 text-gray-600 py-2 px-4 rounded-md hover:bg-gray-100 transition-colors">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
