# dotCMS PHP SDK (`alpha`)

A PHP library designed to simplify interaction with the dotCMS Page API. This SDK provides a clean, object-oriented interface for retrieving and working with dotCMS pages and their components.

[![PHP Code Quality Checks](https://github.com/dotCMS/dotcms-php-sdk/actions/workflows/php-quality-checks.yml/badge.svg)](https://github.com/dotCMS/dotcms-php-sdk/actions/workflows/php-quality-checks.yml)

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

// Create a configuration for the client
$config = new Config(
    host: 'https://your-dotcms-instance.com',
    apiKey: 'YOUR_API_KEY',
    clientOptions: [
        'timeout' => 30
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
    $pageAsset = $client->getPage($pageRequest);
    
    // Access page information
    echo "Page title: " . $pageAsset->page->title . "\n";
    echo "Page URL: " . $pageAsset->page->pageUrl . "\n";
    echo "Template name: " . $pageAsset->template->title . "\n";
    
    // Check if page has vanity URL
    if ($pageAsset->vanityUrl !== null) {
        echo "Vanity URL: " . $pageAsset->vanityUrl->url . "\n";
        echo "Forward to: " . $pageAsset->vanityUrl->forwardTo . "\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

### Fetching Navigation

```php
try {
    // Create a navigation request
    $navRequest = $client->createNavigationRequest('/about-us', 2);
    
    // Get the navigation
    $nav = $client->getNavigation($navRequest);
    
    // Access navigation information
    echo "Navigation title: " . $nav->title . "\n";
    echo "Navigation URL: " . $nav->href . "\n";
    
    // Access children if available
    if ($nav->hasChildren()) {
        foreach ($nav->getChildren() as $child) {
            echo "- " . $child->title . " (" . $child->href . ")\n";
        }
    }
    
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
        if ($asyncPage->vanityUrl !== null) {
            echo "Vanity URL: " . $asyncPage->vanityUrl->url . "\n";
        }
    },
    function (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
);

// Wait for the promise to complete
$promise->wait();
```

### Asynchronous Navigation Requests

```php
// Create a navigation request
$asyncNavRequest = $client->createNavigationRequest('/', 2);

// Get the navigation asynchronously
$promise = $client->getNavigationAsync($asyncNavRequest);

// Add callbacks for success and failure
$promise->then(
    function ($nav) {
        echo "Navigation title: " . $nav['title'] . "\n";
        if ($nav->hasChildren()) {
            foreach ($nav->getChildren() as $child) {
                echo "- " . $child['title'] . "\n";
            }
        }
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

### Customizing Navigation Requests

The `NavigationRequest` class allows you to customize your navigation requests:

```php
// Create a navigation request with custom parameters
$navRequest = $client->createNavigationRequest(
    path: '/about-us',  // The root path to begin traversing
    depth: 2,           // The depth of the folder tree to return (1-3)
    languageId: 2       // The language ID for content (e.g., 2 for Spanish)
);

// Get the navigation with the custom parameters
$nav = $client->getNavigation($navRequest);
```

### Working with Page Components

Once you have a page, you can access its components:

```php
// Access site information
echo "Site hostname: " . $page->site->hostname . "\n";

// Access template information
echo "Template title: " . $page->template->title . "\n";

// Access layout information
echo "Layout header: " . $page->layout->header . "\n";

// Access vanity URL if present
if ($page->vanityUrl !== null) {
    echo "Vanity URL pattern: " . $page->vanityUrl->pattern . "\n";
    echo "Forward to: " . $page->vanityUrl->forwardTo . "\n";
    echo "Response code: " . $page->vanityUrl->response . "\n";
    echo "Is temporary redirect: " . ($page->vanityUrl->temporaryRedirect ? 'Yes' : 'No') . "\n";
}

// Access containers and contentlets
foreach ($page->containers as $containerId => $container) {
    echo "Container ID: " . $containerId . "\n";
    echo "Max Contentlets: " . $container->maxContentlets . "\n";
    
    if (!empty($container->contentlets)) {
        foreach ($container->contentlets as $uuid => $contentlets) {
            foreach ($contentlets as $contentlet) {
                echo "Contentlet type: " . $contentlet->contentType . "\n";
                echo "Contentlet title: " . ($contentlet->title ?? 'N/A') . "\n";
                
                // Access additional fields using object properties
                foreach ($contentlet->getAdditionalProperties() as $fieldName => $fieldValue) {
                    if (is_scalar($fieldValue)) {
                        echo "$fieldName: $fieldValue\n";
                    }
                }
            }
        }
    }
}
```

### Working with Navigation Items

The `NavigationItem` class provides array access to its properties:

```php
// Check if the navigation item is a folder
if ($nav->isFolder()) {
    echo "This is a folder\n";
}

// Check if the navigation item is a page
if ($nav->isPage()) {
    echo "This is a page\n";
}

// Access navigation properties
echo "Title: " . $nav->title . "\n";
echo "URL: " . $nav->href . "\n";
echo "Type: " . $nav->type . "\n";
echo "Target: " . $nav->target . "\n"; // e.g., "_self", "_blank"
echo "Order: " . $nav->order . "\n";

// Recursively process navigation tree
function processNavigation($navItem, $level = 0) {
    $indent = str_repeat("  ", $level);
    echo $indent . "- " . $navItem->title . " (" . $navItem->href . ")\n";
    
    if ($navItem->hasChildren()) {
        foreach ($navItem->getChildren() as $child) {
            processNavigation($child, $level + 1);
        }
    }
}

processNavigation($nav);
```

### Using SDK Utilities

The SDK includes a `DotCmsHelper` class with common functions for rendering and working with DotCMS content:

```php
use Dotcms\PhpSdk\Utils\DotCmsHelper;
use Dotcms\PhpSdk\Model\Content\Contentlet;

// Generate HTML attributes from an associative array
$attrs = [
    'class' => 'my-class',
    'data-id' => '123',
    'disabled' => true
];
$htmlAttrs = DotCmsHelper::htmlAttributes($attrs);

// Generate simple HTML for a contentlet
$contentlet = new Contentlet(
    identifier: 'abc123',
    inode: 'inode123',
    title: 'My Content',
    contentType: 'Banner'
);
$html = DotCmsHelper::simpleContentHtml($contentlet->jsonSerialize());

// Extract accept types from container structures
$acceptTypes = DotCmsHelper::extractAcceptTypes($containerStructures);

// Extract contentlets from container page
$contentlets = DotCmsHelper::extractContentlets($containerPage, $uuid);
```

These utilities help with common tasks like:
- Generating HTML attributes safely
- Rendering contentlets with basic HTML
- Working with container structures and contentlets
- Extracting data from container pages

## Data Access Patterns

The SDK provides two ways to access data: object notation and array access. Here's when to use each:

### Object Notation (->)

Use object notation for accessing standard properties of these classes:

```php
// Page and Site properties
$page->title
$page->pageUrl
$site->hostname

// Container properties
$container->identifier
$container->title
$container->maxContentlets

// Contentlet properties
$contentlet->identifier
$contentlet->title
$contentlet->contentType

// Navigation properties
$nav->title
$nav->href
$nav->type
```

### Array Access ([])

Use array access for:
1. Additional properties not explicitly defined in the class
2. Accessing container contentlets by UUID
3. Accessing rendered content by UUID

```php
// Additional properties
$contentlet['customField']
$page['metadata']

// Container contentlets
$container->contentlets['uuid-123']

// Rendered content
$container->rendered['uuid-123']
```

### Classes That Support Both

These classes support both object and array access:
- `Page`
- `Site`
- `Contentlet`
- `Container`
- Any class extending `AbstractModel`

### Classes That Only Support Object Access

These classes only support object notation:
- `PageAsset`
- `ContainerPage`
- `NavigationItem`
- `Layout`
- `Template`
- `VanityUrl`

## API Reference

### DotCMSClient

The main client for interacting with the dotCMS API.

| Method | Description | Parameters |
|--------|-------------|------------|
| `__construct` | Create a new client instance | `Config $config` |
| `getPage` | Fetch a page synchronously | `PageRequest $request` |
| `getPageAsync` | Fetch a page asynchronously | `PageRequest $request` |
| `createPageRequest` | Create a new page request | `string $pagePath, string $format = 'json'` |
| `getNavigation` | Fetch navigation items synchronously | `NavigationRequest $request` |
| `getNavigationAsync` | Fetch navigation items asynchronously | `NavigationRequest $request` |
| `createNavigationRequest` | Create a new navigation request | `string $path = '/', int $depth = 1, int $languageId = 1` |

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

### NavigationRequest

Represents a request to the dotCMS Navigation API.

| Method | Description | Parameters |
|--------|-------------|------------|
| `__construct` | Create a new navigation request | `string $path = '/', int $depth = 1, int $languageId = 1` |
| `getPath` | Get the path | None |
| `getDepth` | Get the depth | None |
| `getLanguageId` | Get the language ID | None |
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
| `containers` | array<ContainerPage> | Array of ContainerPage objects |
| `urlContentMap` | Contentlet\|null | Content map for generated pages |
| `viewAs` | ViewAs | Visitor context information |
| `vanityUrl` | VanityUrl\|null | Optional vanity URL configuration |

### ContainerPage

Represents a container page from dotCMS.

| Property | Type | Description |
|----------|------|-------------|
| `container` | Container | The Container object |
| `containerStructures` | array<ContainerStructure> | Array of ContainerStructure objects |
| `rendered` | array<string, string> | Rendered content keyed by UUID |
| `contentlets` | array<string, array<Contentlet>> | Contentlets keyed by UUID |

### Contentlet

Represents a content item from dotCMS.

| Property | Type | Description |
|----------|------|-------------|
| `identifier` | string | The content identifier |
| `inode` | string | The content inode |
| `title` | string | The content title |
| `contentType` | string | The content type |
| `additionalProperties` | array<string, mixed> | Additional content properties |

| Method | Description | Return Type |
|--------|-------------|-------------|
| `getAdditionalProperties` | Get additional properties | `array<string, mixed>` |
| `jsonSerialize` | Convert to array for JSON serialization | `array<string, mixed>` |

### DotCmsHelper

Utility class for common DotCMS operations.

| Method | Description | Parameters | Return Type |
|--------|-------------|------------|-------------|
| `htmlAttributes` | Generate HTML attributes | `array<string, mixed>` | `string` |
| `simpleContentHtml` | Generate simple HTML for content | `array<string, mixed>` | `string` |
| `extractAcceptTypes` | Extract accept types from structures | `array<ContainerStructure>` | `string` |
| `extractContentlets` | Extract contentlets from container | `ContainerPage, string` | `array<Contentlet>` |

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
    $pageAsset = $client->getPage($pageRequest);
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

### Basic Page Example

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\DotCMSClient;
use Dotcms\PhpSdk\Model\Page\PageAsset;
use Dotcms\PhpSdk\Model\Page\VanityUrl;

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
$pageAsset = $client->getPage($pageRequest);

// Display page information
echo "Page title: " . $pageAsset->page->title . "\n";
echo "Page URL: " . $pageAsset->page->pageUrl . "\n";

// Check for vanity URL
if ($pageAsset->vanityUrl !== null) {
    echo "Vanity URL: " . $pageAsset->vanityUrl->url . "\n";
    echo "Forward to: " . $pageAsset->vanityUrl->forwardTo . "\n";
}
```

### Basic Navigation Example

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\DotCMSClient;
use Dotcms\PhpSdk\Model\Navigation\NavigationItem;

// Create configuration
$config = new Config(
    host: 'https://demo.dotcms.com',
    apiKey: 'YOUR_API_KEY'
);

// Create client
$client = new DotCMSClient($config);

// Create navigation request for the About Us section with depth=2
$navRequest = $client->createNavigationRequest('/about-us', 2);

// Get navigation
$nav = $client->getNavigation($navRequest);

// Display navigation information
echo "Navigation title: " . $nav->title . "\n";

// Display children if available
if ($nav->hasChildren()) {
    echo "Children:\n";
    foreach ($nav->getChildren() as $child) {
        echo "- " . $child->title . " (" . $child->href . ")\n";
    }
}
```

### Integration with Symfony

The SDK can be easily integrated with Symfony. See the `examples/dotcms-symfony` directory for a complete example.

## We Need Your Feedback!

The SDK is in active development, and your feedback is crucial to its success. We're particularly interested in:

- Use cases and real-world scenarios
- Feature requests and improvements
- Performance feedback
- Integration challenges
- Documentation suggestions

Feel free to:
- Open issues for bugs or feature requests
- Join our community discussions
- Share your implementation experiences
- Suggest improvements to the API design

## Contributing

We welcome contributions of all kinds! Whether you're fixing bugs, adding features, or improving documentation, your help makes the SDK better for everyone.

### How to Contribute

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run the test suite (`composer check`)
5. Commit your changes (`git commit -m 'feat: add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

### Pull Requests

We actively review and merge pull requests. To ensure a smooth process:

- Follow our coding standards
- Add tests for new features
- Update documentation
- Use conventional commit messages
- Keep PRs focused and manageable

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

## What's Next

The SDK is actively being developed with several exciting features planned:

### Template Rendering System

We're working on a comprehensive template rendering system that will make it easier to integrate DotCMS with popular PHP frameworks:

#### Twig Templates
- Pre-built templates for DotCMS layouts, containers, and content types
- Twig extensions for common DotCMS operations

#### Blade Templates
- Ready-to-use Blade components for DotCMS content
- Blade directives for DotCMS-specific functionality

The template system will include:
- Standard layouts for common page structures
- SEO-friendly markup and metadata handling

### Content API Support
- Full Content API integration for content management
- ElasticSearch Support
- Advanced search capabilities

### Data Validation Layer
- Schema-based validation for content types
- Custom validation rules
- Validation error handling
- Type conversion and normalization
- Cross-field validation
- Validation caching

### Additional Features
- Enhanced caching mechanisms
- Additional framework integrations
- Performance optimizations
- Extended documentation and examples

Stay tuned for updates and feel free to contribute to these upcoming features!

## License

This project is licensed under the MIT License - see the LICENSE file for details. 