<?php require_once 'app/views/layouts/main.php'; ?>

<div class="relative">
    <!-- Hero Section -->
    <div class="relative min-h-[85vh] overflow-hidden bg-gradient-to-br from-indigo-900 via-blue-900 to-indigo-950">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
            </div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
            <?php if (isset($isSubscribed) && $isSubscribed): ?>
                <div class="space-y-8">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-white">Featured Content</h1>
                        <div class="flex space-x-4">
                            <button class="inline-flex items-center px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-sm font-medium transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                                </svg>
                                Sort
                            </button>
                            <button class="inline-flex items-center px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-sm font-medium transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Filter
                            </button>
                        </div>
                    </div>

                    <!-- Content Grid -->
                    <?php if (isset($content) && !empty($content)): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-8">
                            <?php foreach ($content as $item): ?>
                                <div class="group relative overflow-hidden rounded-2xl bg-dark-800/50 hover:shadow-xl transition duration-300 
                                          hover:transform hover:-translate-y-1 backdrop-blur-sm border border-white/10">
                                    <div class="relative aspect-video overflow-hidden">
                                        <?php if (isset($item->content_type) && strpos($item->content_type, 'video') !== false): ?>
                                            <div class="absolute inset-0 bg-dark-900/50 overflow-hidden">
                                                <img src="<?= BASE_URL ?>/assets/images/video-thumbnail.jpg" 
                                                     class="w-full h-full object-cover opacity-90 group-hover:opacity-100 scale-100 group-hover:scale-105 transition duration-500"
                                                     alt="<?= htmlspecialchars($item->title ?? 'Video thumbnail') ?>">
                                                <div class="absolute inset-0 flex items-center justify-center opacity-90 group-hover:opacity-100 transition-opacity">
                                                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-indigo-600 to-blue-600 p-1
                                                                flex items-center justify-center transform group-hover:scale-110 transition-all duration-300
                                                                shadow-[0_0_30px_rgba(79,70,229,0.5)]">
                                                        <div class="w-full h-full rounded-full bg-dark-900/30 backdrop-blur-sm flex items-center justify-center">
                                                            <svg class="w-8 h-8 text-white transform translate-x-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <img src="<?= BASE_URL ?>/content/stream/<?= $item->id ?? 0 ?>"
                                                 class="w-full h-full object-cover opacity-90 group-hover:opacity-100 scale-100 group-hover:scale-105 transition duration-500"
                                                 alt="<?= htmlspecialchars($item->title ?? 'Content image') ?>">
                                        <?php endif; ?>
                                        <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-dark-900/20 to-transparent opacity-60"></div>
                                    </div>
                                    <div class="relative p-6">
                                        <h3 class="font-bold text-lg text-white group-hover:text-indigo-400 truncate transition duration-300">
                                            <?= htmlspecialchars($item->title ?? 'Untitled') ?>
                                        </h3>
                                        <p class="mt-2 text-sm text-gray-300/90 line-clamp-2">
                                            <?= htmlspecialchars($item->description ?? '') ?>
                                        </p>
                                        <div class="mt-4 flex items-center justify-between">
                                            <div class="flex items-center text-sm text-gray-400/90">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <?= isset($item->created_at) ? date('M j, Y', strtotime($item->created_at)) : 'No date' ?>
                                            </div>
                                            <a href="<?= BASE_URL ?>/content/view/<?= $item->id ?? 0 ?>"
                                               class="inline-flex items-center text-sm font-semibold text-indigo-400 hover:text-indigo-300 transition duration-300">
                                                Watch now
                                                <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <p class="text-gray-400">No content available yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Landing Page Content -->
                <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-center">
                    <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left relative z-10">
                        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl lg:text-5xl xl:text-6xl">
                            <span class="block">Unleash the Power of</span>
                            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-blue-400">Premium Content</span>
                        </h1>
                        <p class="mt-3 text-base text-blue-100 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl">
                            Dive into a world of exclusive videos curated just for you. Experience stunning quality, engaging content, and seamless streaming.
                        </p>
                        <div class="mt-10 sm:flex sm:justify-center lg:justify-start space-x-4">
                            <a href="<?= BASE_URL ?>/auth/register" 
                               class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-xl
                                      bg-gradient-to-r from-indigo-600 to-blue-600 text-white
                                      hover:from-indigo-500 hover:to-blue-500 transform hover:-translate-y-0.5 transition-all duration-200">
                                Get Started
                                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                            <a href="<?= BASE_URL ?>/pricing" 
                               class="inline-flex items-center px-8 py-3 border border-blue-400/30 text-base font-medium rounded-xl
                                      text-blue-100 bg-blue-400/10 backdrop-blur-sm hover:bg-blue-400/20 transition-all duration-200">
                                View Plans
                            </a>
                        </div>
                    </div>

                    <!-- Hero Preview -->
                    <div class="mt-12 relative sm:max-w-lg sm:mx-auto lg:mt-0 lg:max-w-none lg:mx-0 lg:col-span-6 lg:flex lg:items-center">
                        <div class="relative mx-auto w-full rounded-2xl overflow-hidden shadow-2xl lg:max-w-xl">
                            <div class="relative aspect-video rounded-2xl overflow-hidden bg-dark-800 border border-white/10">
                                <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-blue-600/20"></div>
                                <img src="<?= BASE_URL ?>/assets/images/hero-preview.jpg" 
                                     alt="Video Preview" 
                                     class="absolute inset-0 h-full w-full object-cover opacity-90">
                                <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-dark-900/20 to-transparent"></div>
                                
                                <!-- Play Button -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-20 h-20 rounded-full bg-gradient-to-r from-indigo-600 to-blue-600 p-1
                                                flex items-center justify-center transform hover:scale-110 transition-all duration-300
                                                shadow-[0_0_30px_rgba(79,70,229,0.5)]">
                                        <div class="w-full h-full rounded-full bg-dark-900/30 backdrop-blur-sm flex items-center justify-center">
                                            <svg class="w-10 h-10 text-white transform translate-x-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Floating Elements -->
                            <div class="absolute -top-4 -right-4 w-72 h-72 bg-gradient-to-br from-indigo-600 to-blue-600 rounded-full 
                                      mix-blend-multiply filter blur-2xl opacity-20 animate-blob"></div>
                            <div class="absolute -bottom-4 -left-4 w-72 h-72 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full 
                                      mix-blend-multiply filter blur-2xl opacity-20 animate-blob animation-delay-2000"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Features Section -->
                <div class="mt-24">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                            Why Choose Our Platform?
                        </h2>
                        <p class="mt-4 text-xl text-blue-100/80 max-w-2xl mx-auto">
                            Experience video content like never before with our cutting-edge features.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Features cards here -->
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
    animation: blob 7s infinite;
}
.animation-delay-2000 {
    animation-delay: 2s;
}
</style>