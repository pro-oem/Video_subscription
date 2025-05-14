<div class="min-h-screen bg-gradient-to-br from-indigo-600 to-purple-600">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 to-purple-600 -mt-8 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6">
                    Unleash the Power of Premium Content
                </h1>
                <p class="text-lg md:text-xl text-indigo-100 mb-8 max-w-3xl mx-auto">
                    Dive into a world of exclusive videos curated just for you. Experience stunning quality, engaging content, and seamless streaming.
                </p>
                <div class="space-x-4">
                    <a href="<?= BASE_URL ?>/auth/register" class="button button-secondary inline-block">
                        Get Started
                    </a>
                    <a href="<?= BASE_URL ?>/content" class="text-white border-2 border-white button hover:bg-white hover:text-indigo-600 inline-block">
                        Browse Content
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 0h150v150H0z" fill="url(#pattern)" />
                <defs>
                    <pattern id="pattern" patternUnits="userSpaceOnUse" width="40" height="40">
                        <path d="M0 20h40v1H0z" fill="#fff" opacity=".1" />
                        <path d="M20 0v40h1V0z" fill="#fff" opacity=".1" />
                    </pattern>
                </defs>
            </svg>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="card">
                <div class="card-body text-center">
                    <svg class="w-12 h-12 text-indigo-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-xl font-bold mb-2">Premium Quality</h3>
                    <p class="text-gray-600">Experience content in stunning high definition with crystal clear audio.</p>
                </div>
            </div>
            
            <!-- Feature 2 -->
            <div class="card">
                <div class="card-body text-center">
                    <svg class="w-12 h-12 text-indigo-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <h3 class="text-xl font-bold mb-2">Secure Platform</h3>
                    <p class="text-gray-600">Your content and personal data are protected with enterprise-grade security.</p>
                </div>
            </div>
            
            <!-- Feature 3 -->
            <div class="card">
                <div class="card-body text-center">
                    <svg class="w-12 h-12 text-indigo-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <h3 class="text-xl font-bold mb-2">Lightning Fast</h3>
                    <p class="text-gray-600">Stream content instantly with our optimized delivery network.</p>
                </div>
            </div>
        </div>
    </div>
</div>