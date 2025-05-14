<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' - ' . APP_NAME : APP_NAME ?></title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://unpkg.com/@heroicons/react@2.0.18/24/outline/esm/index.css">
    <script src="https://unpkg.com/@heroicons/react@2.0.18/24/outline/esm/index.js"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <nav class="navbar">
        <div class="container navbar-content">
            <a href="<?= BASE_URL ?>" class="logo">
                <?= APP_NAME ?>
            </a>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= BASE_URL ?>/content" class="nav-link">Content</a>
                    <?php if ($_SESSION['is_admin']): ?>
                        <a href="<?= BASE_URL ?>/admin" class="nav-link">Admin</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/auth/logout" class="button button-secondary">Logout</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/auth/login" class="button button-primary">Login</a>
                    <a href="<?= BASE_URL ?>/auth/register" class="button button-secondary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container py-8">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-section">
                <h3 class="footer-title"><?= APP_NAME ?></h3>
                <p>Your trusted video platform</p>
            </div>
            <div class="footer-section">
                <h3 class="footer-title">Links</h3>
                <ul class="space-y-2">
                    <li><a href="<?= BASE_URL ?>" class="footer-link">Home</a></li>
                    <li><a href="<?= BASE_URL ?>/content" class="footer-link">Content</a></li>
                    <li><a href="<?= BASE_URL ?>/auth/subscribe" class="footer-link">Subscribe</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3 class="footer-title">Contact</h3>
                <ul class="space-y-2">
                    <li><a href="mailto:info@example.com" class="footer-link">info@example.com</a></li>
                    <li><a href="tel:+1234567890" class="footer-link">+1 234 567 890</a></li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>