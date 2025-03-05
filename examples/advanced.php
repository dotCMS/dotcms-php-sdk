<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\DotCMSClient;
use Dotcms\PhpSdk\Config\LogLevel;
use Dotcms\PhpSdk\Model\Container\ContainerPage;

// Create a configuration for the client
// Replace with your actual dotCMS host and API key
$config = new Config(
    host: 'https://demo.dotcms.com',
    apiKey: 'API_KEY',
    clientOptions: [
        'timeout' => 30,
        'verify' => true,
        'headers' => [
            'Accept-Language' => 'en-US', // Specify language
        ],
    ],
    logConfig: [
        'level' => LogLevel::INFO,
        'console' => true,
    ]
);

// Create the dotCMS client
$client = new DotCMSClient($config);

try {
    // Create a page request for a specific page
    $pageRequest = $client->createPageRequest('/', 'json');
    
    // Get the page
    $page = $client->getPage($pageRequest);
    
    // Display detailed page information
    echo "=== Page Information ===\n";
    echo "Title: " . $page->page->title . "\n";
    echo "URL: " . $page->page->pageUrl . "\n";
    
    // Access additional properties using array access
    echo "Description: " . ($page->page['description'] ?? 'N/A') . "\n";
    echo "Created: " . ($page->page['createDate'] ?? 'N/A') . "\n";
    echo "Modified: " . ($page->page['modDate'] ?? 'N/A') . "\n";
    
    // Display site information
    echo "\n=== Site Information ===\n";
    echo "Hostname: " . $page->site->hostname . "\n";
    
    // Access additional site properties using array access
    echo "Site name: " . ($page->site['name'] ?? 'N/A') . "\n";
    echo "Default language: " . ($page->site['defaultLanguage'] ?? 'N/A') . "\n";
    
    // Display template information
    echo "\n=== Template Information ===\n";
    echo "Template identifier: " . $page->template->identifier . "\n";
    
    // Access additional template properties using array access
    echo "Template title: " . ($page->template['title'] ?? 'N/A') . "\n";
    
    // Display layout information
    echo "\n=== Layout Information ===\n";
    echo "Layout body: " . (is_array($page->layout->body) ? json_encode($page->layout->body) : $page->layout->body) . "\n";
    echo "Layout header: " . $page->layout->header . "\n";
    echo "Layout footer: " . $page->layout->footer . "\n";
    
    // Display container information
    echo "\n=== Containers ===\n";
    foreach ($page->containers as $containerId => $container) {
        echo "Container ID: " . $containerId . "\n";
        
        // Display contentlets in the container
        if (!empty($container->contentlets)) {
            echo "  Contentlets:\n";
            
            // Loop through contentlets by UUID
            foreach ($container->contentlets as $uuid => $contentletArray) {
                echo "  UUID: " . $uuid . "\n";
                
                // Loop through each contentlet in the array
                foreach ($contentletArray as $contentlet) {
                    echo "    - Type: " . $contentlet->contentType . "\n";
                    echo "      Title: " . ($contentlet->title ?? 'N/A') . "\n";
                    echo "      Identifier: " . $contentlet->identifier . "\n";
                    
                    // Display additional fields using array access
                    echo "      Fields:\n";
                    foreach ($contentlet as $fieldName => $fieldValue) {
                        if ($fieldName !== 'contentType' && $fieldName !== 'title' && $fieldName !== 'identifier') {
                            if (is_scalar($fieldValue)) {
                                echo "        $fieldName: $fieldValue\n";
                            } elseif (is_array($fieldValue)) {
                                echo "        $fieldName: " . json_encode($fieldValue) . "\n";
                            }
                        }
                    }
                    
                    echo "\n";
                }
            }
        } else {
            echo "  No contentlets in this container\n";
        }
        
        echo "\n";
    }
    
    // Example of working with a specific container if you know its ID
    echo "=== Working with a specific container ===\n";
    $specificContainerId = array_key_first($page->containers);
    if ($specificContainerId) {
        echo "Container ID: $specificContainerId\n";
        
        $specificContainer = $page->containers[$specificContainerId];
        
        // Fix: Check if contentlets property exists and is not null
        if (isset($specificContainer->contentlets) && $specificContainer->contentlets !== null) {
            // Get the first contentlet UUID
            $firstContentletUuid = array_key_first($specificContainer->contentlets);
            
            if ($firstContentletUuid && !empty($specificContainer->contentlets[$firstContentletUuid])) {
                $firstContentletArray = $specificContainer->contentlets[$firstContentletUuid];
                
                if (!empty($firstContentletArray)) {
                    $firstContentlet = $firstContentletArray[0];
                    echo "First contentlet title: " . ($firstContentlet->title ?? 'N/A') . "\n";
                    
                    // You can access specific fields using array access
                    echo "Available fields: ";
                    $fields = [];
                    foreach ($firstContentlet as $fieldName => $fieldValue) {
                        if ($fieldName !== 'contentType' && $fieldName !== 'title' && $fieldName !== 'identifier') {
                            $fields[] = $fieldName;
                        }
                    }
                    echo implode(', ', $fields) . "\n";
                }
            } else {
                echo "No contentlets found in this container\n";
            }
        } else {
            echo "Container does not have contentlets property or it is null\n";
        }
    } else {
        echo "No containers found\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 