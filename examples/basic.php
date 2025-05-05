<?php

declare(strict_types=1);

require_once __DIR__ . '../../vendor/autoload.php';

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\DotCMSClient;
use Dotcms\PhpSdk\Config\LogLevel;

// Create a configuration for the client
// Replace with your actual dotCMS host and API key
$config = new Config(
    host: 'https://demo.dotcms.com',
    apiKey: 'API_KEY',
    clientOptions: [
        'timeout' => 30,
        'verify' => true, // Set to false if using self-signed certificates
    ],
    logConfig: [
        'level' => LogLevel::DEBUG,
        'console' => true, // Output logs to console
    ]
);

// Create the dotCMS client
$client = new DotCMSClient($config);

try {
    // Example 1: Fetch a page synchronously
    echo "Fetching page synchronously...\n";
    
    // Create a page request for a specific page
    $pageRequest = $client->createPageRequest('/', 'json');
    
    // Get the page
    $page = $client->getPage($pageRequest);

    $test = $page->template->title;
    $test1 = $page->layout['rows'][0];

    $test2 = $page->layout->body->rows[0]->columns[0]->containers[0];

    // Display some information about the page
    echo "Page title: " . $page->page->title . "\n";
    echo "Page URL: " . $page->page->pageUrl . "\n";
    echo "Template name: " . $page->template->title . "\n";
    echo "Number of containers: " . count($page->containers) . "\n\n";
    
    // Example 2: Fetch a page asynchronously
    echo "Fetching page asynchronously...\n";
    
    // Create another page request
    $asyncPageRequest = $client->createPageRequest('/', 'json');
    
    // Get the page asynchronously
    $promise = $client->getPageAsync($asyncPageRequest);
    
    // Add callbacks for success and failure
    $promise->then(
        function ($asyncPage) {
            echo "Async page title: " . $asyncPage->page->title . "\n";
            echo "Async page URL: " . $asyncPage->page->pageUrl . "\n";
            echo "Async template name: " . $asyncPage->template->title . "\n";
        },
        function (\Exception $e) {
            echo "Error fetching page asynchronously: " . $e->getMessage() . "\n";
        }
    );
    
    // Wait for the promise to complete
    $promise->wait();
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 