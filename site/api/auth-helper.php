<?php
/**
 * Auth Helper for FaucetList.org
 * Verifies admin status via auth.directsponsor.org
 */

function isAdmin($userId) {
    if (empty($userId)) return false;

    $url = "https://auth.directsponsor.org/api/sync.php?action=get&user_id=" . urlencode($userId) . "&data_type=profile";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        if ($data && $data['success'] && isset($data['data'])) {
            $profile = $data['data'];
            $roles = $profile['roles'] ?? [];
            return in_array('admin', $roles);
        }
    }
    
    return false;
}

function requireAdmin() {
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $input['user_id'] ?? $_GET['user_id'] ?? null;
    
    if (!isAdmin($userId)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Admin privileges required']);
        exit;
    }
}
