<?php require_once 'app/views/layouts/main.php'; ?>

<div class="min-h-screen bg-dark-100 flex flex-col items-center justify-center px-4">
    <div class="max-w-md w-full bg-dark-200 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-center">
                <svg class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h2 class="mt-4 text-center text-xl font-semibold text-white">
                <?= $title ?? 'An Error Occurred' ?>
            </h2>
            
            <p class="mt-2 text-center text-gray-400">
                <?= $message ?? 'Something went wrong. Please try again.' ?>
            </p>

            <?php if (isset($details) && !empty($details)): ?>
                <div class="mt-4 bg-dark-300 rounded p-4">
                    <p class="text-sm text-gray-400">
                        <?= $details ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="mt-6 flex justify-center">
                <?php if (isset($backUrl)): ?>
                    <a href="<?= $backUrl ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <?= $backText ?? 'Go Back' ?>
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Return to Home
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>