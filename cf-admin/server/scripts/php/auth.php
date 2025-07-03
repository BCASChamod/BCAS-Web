<?php
// login.php - Combined authentication system
require './config.php';

// Configure secure session settings
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
    'lifetime' => 3600 // 1 hour session timeout
]);
session_start();

// Set JSON response header
header('Content-Type: application/json');

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Return CSRF token for form initialization
        echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
        break;
        
    case 'POST':
        handleLogin();
        break;
        
    case 'DELETE':
        handleLogout();
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

function handleLogin() {
    global $conn;
    
    // Get JSON input
    $input = json_decode(file_get_contents("php://input"), true);
    
    // Validate JSON input
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
        return;
    }
    
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';
    $csrf_token = $input['csrf_token'] ?? '';
    
    // Basic input validation
    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username and password required']);
        return;
    }
    
    // CSRF token validation (recommended)
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        return;
    }
    
    // Enhanced rate limiting with IP tracking
    $client_ip = getClientIP();
    $rate_limit_key = "login_attempts_{$client_ip}";
    
    if (!isset($_SESSION[$rate_limit_key])) {
        $_SESSION[$rate_limit_key] = ['count' => 0, 'last_attempt' => time()];
    }
    
    // Reset attempts if more than 15 minutes have passed
    if (time() - $_SESSION[$rate_limit_key]['last_attempt'] > 900) {
        $_SESSION[$rate_limit_key] = ['count' => 0, 'last_attempt' => time()];
    }
    
    if ($_SESSION[$rate_limit_key]['count'] >= 5) {
        http_response_code(429);
        echo json_encode(['success' => false, 'message' => 'Too many attempts. Try again later.']);
        return;
    }
    
    // Fetch user with prepared statement
    $stmt = $conn->prepare("SELECT id, username, password_hash, is_active FROM users WHERE username = ? LIMIT 1");
    if (!$stmt) {
        error_log("Database prepare failed: " . $conn->error);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        return;
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Check if user account is active
        if (!$user['is_active']) {
            $_SESSION[$rate_limit_key]['count']++;
            $_SESSION[$rate_limit_key]['last_attempt'] = time();
            echo json_encode(['success' => false, 'message' => 'Account is disabled']);
            return;
        }
        
        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // Successful login
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['authenticated'] = true;
            $_SESSION['login_time'] = time();
            
            // Reset rate limiting
            $_SESSION[$rate_limit_key] = ['count' => 0, 'last_attempt' => time()];
            
            // Log successful login
            logSecurityEvent('login_success', $user['id'], $client_ip);
            
            // Generate new CSRF token for next requests
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful',
                'csrf_token' => $_SESSION['csrf_token']
            ]);
            return;
        }
    }
    
    // If login fails
    $_SESSION[$rate_limit_key]['count']++;
    $_SESSION[$rate_limit_key]['last_attempt'] = time();
    
    // Log failed login attempt
    logSecurityEvent('login_failed', null, $client_ip, $username);
    
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
}

function handleLogout() {
    // Log logout event
    if (isset($_SESSION['user_id'])) {
        logSecurityEvent('logout', $_SESSION['user_id'], getClientIP());
    }
    
    // Clear session data
    session_unset();
    session_destroy();
    
    // Clear session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
}

function getClientIP() {
    $ip_keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
            $ip = explode(',', $_SERVER[$key])[0];
            if (filter_var(trim($ip), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return trim($ip);
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function logSecurityEvent($event_type, $user_id = null, $ip_address = null, $username = null) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO security_logs (event_type, user_id, ip_address, username, timestamp) VALUES (?, ?, ?, ?, NOW())");
    if ($stmt) {
        $stmt->bind_param("siss", $event_type, $user_id, $ip_address, $username);
        $stmt->execute();
    }
}

// Check session timeout
function checkSessionTimeout() {
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 3600) {
        session_unset();
        session_destroy();
        return false;
    }
    return true;
}
?>