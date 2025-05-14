<?php
require_once __DIR__ . '/../config/config.php';

// Check and create required directories
$directories = [
    __DIR__ . '/../public/css',
    __DIR__ . '/../public/js',
    __DIR__ . '/../public/images',
    __DIR__ . '/../uploads',
    __DIR__ . '/../uploads/content'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: " . $dir . "\n";
    }
}

// Copy CSS file if it doesn't exist
$cssSource = __DIR__ . '/../public/css/style.css';
if (!file_exists($cssSource)) {
    file_put_contents($cssSource, file_get_contents(__DIR__ . '/../app/assets/css/style.css'));
    echo "Created CSS file: style.css\n";
}

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE " . DB_NAME);    // Drop existing tables if exists to recreate with correct structure
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DROP TABLE IF EXISTS access_tokens");
    $pdo->exec("DROP TABLE IF EXISTS access_logs");
    $pdo->exec("DROP TABLE IF EXISTS content_access_log");
    $pdo->exec("DROP TABLE IF EXISTS content_views");
    $pdo->exec("DROP TABLE IF EXISTS chat_history");
    $pdo->exec("DROP TABLE IF EXISTS user_restrictions");
    $pdo->exec("DROP TABLE IF EXISTS security_events");
    $pdo->exec("DROP TABLE IF EXISTS subscriptions");
    $pdo->exec("DROP TABLE IF EXISTS content");
    $pdo->exec("DROP TABLE IF EXISTS users");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    $pdo->exec("DROP TABLE IF EXISTS access_logs");

    // Create users table with correct structure
    $pdo->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_admin TINYINT(1) DEFAULT 0,
        requires_verification TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX email_idx (email)
    )");

    // Create admin and demo users
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $demoPassword = password_hash('demo123', PASSWORD_DEFAULT);
    
    $pdo->exec("INSERT INTO users (email, password, is_admin) VALUES 
                ('admin@example.com', '$adminPassword', 1),
                ('demo@example.com', '$demoPassword', 0)");

    // Create subscriptions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        plan_type VARCHAR(50) NOT NULL,
        subscription_end DATETIME NOT NULL,
        payment_id VARCHAR(255),
        amount DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        INDEX user_sub_idx (user_id, subscription_end)
    )");

    // Create content table
    $pdo->exec("CREATE TABLE IF NOT EXISTS content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        file_path VARCHAR(255) NOT NULL,
        content_type VARCHAR(50) NOT NULL,
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX title_idx (title),
        INDEX content_type_idx (content_type)
    )");

    // Create chat_history table
    $pdo->exec("CREATE TABLE IF NOT EXISTS chat_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        message TEXT NOT NULL,
        response TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        INDEX user_chat_idx (user_id, created_at)
    )");

    // Create content_views table
    $pdo->exec("CREATE TABLE IF NOT EXISTS content_views (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content_id INT NOT NULL,
        user_id INT NOT NULL,
        viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (content_id) REFERENCES content(id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        UNIQUE KEY unique_view (content_id, user_id),
        INDEX content_view_idx (content_id, user_id, viewed_at)
    )");

    // Create content access logging table
    $pdo->exec("CREATE TABLE IF NOT EXISTS content_access_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content_id INT NOT NULL,
        user_id INT NOT NULL,
        access_type VARCHAR(50) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        user_agent TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (content_id) REFERENCES content(id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        INDEX access_time_idx (created_at),
        INDEX user_access_idx (user_id, created_at)
    )");

    // Create security events table
    $pdo->exec("CREATE TABLE IF NOT EXISTS security_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        event_type VARCHAR(50) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        INDEX event_type_idx (event_type),
        INDEX user_event_idx (user_id, created_at)
    )");

    // Create user restrictions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_restrictions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        restriction_type VARCHAR(50) NOT NULL,
        reason TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at DATETIME NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id),
        INDEX user_restriction_idx (user_id, expires_at)
    )");

    // Create access tokens table
    $pdo->exec("CREATE TABLE IF NOT EXISTS access_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        content_id INT NOT NULL,
        token VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at DATETIME NOT NULL,
        used TINYINT(1) DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (content_id) REFERENCES content(id),
        UNIQUE KEY unique_token (token),
        INDEX token_validity_idx (token, expires_at, used)
    )");

    // Create access_logs table
    $pdo->exec("CREATE TABLE access_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        content_id INT NOT NULL,
        action VARCHAR(50) NOT NULL,
        status VARCHAR(20) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        user_agent TEXT,
        additional_info TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
        INDEX user_action_idx (user_id, action),
        INDEX content_access_idx (content_id, created_at)
    )");

    echo "Database initialized successfully!\n";
    echo "Default admin credentials:\n";
    echo "Email: admin@example.com\n";
    echo "Password: admin123\n\n";
    echo "Demo user credentials:\n";
    echo "Email: demo@example.com\n";
    echo "Password: demo123\n";
    echo "\nPlease change these credentials after first login!";

} catch (PDOException $e) {
    die("Database initialization failed: " . $e->getMessage());
}