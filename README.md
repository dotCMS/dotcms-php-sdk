# dotCMS PHP SDK

A PHP library designed to simplify interaction with the dotCMS Page API. This SDK provides a clean, object-oriented interface for retrieving and working with dotCMS pages and their components.

## Requirements

- PHP 8.2 or higher
- Composer

## Installation

Install the SDK using Composer:

```bash
composer require dotcms/php-sdk
```

## Configuration

The SDK requires configuration to connect to your dotCMS instance:

```php
use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\Config\LogLevel;

// Create a configuration for the client
$config = new Config(
    host: 'https://your-dotcms-instance.com',
    apiKey: 'YOUR_API_KEY',
    clientOptions: [
        'timeout' => 30
    ],
    logConfig: [
        'level' => LogLevel::INFO,
        'console' => true, // Output logs to console
    ]
);
```

### Configuration Options

#### Required Parameters

- `host`: Your dotCMS instance URL
- `apiKey`: Your dotCMS API key

#### Optional Parameters

- `clientOptions`: Guzzle HTTP client options
  - `headers`: Custom HTTP headers
  - `verify`: SSL verification (boolean)
  - `timeout`: Request timeout in seconds
  - `connect_timeout`: Connection timeout in seconds
  - `http_errors`: Whether to throw exceptions for HTTP errors
  - `allow_redirects`: Whether to follow redirects

- `logConfig`: Logging configuration
  - `level`: Log level (DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL, ALERT, EMERGENCY)
  - `console`: Whether to output logs to console
  - `handlers`: Array of custom Monolog handlers

## Basic Usage

### Creating a Client

```php
use Dotcms\PhpSdk\DotCMSClient;

// Create the dotCMS client
$client = new DotCMSClient($config);
```

### Fetching a Page

```php
try {
    // Create a page request for a specific page
    $pageRequest = $client->createPageRequest('/', 'json');
    
    // Get the page
    $page = $client->getPage($pageRequest);
    
    // Access page information
    echo "Page title: " . $page->page->title . "\n";
    echo "Page URL: " . $page->page->pageUrl . "\n";
    echo "Template name: " . $page->template->title . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

### Asynchronous Requests

```php
// Create a page request
$asyncPageRequest = $client->createPageRequest('/', 'json');

// Get the page asynchronously
$promise = $client->getPageAsync($asyncPageRequest);

// Add callbacks for success and failure
$promise->then(
    function ($asyncPage) {
        echo "Async page title: " . $asyncPage->page->title . "\n";
    },
    function (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
);

// Wait for the promise to complete
$promise->wait();
```

## Advanced Usage

### Customizing Page Requests

The `PageRequest` class provides several methods to customize your page requests:

```php
$pageRequest = $client->createPageRequest('/about-us', 'json');

// Set the language ID for the request
$pageRequest = $pageRequest->withLanguageId(1);

// Set the mode (LIVE, WORKING, EDIT_MODE)
$pageRequest = $pageRequest->withMode('WORKING');

// Set the depth of the content to retrieve (0-3)
$pageRequest = $pageRequest->withDepth(2);

// Set personalization options
$pageRequest = $pageRequest->withPersonaId('persona_id');

// Set whether to fire rules
$pageRequest = $pageRequest->withFireRules(true);

// Set the host ID (Site ID)
$pageRequest = $pageRequest->withHostId('48190c8c-42c4-46af-8d1a-0cd5db894797');
```

Note that each method returns a new instance with the updated value, so you need to reassign the result.

### Working with Page Components

Once you have a page, you can access its components:

```php
// Access site information
echo "Site hostname: " . $page->site->hostname . "\n";

// Access template information
echo "Template title: " . $page->template->title . "\n";

// Access layout information
echo "Layout header: " . $page->layout->header . "\n";

// Access containers and contentlets
foreach ($page->containers as $containerId => $container) {
    echo "Container ID: " . $containerId . "\n";
    
    if (!empty($container->contentlets)) {
        foreach ($container->contentlets as $uuid => $contentletArray) {
            foreach ($contentletArray as $contentlet) {
                echo "Contentlet type: " . $contentlet->contentType . "\n";
                echo "Contentlet title: " . ($contentlet->title ?? 'N/A') . "\n";
                
                // Access additional fields using array access
                foreach ($contentlet as $fieldName => $fieldValue) {
                    if (is_scalar($fieldValue)) {
                        echo "$fieldName: $fieldValue\n";
                    }
                }
            }
        }
    }
}
```

## API Reference

### DotCMSClient

The main client for interacting with the dotCMS API.

| Method | Description | Parameters |
|--------|-------------|------------|
| `__construct` | Create a new client instance | `Config $config` |
| `getPage` | Fetch a page synchronously | `PageRequest $request` |
| `getPageAsync` | Fetch a page asynchronously | `PageRequest $request` |
| `createPageRequest` | Create a new page request | `string $pagePath, string $format = 'json'` |

### PageRequest

Represents a request to the dotCMS Page API.

| Method | Description | Parameters |
|--------|-------------|------------|
| `__construct` | Create a new page request | `string $pagePath, string $format = 'json'` |
| `withLanguageId` | Set the language ID for the request | `int $languageId` |
| `withMode` | Set the mode (LIVE, WORKING, EDIT_MODE) | `string $mode` |
| `withDepth` | Set the depth of the content to retrieve (0-3) | `int $depth` |
| `withPersonaId` | Set the persona ID for personalization | `string $personaId` |
| `withHostId` | Set the host ID (Site ID) | `string $hostId` |
| `withFireRules` | Set whether to fire rules | `bool $fireRules` |
| `buildPath` | **(Internal)** Build the API path for the request | None |
| `buildQueryParams` | **(Internal)** Build the query parameters for the request | None |

### PageAsset

Represents a complete page asset from dotCMS.

| Property | Type | Description |
|----------|------|-------------|
| `page` | Page | The Page object |
| `site` | Site | The Site object |
| `template` | Template | The Template object |
| `layout` | Layout | The Layout object |
| `containers` | Array | Array of Container objects |

## Error Handling

The SDK provides several exception classes for error handling:

| Exception | Description |
|-----------|-------------|
| `DotCMSException` | Base exception class for all SDK exceptions |
| `ConfigException` | Thrown when there's an issue with the configuration |
| `HttpException` | Thrown when there's an HTTP error |
| `ResponseException` | Thrown when there's an issue with the response |

Example error handling:

```php
try {
    $page = $client->getPage($pageRequest);
} catch (ConfigException $e) {
    echo "Configuration error: " . $e->getMessage() . "\n";
} catch (HttpException $e) {
    echo "HTTP error: " . $e->getMessage() . "\n";
    echo "Status code: " . $e->getStatusCode() . "\n";
} catch (ResponseException $e) {
    echo "Response error: " . $e->getMessage() . "\n";
} catch (DotCMSException $e) {
    echo "dotCMS error: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "General error: " . $e->getMessage() . "\n";
}
```

## Examples

### Basic Example

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\DotCMSClient;

// Create configuration
$config = new Config(
    host: 'https://demo.dotcms.com',
    apiKey: 'YOUR_API_KEY'
);

// Create client
$client = new DotCMSClient($config);

// Create page request
$pageRequest = $client->createPageRequest('/', 'json');

// Get page
$page = $client->getPage($pageRequest);

// Display page information
echo "Page title: " . $page->page->title . "\n";
echo "Page URL: " . $page->page->pageUrl . "\n";
```

### Integration with Symfony

The SDK can be easily integrated with Symfony. See the `examples/dotcms-symfony` directory for a complete example.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`

### Coding Standards

The project uses PHP-CS-Fixer for code style. Run the following commands:

- Check code style: `composer cs-check`
- Fix code style: `composer cs-fix`

### Static Analysis

The project uses PHPStan for static analysis:

```bash
composer phpstan
```

### Running All Checks

```bash
composer check
```

## License

This project is licensed under the MIT License - see the LICENSE file for details. 