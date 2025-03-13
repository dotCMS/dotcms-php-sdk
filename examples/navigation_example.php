<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\DotCMSClient;

// Create a configuration
$config = new Config(
    'https://demo.dotcms.com', // host
    'your-api-key-here' // apiKey - Replace with your actual API key if needed
);

// Create a client
$client = new DotCMSClient($config);

// Example 1: Get top-level navigation
echo "Example 1: Top-level navigation\n";
echo "--------------------------------\n";
$navRequest = $client->createNavigationRequest('/', 1);
$nav = $client->getNavigation($navRequest);
echo "Title: " . $nav['title'] . "\n";
echo "URL: " . $nav['href'] . "\n";
echo "Type: " . $nav['type'] . "\n\n";

// Example 2: Get navigation with children (depth=2)
echo "Example 2: Navigation with children (depth=2)\n";
echo "------------------------------------------\n";
$navWithChildrenRequest = $client->createNavigationRequest('/about-us', 2);
$navWithChildren = $client->getNavigation($navWithChildrenRequest);
echo "Title: " . $navWithChildren['title'] . "\n";
echo "URL: " . $navWithChildren['href'] . "\n";
echo "Type: " . $navWithChildren['type'] . "\n";

if ($navWithChildren->hasChildren()) {
    echo "Children:\n";
    foreach ($navWithChildren->getChildren() as $child) {
        echo "- " . $child['title'] . " (" . $child['href'] . ")\n";
    }
}
echo "\n";

// Example 3: Get navigation in a different language
echo "Example 3: Navigation in Spanish (languageId=2)\n";
echo "-------------------------------------------\n";
$navSpanishRequest = $client->createNavigationRequest('/', 1, 2);
$navSpanish = $client->getNavigation($navSpanishRequest);
echo "Title: " . $navSpanish['title'] . "\n";
echo "URL: " . $navSpanish['href'] . "\n";
echo "Language ID: " . $navSpanish['languageId'] . "\n\n";

// Example 4: Async navigation request
echo "Example 4: Async navigation request\n";
echo "--------------------------------\n";
$asyncRequest = $client->createNavigationRequest('/about-us', 2);
$client->getNavigationAsync($asyncRequest)->then(
    function ($nav) {
        echo "Title: " . $nav['title'] . "\n";
        if ($nav->hasChildren()) {
            echo "Children:\n";
            foreach ($nav->getChildren() as $child) {
                echo "- " . $child['title'] . "\n";
            }
        }
    }
)->wait(); 