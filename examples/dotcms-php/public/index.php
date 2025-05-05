<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotcms\PhpSdk\DotCMSClient;

// Load configuration
$config = require __DIR__ . '/../config/dotcms.php';

// Initialize client
$client = new DotCMSClient($config);

try {
    // Get current path
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    
    // Get page and navigation
    $pageRequest = $client->createPageRequest($path, 'json');
    $pageAsset = $client->getPage($pageRequest);
    
    $navRequest = $client->createNavigationRequest('/', 2);
    $nav = $client->getNavigation($navRequest);
    
    include __DIR__ . '/../templates/layout.php';
} catch (Exception $e) {
    $statusCode = $e->getCode() ?: 500;
    http_response_code($statusCode);
    echo "<h1>Error (Status Code: $statusCode)</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
