<?php $title = 'Login - ' . APP_NAME; ?>

<div class="flex min-h-[calc(100vh-8rem)] flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-white">
            Sign in to your account
        </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-dark-200 py-8 px-4 shadow-xl rounded-lg sm:px-10">
            <?php if (isset($error)): ?>
                <div class="mb-4 bg-red-500/10 border border-red-500 text-red-400 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="<?= BASE_URL ?>/auth/login" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-200">
                        Email address
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                               class="block w-full appearance-none rounded-md border border-dark-300 bg-dark-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm text-white" 
                               placeholder="Enter your email">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-200">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="block w-full appearance-none rounded-md border border-dark-300 bg-dark-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm text-white"
                               placeholder="Enter your password">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox"
                               class="h-4 w-4 rounded border-dark-300 bg-dark-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-200">
                            Remember me
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Sign in
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-dark-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-dark-200 px-2 text-gray-400">Or</span>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-400">
                        Don't have an account?
                        <a href="<?= BASE_URL ?>/auth/register" class="font-medium text-indigo-500 hover:text-indigo-400">
                            Register here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

