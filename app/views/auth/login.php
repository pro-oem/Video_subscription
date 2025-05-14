<?php require_once 'app/views/layouts/main.php'; ?>

<div class="min-h-screen flex flex-col md:flex-row">
    <!-- Left Side - Hero Section -->
    <div class="md:w-1/2 bg-gradient-to-br from-blue-600 to-indigo-800 p-8 md:p-12 lg:p-16 flex items-center justify-center">
        <div class="max-w-2xl">
            <div class="mb-8 flex items-center">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-white">Premium Video Platform</h1>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-6">Watch Premium Content Anywhere</h2>
            <p class="text-lg text-blue-100 mb-8">Access thousands of premium videos, tutorials, and exclusive content. Learn from experts and enhance your skills.</p>
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-4">
                    <div class="text-3xl font-bold text-white mb-1">10K+</div>
                    <div class="text-blue-100">Premium Videos</div>
                </div>
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-4">
                    <div class="text-3xl font-bold text-white mb-1">50K+</div>
                    <div class="text-blue-100">Active Users</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="md:w-1/2 flex items-center justify-center p-8 md:p-12 lg:p-16 bg-gradient-to-br from-gray-900 to-gray-800">
        <div class="w-full max-w-md">
            <!-- Logo for Mobile -->
            <div class="md:hidden flex justify-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        
        <!-- Heading -->
        <div class="text-center">
            <h2 class="text-3xl font-bold text-white tracking-tight">
                Welcome Back
            </h2>
            <p class="mt-2 text-gray-400">
                Don't have an account?
                <a href="<?= BASE_URL ?>/auth/register" class="text-accent-100 hover:text-accent-200 font-medium">
                    Sign up for free
                </a>
            </p>
        </div><?php if (isset($error)): ?>
            <div class="bg-red-900/50 border border-red-500 text-red-300 px-4 py-3 rounded relative mt-4" role="alert">
                <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-900/50 border border-green-500 text-green-300 px-4 py-3 rounded relative mt-4" role="alert">
                <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success']) ?></span>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>        <!-- Error/Success Messages -->
        <?php if (isset($error)): ?>
            <div class="mt-6 animate-shake">
                <div class="rounded-lg bg-red-900/50 border border-red-500/50 p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-red-300"><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mt-6 animate-fade-in">
                <div class="rounded-lg bg-green-900/50 border border-green-500/50 p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-green-300"><?= htmlspecialchars($_SESSION['success']) ?></p>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Login Form -->
        <form class="mt-8 space-y-6" action="<?= BASE_URL ?>/auth/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="space-y-5">
                <!-- Email Field -->
                <div class="form-group">
                    <label for="email" class="form-label flex items-center justify-between">
                        <span>Email address</span>
                        <span class="text-xs text-gray-400">(required)</span>
                    </label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" required 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               class="pl-10 w-full rounded-lg px-3 py-2.5 bg-dark-300/50 border border-dark-400 text-white placeholder-gray-400 focus:ring-2 focus:ring-accent-100 focus:border-transparent"
                               placeholder="you@example.com">
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label flex items-center justify-between">
                        <span>Password</span>
                        <span class="text-xs text-gray-400">(required)</span>
                    </label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required
                               class="pl-10 w-full rounded-lg px-3 py-2.5 bg-dark-300/50 border border-dark-400 text-white placeholder-gray-400 focus:ring-2 focus:ring-accent-100 focus:border-transparent"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-300">
                            <svg id="eye-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>            <!-- Additional Options -->
            <div class="flex items-center justify-between mt-6">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" 
                           class="h-4 w-4 rounded bg-dark-300/50 border-dark-400 text-accent-100 focus:ring-accent-100 focus:ring-offset-0">
                    <label for="remember-me" class="ml-2 text-sm text-gray-300 select-none">
                        Remember me
                    </label>
                </div>

                <a href="<?= BASE_URL ?>/auth/forgot-password" 
                   class="text-sm font-medium text-accent-100 hover:text-accent-200 hover:underline">
                    Forgot password?
                </a>
            </div>            <!-- Submit Button -->
            <button type="submit" 
                    class="mt-6 w-full py-3.5 px-4 border border-transparent rounded-xl text-base font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-gray-900 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center shadow-lg shadow-blue-500/25">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Sign in to your account
            </button>
        </form>

        <!-- Social Login -->
        <div class="mt-8">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-gray-900 text-gray-400">Or continue with</span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4">
                <!-- Google -->
                <button type="button" 
                        class="w-full inline-flex justify-center items-center px-4 py-3 rounded-xl text-gray-200 bg-white/5 hover:bg-white/10 border border-gray-700 backdrop-blur-xl transition-all duration-200 text-sm font-medium group">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 48 48">
                        <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                        <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                        <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                        <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                    </svg>
                    <span class="group-hover:text-white transition-colors">Sign in with Google</span>
                </button>

                <!-- LinkedIn -->
                <button type="button" 
                        class="w-full inline-flex justify-center items-center px-4 py-3 rounded-xl text-gray-200 bg-white/5 hover:bg-white/10 border border-gray-700 backdrop-blur-xl transition-all duration-200 text-sm font-medium group">
                    <svg class="w-5 h-5 mr-2 text-[#0A66C2]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                    </svg>
                    <span class="group-hover:text-white transition-colors">Sign in with LinkedIn</span>
                </button>
            </div>
        </div>

        <!-- Footer Text -->
        <p class="mt-8 text-center text-sm text-gray-400">
            By signing in, you agree to our
            <a href="<?= BASE_URL ?>/terms" class="text-blue-400 hover:text-blue-300 hover:underline">Terms of Service</a>
            and
            <a href="<?= BASE_URL ?>/privacy" class="text-blue-400 hover:text-blue-300 hover:underline">Privacy Policy</a>
        </p>
    </div>
</div>

<!-- Password Toggle Script -->
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
        `;
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        `;
    }
}
</script>
    </div>
</div>

<script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
    </script>