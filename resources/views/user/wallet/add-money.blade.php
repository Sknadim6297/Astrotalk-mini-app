@extends('layouts.auth-guard')

@section('title', 'Add Money to Wallet')

@section('protected-content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('wallet.index') }}" class="text-purple-600 hover:text-purple-700 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-plus-circle text-purple-600 mr-3"></i>Add Money
                    </h1>
                    <p class="text-gray-600 mt-1">Add funds to your wallet for seamless consultations</p>
                </div>
            </div>
        </div>

        <!-- Current Balance Display -->
        <div class="bg-white rounded-xl p-6 mb-8 shadow-lg" x-data="walletBalance()">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Current Wallet Balance</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(balance)">
                        ₹0.00
                    </p>
                </div>
                <div class="text-3xl text-purple-600">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>

        <!-- Add Money Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-credit-card text-gray-500 mr-2"></i>Add Funds
                </h3>
            </div>

            <div class="p-6">
                <!-- Amount Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Choose Amount</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
                        <button onclick="selectAmount(100)" class="amount-btn border-2 border-gray-200 rounded-lg p-4 text-center hover:border-purple-500 hover:bg-purple-50 transition-all duration-200">
                            <div class="text-lg font-semibold text-gray-900">₹100</div>
                        </button>
                        <button onclick="selectAmount(500)" class="amount-btn border-2 border-gray-200 rounded-lg p-4 text-center hover:border-purple-500 hover:bg-purple-50 transition-all duration-200">
                            <div class="text-lg font-semibold text-gray-900">₹500</div>
                        </button>
                        <button onclick="selectAmount(1000)" class="amount-btn border-2 border-gray-200 rounded-lg p-4 text-center hover:border-purple-500 hover:bg-purple-50 transition-all duration-200">
                            <div class="text-lg font-semibold text-gray-900">₹1,000</div>
                        </button>
                        <button onclick="selectAmount(2000)" class="amount-btn border-2 border-gray-200 rounded-lg p-4 text-center hover:border-purple-500 hover:bg-purple-50 transition-all duration-200">
                            <div class="text-lg font-semibold text-gray-900">₹2,000</div>
                        </button>
                        <button onclick="selectAmount(5000)" class="amount-btn border-2 border-gray-200 rounded-lg p-4 text-center hover:border-purple-500 hover:bg-purple-50 transition-all duration-200">
                            <div class="text-lg font-semibold text-gray-900">₹5,000</div>
                        </button>
                        <button onclick="selectAmount(10000)" class="amount-btn border-2 border-gray-200 rounded-lg p-4 text-center hover:border-purple-500 hover:bg-purple-50 transition-all duration-200">
                            <div class="text-lg font-semibold text-gray-900">₹10,000</div>
                        </button>
                    </div>
                </div>

                <!-- Custom Amount Input -->
                <div class="mb-6">
                    <label for="custom-amount" class="block text-sm font-medium text-gray-700 mb-2">Or enter custom amount</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-lg">₹</span>
                        </div>
                        <input type="number" 
                               id="custom-amount" 
                               placeholder="Enter amount" 
                               min="1" 
                               max="50000"
                               class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-lg"
                               oninput="updateAmount(this.value)">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Minimum: ₹1 • Maximum: ₹50,000</p>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                    <input type="text" 
                           id="description" 
                           placeholder="e.g., Top-up for consultations"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Amount Summary -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6" id="amount-summary" style="display: none;">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Amount to add:</span>
                        <span class="text-xl font-bold text-purple-600" id="selected-amount">₹0</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-gray-600">New balance will be:</span>
                        <span class="text-lg font-semibold text-gray-900" id="new-balance">₹0.00</span>
                    </div>
                </div>

                <!-- Add Money Button -->
                <button onclick="addMoney()" 
                        id="add-money-btn"
                        disabled
                        class="w-full bg-gray-400 text-white py-4 rounded-lg font-semibold text-lg transition-all duration-200 disabled:cursor-not-allowed">
                    <i class="fas fa-plus mr-2"></i>Add Money to Wallet
                </button>

                <!-- Security Note -->
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-shield-alt text-blue-600 mt-1 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Secure Transaction</p>
                            <p class="text-xs text-blue-600 mt-1">Your payment information is encrypted and secure. Funds will be added instantly to your wallet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-8 max-w-md mx-4 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-green-600 text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Money Added Successfully!</h3>
        <p class="text-gray-600 mb-6" id="success-message"></p>
        <button onclick="closeSuccessModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">
            Continue
        </button>
    </div>
</div>

<script>
// Global variables
let selectedAmountValue = 0;
let currentBalance = 0;

// Alpine.js component for wallet balance
function walletBalance() {
    return {
        balance: 0,
        
        init() {
            this.loadBalance();
        },
        
        async loadBalance() {
                try {
                    const response = await fetch('/wallet/data', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        credentials: 'same-origin'
                    });

                    const data = await response.json();
                    console.log('Balance API Response:', data);

                    if (data.status === 'success') {
                        this.balance = parseFloat(data.data.balance || 0);
                        currentBalance = this.balance;
                        console.log('Current balance set to:', currentBalance);
                    } else {
                        console.error('Failed to load balance:', data.message);
                    }
                } catch (error) {
                    console.error('Error loading balance:', error);
                }
        },
        
        formatCurrency(amount) {
            return '₹' + parseFloat(amount || 0).toLocaleString('en-IN', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });
        }
    }
}

// Make Alpine component globally available
window.walletBalance = walletBalance;

// Global function to select amount
function selectAmount(amount) {
    console.log('selectAmount called with:', amount);
    
    // Remove selected class from all buttons
    document.querySelectorAll('.amount-btn').forEach(btn => {
        btn.classList.remove('border-purple-500', 'bg-purple-50');
        btn.classList.add('border-gray-200');
    });
    
    // Add selected class to clicked button
    const clickedBtn = event.target.closest('.amount-btn');
    if (clickedBtn) {
        clickedBtn.classList.add('border-purple-500', 'bg-purple-50');
        clickedBtn.classList.remove('border-gray-200');
    }
    
    // Clear custom amount input
    document.getElementById('custom-amount').value = '';
    
    // Update amount
    updateAmount(amount);
}

// Global function to update amount
function updateAmount(amount) {
    selectedAmountValue = parseFloat(amount) || 0;
    console.log('Selected amount:', selectedAmountValue);
    
    if (selectedAmountValue > 0) {
        // Show amount summary
        document.getElementById('amount-summary').style.display = 'block';
        document.getElementById('selected-amount').textContent = '₹' + selectedAmountValue.toLocaleString();
        document.getElementById('new-balance').textContent = '₹' + (currentBalance + selectedAmountValue).toLocaleString();
        
        // Enable add money button
        const addBtn = document.getElementById('add-money-btn');
        addBtn.disabled = false;
        addBtn.classList.remove('bg-gray-400');
        addBtn.classList.add('bg-purple-600', 'hover:bg-purple-700');
        console.log('Button enabled');
        
        // Clear amount button selections if using custom input
        if (event && event.target && event.target.id === 'custom-amount') {
            document.querySelectorAll('.amount-btn').forEach(btn => {
                btn.classList.remove('border-purple-500', 'bg-purple-50');
                btn.classList.add('border-gray-200');
            });
        }
    } else {
        // Hide amount summary
        document.getElementById('amount-summary').style.display = 'none';
        
        // Disable add money button
        const addBtn = document.getElementById('add-money-btn');
        addBtn.disabled = true;
        addBtn.classList.add('bg-gray-400');
        addBtn.classList.remove('bg-purple-600', 'hover:bg-purple-700');
        console.log('Button disabled');
    }
}

// Global function to add money
async function addMoney() {
    console.log('addMoney called, amount:', selectedAmountValue);
    
    if (selectedAmountValue <= 0) {
        alert('Please select an amount first');
        return;
    }
    
    // Use session authentication; no API token required
    
    const addBtn = document.getElementById('add-money-btn');
    const originalText = addBtn.innerHTML;
    
    // Show loading state
    addBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    addBtn.disabled = true;
    
    try {
        const description = document.getElementById('description').value || 'Wallet top-up';
        
        console.log('Sending API request with amount:', selectedAmountValue);
        
        const response = await fetch('/wallet/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                amount: selectedAmountValue,
                description: description
            })
        });
        
        const data = await response.json();
        console.log('API Response:', response.status, data);
        
    if (response.ok && data.status === 'success') {
            // Update current balance
            currentBalance = parseFloat(data.data.new_balance || 0);
            
            // Update Alpine.js balance display
            const balanceComponent = document.querySelector('[x-data*="walletBalance"]');
            if (balanceComponent && balanceComponent._x_dataStack) {
                balanceComponent._x_dataStack[0].balance = currentBalance;
            }
            
            // Show success modal
            document.getElementById('success-message').textContent = 
                `₹${selectedAmountValue.toLocaleString()} has been added to your wallet. Your new balance is ₹${currentBalance.toLocaleString()}.`;
            document.getElementById('success-modal').classList.remove('hidden');
            document.getElementById('success-modal').classList.add('flex');
            
            // Reset form
            resetForm();
        } else {
            let errorMessage = data.message || 'Failed to add money';
            if (data.errors) {
                errorMessage += '\n\nValidation errors:\n';
                Object.keys(data.errors).forEach(key => {
                    errorMessage += '- ' + data.errors[key].join(', ') + '\n';
                });
            }
            throw new Error(errorMessage);
        }
    } catch (error) {
        console.error('Add money error:', error);
        alert('Error: ' + error.message);
    } finally {
        // Restore button state
        addBtn.innerHTML = originalText;
        addBtn.disabled = selectedAmountValue <= 0;
    }
}

// Global function to close success modal
function closeSuccessModal() {
    document.getElementById('success-modal').classList.add('hidden');
    document.getElementById('success-modal').classList.remove('flex');
}

// Global function to reset form
function resetForm() {
    selectedAmountValue = 0;
    document.getElementById('custom-amount').value = '';
    document.getElementById('description').value = '';
    document.getElementById('amount-summary').style.display = 'none';
    
    // Clear button selections
    document.querySelectorAll('.amount-btn').forEach(btn => {
        btn.classList.remove('border-purple-500', 'bg-purple-50');
        btn.classList.add('border-gray-200');
    });
    
    // Disable add money button
    const addBtn = document.getElementById('add-money-btn');
    addBtn.disabled = true;
    addBtn.classList.add('bg-gray-400');
    addBtn.classList.remove('bg-purple-600', 'hover:bg-purple-700');
}

// Make functions globally available
window.selectAmount = selectAmount;
window.updateAmount = updateAmount;
window.addMoney = addMoney;
window.closeSuccessModal = closeSuccessModal;
window.resetForm = resetForm;

// Debug: Check authentication on page load
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('api_token');
    const userData = localStorage.getItem('user_data');
    console.log('Page loaded - Auth check:');
    console.log('Token present:', token ? 'Yes' : 'No');
    console.log('User data present:', userData ? 'Yes' : 'No');
    
    // If session auth is not present, the auth-guard layout will handle redirection.
});
</script>
@endsection
