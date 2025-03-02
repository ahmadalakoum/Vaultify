<?php
function getBearerToken()
{
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        return null;
    }

    $authHeader = $headers['Authorization'];
    if (strpos($authHeader, 'Bearer ') !== 0) {
        return null;
    }

    return trim(substr($authHeader, 7)); // Extract token after "Bearer "
}
?>