<?php
/**
 * Production Configuration - Use this for hosting environments
 * 
 * Ganti nilai-nilai di bawah sesuai dengan konfigurasi server Anda
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// ============================================
// DATABASE CONFIGURATION
// ============================================

$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'db_absensi_qr';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';

// ============================================
// ATTENDANCE CONFIGURATION
// ============================================

// Jam batas hadir (format HH:MM:SS)
define('BATAS_JAM_MASUK', getenv('ATTENDANCE_DEADLINE_HOUR') ? 
    getenv('ATTENDANCE_DEADLINE_HOUR') . ':' . 
    getenv('ATTENDANCE_DEADLINE_MINUTE') . ':00' : 
    '08:00:00'
);

// Session timeout (dalam detik, default 1 jam)
define('SESSION_TIMEOUT', (int)(getenv('SESSION_TIMEOUT') ?: 3600));

// ============================================
// APPLICATION ENVIRONMENT
// ============================================

define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true' ? true : false);

// Error reporting berdasarkan environment
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// ============================================
// DATABASE CONNECTION
// ============================================

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_PERSISTENT => false,
            // Connection pooling attributes
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            PDO::MYSQL_ATTR_COMPRESS => true
        ]
    );
    
    // Test connection
    $pdo->query('SELECT 1');
} catch(PDOException $e) {
    // Log error tanpa expose detail ke user
    error_log("Database connection failed: " . $e->getMessage());
    
    // Show generic error message
    die("Sistem sedang mengalami gangguan. Silakan coba beberapa saat lagi.");
}

// ============================================
// SECURITY FUNCTIONS
// ============================================

/**
 * Sanitize user input
 */
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token ?? '');
}

/**
 * Redirect with flash message
 */
function redirect_with_message($url, $type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
        'timestamp' => time()
    ];
    header("Location: $url");
    exit;
}

/**
 * Get and clear flash message
 */
function get_flash() {
    $flash = $_SESSION['flash'] ?? null;
    
    // Auto-expire flash messages after 5 seconds
    if ($flash && isset($flash['timestamp'])) {
        if (time() - $flash['timestamp'] > 5) {
            unset($_SESSION['flash']);
            return null;
        }
    }
    
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Session security check
 */
function check_session_timeout() {
    $timeout = SESSION_TIMEOUT;
    
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > $timeout) {
            session_destroy();
            return false;
        }
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Get client IP address
 */
function get_client_ip() {
    $ip = '';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/**
 * Log activity
 */
function log_activity($action, $details = '') {
    $log_message = sprintf(
        "[%s] IP: %s | User: %s | Action: %s | Details: %s\n",
        date('Y-m-d H:i:s'),
        get_client_ip(),
        $_SESSION['admin_name'] ?? 'Guest',
        $action,
        $details
    );
    
    // Log to file atau syslog
    error_log($log_message);
}

// ============================================
// GLOBAL MIDDLEWARE
// ============================================

// Check session timeout
if (!check_session_timeout()) {
    $_SESSION = [];
    session_destroy();
    if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
        header("Location: login.php?expired=1");
        exit;
    }
}

// Set security headers
if (APP_ENV === 'production') {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=self, geolocation=none, payment=none');
}

/**
 * Database query helper
 */
function db_query($sql, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Get database error
 */
function db_error() {
    global $pdo;
    $error = $pdo->errorInfo();
    return $error[2] ?? 'Unknown error';
}
