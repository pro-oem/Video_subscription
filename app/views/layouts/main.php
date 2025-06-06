<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Video Platform' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark: {
                            100: '#1a202c',
                            200: '#2d3748',
                            300: '#4a5568',
                            400: '#718096',
                            500: '#a0aec0'
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #1a202c;
            color: #e2e8f0;
        }
        input, textarea, select {
            background-color: #2d3748 !important;
            border-color: #4a5568 !important;
            color: #e2e8f0 !important;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #4f46e5 !important;
            ring-color: #4f46e5 !important;
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
        }
        .btn-primary {
            background-color: #4f46e5;
            color: white;
        }
        .btn-primary:hover {
            background-color: #4338ca;
        }
        .btn-secondary {
            background-color: #2d3748;
            color: #e2e8f0;
        }
        .btn-secondary:hover {
            background-color: #4a5568;
        }
    </style>
<body class="min-h-screen bg-dark-100">
    <nav class="bg-dark-200 border-b border-dark-300 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?= BASE_URL ?>" class="flex items-center">
                        <span class="text-xl font-bold text-white hover:text-dark-500 transition-colors">Video Platform</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <a href="<?= BASE_URL ?>/admin" class="text-dark-500 hover:text-white transition-colors font-medium">Admin Panel</a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/content" class="text-dark-500 hover:text-white transition-colors font-medium">Content</a>
                        <form action="<?= BASE_URL ?>/auth/logout" method="post" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <button type="submit" class="btn btn-secondary">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/auth/login" class="btn btn-secondary">Login</a>
                        <a href="<?= BASE_URL ?>/auth/register" class="btn btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6">
                <div class="bg-red-900/50 border border-red-800 text-red-200 px-4 py-3 rounded-lg shadow-sm">
                    <?= $_SESSION['error'] ?>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6">
                <div class="bg-green-900/50 border border-green-800 text-green-200 px-4 py-3 rounded-lg shadow-sm">
                    <?= $_SESSION['success'] ?>
                </div>            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($content) && !empty($content)): ?>
            <div class="bg-dark-200 rounded-lg shadow-xl p-6">
                <?= $content ?>
            </div>
        <?php endif; ?>
    </main>    <footer class="bg-dark-200 mt-8 border-t border-dark-300">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-dark-500">&copy; <?= date('Y') ?> Video Platform. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
