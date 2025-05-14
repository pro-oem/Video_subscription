<?php require_once 'app/views/layouts/main.php'; ?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="card max-w-md w-full space-y-8 p-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Already have an account?
                <a href="<?= BASE_URL ?>/auth/login" class="font-medium text-accent-100 hover:text-accent-200">
                    Sign in
                </a>
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="<?= BASE_URL ?>/auth/register" method="POST">
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">
                        Email address
                    </label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="appearance-none relative block w-full px-3 py-2 placeholder-gray-500"
                           placeholder="Enter your email">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-1">
                        Password
                    </label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                           class="appearance-none relative block w-full px-3 py-2 placeholder-gray-500"
                           placeholder="Create a password">
                    <p class="mt-1 text-sm text-gray-400">
                        Password must be at least 8 characters long
                    </p>
                </div>
            </div>

            <div>
                <button type="submit" class="primary w-full flex justify-center py-3">
                    Create Account
                </button>
            </div>
        </form>
        
        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-dark-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-dark-200 text-gray-400">Or sign up with</span>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <button type="button" class="secondary w-full">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/>
                </svg>
                Google
            </button>
            <button type="button" class="secondary w-full">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-2 16h-2v-6h2v6zm-1-6.891c-.607 0-1.1-.496-1.1-1.109 0-.612.492-1.109 1.1-1.109s1.1.497 1.1 1.109c0 .613-.493 1.109-1.1 1.109zm8 6.891h-1.998v-2.861c0-1.881-2.002-1.722-2.002 0v2.861h-2v-6h2v1.093c.872-1.616 4-1.736 4 1.548v3.359z"/>
                </svg>
                LinkedIn
            </button>
        </div>
        
        <p class="mt-4 text-xs text-center text-gray-400">
            By signing up, you agree to our
            <a href="#" class="text-accent-100 hover:text-accent-200">Terms of Service</a>
            and
            <a href="#" class="text-accent-100 hover:text-accent-200">Privacy Policy</a>
        </p>
    </div>
</div>