<?php require_once 'app/views/layouts/main.php'; ?>

<div class="bg-dark-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                Choose Your Subscription Plan
            </h2>
            <p class="mt-4 text-xl text-gray-300">
                Get unlimited access to all our premium content
            </p>
        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-3 lg:gap-x-8">
            <!-- Monthly Plan -->
            <div class="relative bg-dark-200 rounded-2xl shadow-lg p-8">
                <div class="text-center">
                    <h3 class="text-2xl font-medium text-white">
                        Monthly Access
                    </h3>
                    <div class="mt-4 flex items-baseline justify-center">
                        <span class="text-5xl font-extrabold text-white">
                            $<?= number_format(MONTHLY_SUBSCRIPTION_PRICE, 2) ?>
                        </span>
                        <span class="ml-1 text-xl font-medium text-gray-400">
                            /month
                        </span>
                    </div>
                </div>

                <div class="mt-8">
                    <ul class="space-y-4">
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="ml-3 text-gray-300">Unlimited access to all content</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="ml-3 text-gray-300">HD quality streaming</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="ml-3 text-gray-300">AI Chat Assistant</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-8">
                    <form action="<?= BASE_URL ?>/auth/process-payment" method="POST" class="space-y-6">
                        <input type="hidden" name="plan" value="monthly">
                        <input type="hidden" name="amount" value="<?= MONTHLY_SUBSCRIPTION_PRICE ?>">
                        
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-300">
                                Select Payment Method
                            </label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" name="payment_method" value="crypto" 
                                           class="h-4 w-4 text-indigo-600 border-gray-700 bg-dark-300 focus:ring-indigo-500"
                                           required>
                                    <label class="ml-3 block text-sm text-gray-300">
                                        Cryptocurrency
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="payment_method" value="card" 
                                           class="h-4 w-4 text-indigo-600 border-gray-700 bg-dark-300 focus:ring-indigo-500">
                                    <label class="ml-3 block text-sm text-gray-300">
                                        Credit/Debit Card
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Crypto Payment Fields -->
                        <div id="crypto-fields" class="space-y-4 hidden">
                            <div>
                                <label class="block text-sm font-medium text-gray-300">
                                    Select Cryptocurrency
                                </label>
                                <select name="crypto_currency" 
                                        class="mt-1 block w-full border-gray-700 rounded-md bg-dark-300 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="BTC">Bitcoin (BTC)</option>
                                    <option value="ETH">Ethereum (ETH)</option>
                                    <option value="USDT">Tether (USDT)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Card Payment Fields -->
                        <div id="card-fields" class="space-y-4 hidden">
                            <div>
                                <label class="block text-sm font-medium text-gray-300">
                                    Card Number
                                </label>
                                <input type="text" name="card_number" 
                                       class="mt-1 block w-full border-gray-700 rounded-md bg-dark-300 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Card Number">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300">
                                        Expiry Date
                                    </label>
                                    <input type="text" name="card_expiry" 
                                           class="mt-1 block w-full border-gray-700 rounded-md bg-dark-300 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="MM/YY">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300">
                                        CVC
                                    </label>
                                    <input type="text" name="card_cvc" 
                                           class="mt-1 block w-full border-gray-700 rounded-md bg-dark-300 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="CVC">
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Subscribe Now
                        </button>
                    </form>
                </div>
            </div>

            <!-- Three Month Plan -->
            <div class="relative bg-dark-200 rounded-2xl shadow-lg p-8">
                <!-- Similar structure as Monthly Plan but with different pricing -->
                <div class="absolute top-0 right-0 -translate-y-1/2 transform">
                    <span class="inline-flex rounded-full bg-indigo-600 px-4 py-1 text-sm font-semibold text-white">
                        Popular
                    </span>
                </div>
                <!-- Rest of the three-month plan content -->
            </div>

            <!-- Annual Plan -->
            <div class="relative bg-dark-200 rounded-2xl shadow-lg p-8">
                <!-- Similar structure as Monthly Plan but with different pricing -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
    const cryptoFields = document.getElementById('crypto-fields');
    const cardFields = document.getElementById('card-fields');

    paymentMethodInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value === 'crypto') {
                cryptoFields.classList.remove('hidden');
                cardFields.classList.add('hidden');
            } else if (this.value === 'card') {
                cardFields.classList.remove('hidden');
                cryptoFields.classList.add('hidden');
            }
        });
    });
});
</script>