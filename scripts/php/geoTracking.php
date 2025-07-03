<?php
/**
 * Branch Geo-Tracking API
 * Returns branch data with location-based matching
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');

// Include database configuration
require_once '../../cf-admin/server/scripts/php/config.php';

// Error reporting for development (remove in production)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

/**
 * Get user's real IP address (handles proxies/load balancers)
 */
function getRealIpAddr() {
    $headers = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];
    
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = $_SERVER[$header];
            // Handle comma-separated IPs (from proxies)
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            // Validate IP format
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

/**
 * Fetch location data from IP info service
 */
function getLocationFromIP($ip) {
    // Skip location detection for local IPs
    if ($ip === '127.0.0.1' || $ip === '::1' || 
        filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return null;
    }
    
    // Use your IPinfo token (consider moving to config file)
    $token = 'e452e5be722902';
    $url = "https://ipinfo.io/{$ip}/json?token={$token}";
    
    // Set up context for HTTP request
    $context = stream_context_create([
        'http' => [
            'timeout' => 5, // 5 second timeout
            'user_agent' => 'BranchTracker/1.0'
        ]
    ]);
    
    try {
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['region'])) {
            return null;
        }
        
        return $data;
        
    } catch (Exception $e) {
        error_log("IP location lookup failed: " . $e->getMessage());
        return null;
    }
}

/**
 * Find matching branch based on region
 */
function findMatchingBranch($branches, $userRegion) {
    if (empty($userRegion) || empty($branches)) {
        return null;
    }
    
    $userRegionLower = strtolower(trim($userRegion));
    
    foreach ($branches as $branch) {
        if (isset($branch['region'])) {
            $branchRegionLower = strtolower(trim($branch['region']));
            if ($branchRegionLower === $userRegionLower) {
                return $branch;
            }
        }
    }
    
    return null;
}

/**
 * Get fallback branch (Colombo first, then first available)
 */
function getFallbackBranch($branches) {
    if (empty($branches)) {
        return null;
    }
    
    // Try to find Colombo branch first
    foreach ($branches as $branch) {
        if (isset($branch['id']) && strtolower(trim($branch['id'])) === 'col') {
            return $branch;
        }
    }
    
    // If Colombo not found, try by name
    foreach ($branches as $branch) {
        if (isset($branch['name']) && stripos($branch['name'], 'colombo') !== false) {
            return $branch;
        }
    }
    
    // Return first branch as last resort
    return $branches[0];
}

/**
 * Validate branch data structure
 */
function validateBranchData($branches) {
    if (!is_array($branches)) {
        return false;
    }
    
    foreach ($branches as $branch) {
        if (!is_array($branch) || empty($branch['name'])) {
            return false;
        }
    }
    
    return true;
}

try {
    // Fetch branches from database
    $sql = "SELECT values_json FROM properties WHERE `key` = 'branch' LIMIT 1";
    $result = $conn->query($sql);

    $branches = [];
    if ($result && $row = $result->fetch_assoc()) {
        $branches = json_decode($row['values_json'], true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON in database: ' . json_last_error_msg());
        }
        
        if (!validateBranchData($branches)) {
            throw new Exception('Invalid branch data structure');
        }
    }

    // Get user's IP and location
    $ip = getRealIpAddr();
    $location = getLocationFromIP($ip);
    $userRegion = '';
    
    if ($location && isset($location['region'])) {
        $userRegion = $location['region'];
    }

    // Find matching branch
    $matchedBranch = null;
    
    if (!empty($branches)) {
        // First try region matching
        if (!empty($userRegion)) {
            $matchedBranch = findMatchingBranch($branches, $userRegion);
        }
        
        // If no match, use fallback
        if (!$matchedBranch) {
            $matchedBranch = getFallbackBranch($branches);
        }
    }

    // Prepare response
    $response = [
        'success' => true,
        'matchedBranch' => $matchedBranch,
        'allBranches' => $branches,
        'userLocation' => [
            'ip' => $ip,
            'region' => $userRegion,
            'country' => $location['country'] ?? null,
            'city' => $location['city'] ?? null
        ],
        'timestamp' => date('c'),
        'debug' => [
            'branchCount' => count($branches),
            'hasRegionMatch' => !empty($userRegion) && $matchedBranch && 
                              isset($matchedBranch['region']) && 
                              strtolower($matchedBranch['region']) === strtolower($userRegion),
            'matchMethod' => $matchedBranch ? 
                ((!empty($userRegion) && isset($matchedBranch['region']) && 
                  strtolower($matchedBranch['region']) === strtolower($userRegion)) ? 'region' : 'fallback') 
                : 'none'
        ]
    ];

} catch (Exception $e) {
    // Log error for debugging
    error_log("Branch tracking error: " . $e->getMessage());
    
    $response = [
        'success' => false,
        'error' => 'Failed to fetch branch data',
        'matchedBranch' => null,
        'allBranches' => [],
        'userLocation' => [
            'ip' => getRealIpAddr(),
            'region' => null
        ],
        'timestamp' => date('c')
    ];
}

// Output JSON response
echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit;