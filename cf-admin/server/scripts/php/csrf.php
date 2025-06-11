<?php
session_start();

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Send token to frontend (JSON example)
echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
