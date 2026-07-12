<?php
/**
 * Database configuration
 * Update these credentials to match your local MySQL setup.
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_portal');
define('DB_USER', 'root');
define('DB_PASS', '');

// Where uploaded profile photos are stored (relative to project root)
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'uploads/');
define('MAX_UPLOAD_BYTES', 2 * 1024 * 1024); // 2 MB
define('ALLOWED_PHOTO_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// The list of branches/courses offered — used to populate dropdowns consistently
define('BRANCHES', ['Computer Science', 'Electronics', 'Mechanical', 'Civil', 'Electrical', 'Information Technology']);

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:2rem;color:#b00020;">
        <h2>Database connection failed</h2>
        <p>' . htmlspecialchars($e->getMessage()) . '</p>
        <p>Make sure you have run <code>schema.sql</code> and updated <code>config.php</code> with your MySQL credentials.</p>
        </div>');
}

/** Small helper to escape output safely everywhere */
function h($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
