# dotCMS Client PHP SDK Architecture Document

## Overview
The `dotcms-php-sdk` is a PHP library designed to simplify interaction with the dotCMS Page API. It provides an intuitive interface to request page data and retrieve it as a `PageAsset` object, supporting both static pages and content-driven pages (e.g., like blog posts or product pages). The SDK emphasizes simplicity, predictability, and ease of integration with popular PHP frameworks such as Laravel, Symfony, and Yii.

### Key Features
- **Intuitive API**: Clear method names and sensible defaults reduce the learning curve.
- **Comprehensive Documentation**: Inline PHPDoc comments and usage examples.
- **Robust Error Handling**: Custom exceptions with detailed messages.
- **Performance**: Async support with Guzzle promises for non-blocking requests.
- **PSR-4 Compliance**: Autoloading compatible with Composer.
- **Framework Compatibility**: Works seamlessly with Laravel, Symfony, etc.
- **Dependencies**: Uses Guzzle for HTTP requests, Monolog for logging, Respect\Validation for input validation, and Symfony Serializer for object mapping.

### Directory Structure
```
dotcms-php-sdk/
├── config/
│   └── dotcms.php              # Configuration defaults
├── src/
│   ├── Client/
│   │   ├── DotCMSClient.php    # Main API client
│   │   └── HttpClient.php      # HTTP client abstraction
│   ├── Exceptions/
│   │   ├── InvalidRequestException.php
│   │   ├── ApiException.php
│   │   └── ValidationException.php
│   ├── Models/
│   │   ├── PageAsset.php       # Main response object
│   │   ├── Layout.php          # Layout structure
│   │   ├── Template.php        # Template details
│   │   ├── Row.php             # Row in layout
│   │   ├── Column.php          # Column in layout
│   │   ├── Container.php       # Container details
│   │   ├── Contentlet.php      # Content item
│   │   ├── Site.php            # Site information
│   │   ├── VanityUrl.php       # Vanity URL details
│   │   └── ViewAs.php          # ViewAs context
│   ├── Requests/
│   │   └── PageRequest.php     # Request builder for Page API
│   ├── Services/
│   │   └── PageService.php     # Business logic layer
│   └── Config.php              # Configuration loader
├── examples/
│   └── basic_usage.php         # Example usage
├── composer.json               # Composer configuration
├── README.md                   # Project documentation
└── .env.example                # Environment variable example
```

---

## Dependencies
The SDK leverages established libraries to ensure reliability and maintainability:
- **`guzzlehttp/guzzle`**: HTTP client for making API requests.
- **`monolog/monolog`**: Logging for debugging and tracing.
- **`respect/validation`**: Input validation for parameters.
- **`symfony/serializer`**: Advanced object serialization/deserialization.

### Composer.json
```json
{
    "name": "dotcms/dotcms-php-sdk",
    "description": "A PHP SDK for interacting with the dotCMS Page API",
    "type": "library",
    "require": {
        "php": "^8.1",
        "guzzlehttp/guzzle": "^7.0",
        "monolog/monolog": "^3.0",
        "respect/validation": "^2.2",
        "symfony/serializer": "^6.0"
    },
    "autoload": {
        "psr-4": {
            "DotCMS\\": "src/"
        }
    },
    "license": "MIT",
    "authors": [
        {
            "name": "Freddy Montes",
            "email": "fmontes@dotcms.com"
        }
    ],
    "minimum-stability": "stable"
}
```

---

## Configuration
Configuration is environment-based, with defaults provided in `config/dotcms.php`. Users can override settings via environment variables or a custom configuration array.

### config/dotcms.php
```php
<?php

namespace DotCMS;

class Config
{
    private readonly string $baseUrl;
    private readonly string $apiKey;
    private readonly Logger $logger;

    public function __construct(array $config = [])
    {
        $this->baseUrl = $config['base_url'] ?? getenv('DOTCMS_BASE_URL') ?: 'https://demo.dotcms.com';
        $this->apiKey = $config['api_key'] ?? getenv('DOTCMS_API_KEY') ?: '';
        $this->logger = $config['logger'] ?? new Logger('dotcms-php-sdk');
        $this->logger->pushHandler(new StreamHandler(
            getenv('DOTCMS_LOG_PATH') ?: 'php://stderr',
            (int) (getenv('DOTCMS_LOG_LEVEL') ?: Logger::INFO)
        ));
    }

    public function getBaseUrl(): string { return $this->baseUrl; }
    public function getApiKey(): string { return $this->apiKey; }
    public function getLogger(): Logger { return $this->logger; }
}
```

### .env.example
```
DOTCMS_BASE_URL=https://demo.dotcms.com
DOTCMS_API_KEY=your-api-key
DOTCMS_LOG_PATH=/var/log/dotcms.log
DOTCMS_LOG_LEVEL=200  # INFO level
```

---

## Core Components

### Client/DotCMSClient.php
The main entry point for interacting with the SDK.

```php
<?php

namespace DotCMS\Client;

use DotCMS\Config;
use DotCMS\Services\PageService;
use DotCMS\Requests\PageRequest;
use DotCMS\Models\PageAsset;

class DotCMSClient
{
    private readonly Config $config;
    private readonly PageService $pageService;

    public function __construct(?Config $config = null)
    {
        $this->config = $config ?? new Config();
        $this->pageService = new PageService(new HttpClient($this->config));
    }

    /**
     * Retrieve a page asset from the dotCMS Page API.
     *
     * @param string $path The page path (e.g., '/index' or '/blog/post/sample')
     * @param string $format Response format ('json' or 'render')
     * @param array $params Additional query parameters (e.g., mode, language_id)
     * @return PageAsset The page asset object
     * @throws InvalidRequestException If the request parameters are invalid
     * @throws ApiException If the API request fails
     */
    public function getPage(string $path, string $format = 'render', array $params = []): PageAsset
    {
        $request = new PageRequest($path, $format, $params);
        return $this->pageService->fetchPage($request);
    }

    /**
     * Async version of getPage.
     *
     * @param string $path The page path
     * @param string $format Response format
     * @param array $params Additional query parameters
     * @return PromiseInterface<PageAsset>
     */
    public function getPageAsync(string $path, string $format = 'render', array $params = []): PromiseInterface
    {
        $request = new PageRequest($path, $format, $params);
        return $this->pageService->fetchPageAsync($request);
    }
}
```

### Client/HttpClient.php
Handles HTTP requests using Guzzle.

```php
<?php

namespace DotCMS\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Promise\PromiseInterface;
use DotCMS\Config;
use DotCMS\Exceptions\ApiException;

class HttpClient
{
    private readonly GuzzleClient $client;
    private readonly Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->client = new GuzzleClient([
            'base_uri' => $this->config->getBaseUrl(),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config->getApiKey(),
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Perform a synchronous GET request.
     *
     * @param string $uri API endpoint
     * @param array $query Query parameters
     * @return array Decoded JSON response
     * @throws ApiException On HTTP errors
     */
    public function get(string $uri, array $query = []): array
    {
        try {
            $response = $this->client->get($uri, ['query' => $query]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new ApiException("API request failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Perform an asynchronous GET request.
     *
     * @param string $uri API endpoint
     * @param array $query Query parameters
     * @return PromiseInterface
     */
    public function getAsync(string $uri, array $query = []): PromiseInterface
    {
        return $this->client->getAsync($uri, ['query' => $query])->then(
            fn($response) => json_decode($response->getBody()->getContents(), true),
            fn($e) => throw new ApiException("Async API request failed: " . $e->getMessage(), $e->getCode(), $e)
        );
    }
}
```

---

### Requests/PageRequest.php
Encapsulates the request parameters and validation.

```php
<?php

namespace DotCMS\Requests;

use Respect\Validation\Validator as v;
use DotCMS\Exceptions\ValidationException;

class PageRequest
{
    private readonly string $path;
    private readonly string $format;
    private readonly array $params;

    /**
     * @param string $path Page path (e.g., '/index')
     * @param string $format 'json' or 'render'
     * @param array $params Optional parameters (mode, language_id, etc.)
     * @throws ValidationException If validation fails
     */
    public function __construct(string $path, string $format, array $params = [])
    {
        $this->validate($path, $format, $params);
        $this->path = $path;
        $this->format = $format;
        $this->params = $params + [
            'mode' => 'LIVE', // Sensible default
            'depth' => 0,     // Default to no related content
        ];
    }

    public function getPath(): string { return $this->path; }
    public function getFormat(): string { return $this->format; }
    public function getParams(): array { return $this->params; }

    private function validate(string $path, string $format, array $params): void
    {
        if (!v::stringType()->notEmpty()->startsWith('/')->validate($path)) {
            throw new ValidationException("Path must be a non-empty string starting with '/'");
        }
        if (!v::in(['json', 'render'])->validate($format)) {
            throw new ValidationException("Format must be 'json' or 'render'");
        }
        if (isset($params['mode']) && !v::in(['LIVE', 'WORKING', 'EDIT_MODE'])->validate($params['mode'])) {
            throw new ValidationException("Mode must be 'LIVE', 'WORKING', or 'EDIT_MODE'");
        }
        if (isset($params['depth']) && !v::intVal()->between(0, 3)->validate($params['depth'])) {
            throw new ValidationException("Depth must be an integer between 0 and 3");
        }
    }

    public function toUrl(): string
    {
        return sprintf('/api/v1/page/%s/%s?%s', $this->format, ltrim($this->path, '/'), http_build_query($this->params));
    }
}
```

---

### Services/PageService.php
Handles the business logic of fetching and mapping API responses.

```php
<?php

namespace DotCMS\Services;

use DotCMS\Client\HttpClient;
use DotCMS\Requests\PageRequest;
use DotCMS\Models\PageAsset;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PageService
{
    private readonly HttpClient $httpClient;
    private readonly Serializer $serializer;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    /**
     * Fetch a page synchronously.
     *
     * @param PageRequest $request The page request
     * @return PageAsset
     */
    public function fetchPage(PageRequest $request): PageAsset
    {
        $response = $this->httpClient->get($request->toUrl());
        return $this->mapResponse($response);
    }

    /**
     * Fetch a page asynchronously.
     *
     * @param PageRequest $request The page request
     * @return PromiseInterface<PageAsset>
     */
    public function fetchPageAsync(PageRequest $request): PromiseInterface
    {
        return $this->httpClient->getAsync($request->toUrl())->then(fn($response) => $this->mapResponse($response));
    }

    private function mapResponse(array $response): PageAsset
    {
        $data = $response['entity'] ?? throw new ApiException('Invalid response format: entity missing');
        return $this->serializer->denormalize($data, PageAsset::class);
    }
}
```

---

### Models/PageAsset.php
The primary response object, supporting both static and generated pages.

```php
<?php

namespace DotCMS\Models;

class PageAsset
{
    public readonly Layout $layout;
    public readonly Template $template;
    public readonly array $page; // Page metadata (SEO, URL, etc.)
    public readonly array $containers; // Keyed by identifier
    public readonly array $contentlets; // Keyed by UUID
    public readonly Site $site;
    public readonly ?array $urlContentMap; // Optional for generated pages
    public readonly ViewAs $viewAs;

    /**
     * @param Layout $layout Page layout structure
     * @param Template $template Template details
     * @param array $page Page metadata
     * @param array $containers Associative array of containers
     * @param array $contentlets Associative array of contentlets
     * @param Site $site Site information
     * @param array|null $urlContentMap Content map for generated pages
     * @param ViewAs $viewAs Visitor context
     */
    public function __construct(
        Layout $layout,
        Template $template,
        array $page,
        array $containers,
        array $contentlets,
        Site $site,
        ?array $urlContentMap,
        ViewAs $viewAs
    ) {
        $this->layout = $layout;
        $this->template = $template;
        $this->page = $page;
        $this->containers = $containers;
        $this->contentlets = $contentlets;
        $this->site = $site;
        $this->urlContentMap = $urlContentMap;
        $this->viewAs = $viewAs;
    }

    /**
     * Check if this is a generated page (e.g., blog post, product page).
     *
     * @return bool
     */
    public function isGenerated(): bool
    {
        return $this->urlContentMap !== null;
    }
}
```

### Models/Layout.php
```php
<?php

namespace DotCMS\Models;

class Layout
{
    public readonly array $rows; // Array of Row objects
    public readonly array $sidebar; // Sidebar configuration

    public function __construct(array $rows, array $sidebar)
    {
        $this->rows = array_map(fn($row) => new Row($row['columns'], $row['styleClass'] ?? ''), $rows);
        $this->sidebar = $sidebar;
    }
}
```

### Models/Row.php
```php
<?php

namespace DotCMS\Models;

class Row
{
    public readonly array $columns; // Array of Column objects
    public readonly string $styleClass;

    public function __construct(array $columns, string $styleClass)
    {
        $this->columns = array_map(fn($col) => new Column($col['containers'], $col['widthPercent'], $col['leftOffset'], $col['styleClass'] ?? ''), $columns);
        $this->styleClass = $styleClass;
    }
}
```

### Models/Column.php
```php
<?php

namespace DotCMS\Models;

class Column
{
    public readonly array $containers; // Array of Container objects
    public readonly int $widthPercent;
    public readonly int $leftOffset;
    public readonly string $styleClass;

    public function __construct(array $containers, int $widthPercent, int $leftOffset, string $styleClass)
    {
        $this->containers = array_map(fn($c) => new Container($c['identifier'], $c['uuid']), $containers);
        $this->widthPercent = $widthPercent;
        $this->leftOffset = $leftOffset;
        $this->styleClass = $styleClass;
    }
}
```

### Models/Container.php
```php
<?php

namespace DotCMS\Models;

class Container
{
    public readonly string $identifier;
    public readonly string $uuid;
    public readonly array $containerStructures; // Container structure details
    public readonly array $rendered; // Rendered content

    public function __construct(string $identifier, string $uuid, array $containerStructures = [], array $rendered = [])
    {
        $this->identifier = $identifier;
        $this->uuid = $uuid;
        $this->containerStructures = $containerStructures;
        $this->rendered = $rendered;
    }
}
```

### Models/Contentlet.php
```php
<?php

namespace DotCMS\Models;

class Contentlet
{
    public readonly string $identifier;
    public readonly string $inode;
    public readonly string $title;
    public readonly string $contentType;
    public readonly array $data; // Full contentlet data

    public function __construct(string $identifier, string $inode, string $title, string $contentType, array $data)
    {
        $this->identifier = $identifier;
        $this->inode = $inode;
        $this->title = $title;
        $this->contentType = $contentType;
        $this->data = $data;
    }
}
```

### Models/Template.php
```php
<?php

namespace DotCMS\Models;

class Template
{
    public readonly string $identifier;
    public readonly string $title;
    public readonly bool $drawed;

    public function __construct(string $identifier, string $title, bool $drawed)
    {
        $this->identifier = $identifier;
        $this->title = $title;
        $this->drawed = $drawed;
    }
}
```

### Models/Site.php
```php
<?php

namespace DotCMS\Models;

class Site
{
    public readonly string $identifier;
    public readonly string $hostname;

    public function __construct(string $identifier, string $hostname)
    {
        $this->identifier = $identifier;
        $this->hostname = $hostname;
    }
}
```

### Models/VanityUrl.php
```php
<?php

namespace DotCMS\Models;

class VanityUrl
{
    public readonly string $url;
    public readonly string $forwardTo;

    public function __construct(string $url, string $forwardTo)
    {
        $this->url = $url;
        $this->forwardTo = $forwardTo;
    }
}
```

### Models/ViewAs.php
```php
<?php

namespace DotCMS\Models;

class ViewAs
{
    public readonly array $visitor; // Visitor context
    public readonly array $language; // Language details
    public readonly string $mode;

    public function __construct(array $visitor, array $language, string $mode)
    {
        $this->visitor = $visitor;
        $this->language = $language;
        $this->mode = $mode;
    }
}
```

---

### Exceptions
Custom exceptions for error handling.

#### Exceptions/InvalidRequestException.php
```php
<?php

namespace DotCMS\Exceptions;

class InvalidRequestException extends \Exception {}
```

#### Exceptions/ApiException.php
```php
<?php

namespace DotCMS\Exceptions;

class ApiException extends \Exception {}
```

#### Exceptions/ValidationException.php
```php
<?php

namespace DotCMS\Exceptions;

class ValidationException extends \Exception {}
```

---

## Usage Examples

### examples/basic_usage.php
```php
<?php

require 'vendor/autoload.php';

use DotCMS\Client\DotCMSClient;

$client = new DotCMSClient();

// Fetch a static page synchronously
$page = $client->getPage('/index', 'render', [
    'language_id' => 1,
    'mode' => 'EDIT_MODE',
]);
echo $page->page['title'] . "\n"; // Outputs: "Home"
echo $page->isGenerated() ? "Generated" : "Static"; // Outputs: "Static"

// Fetch a generated page (e.g., blog post)
$blogPost = $client->getPage('/blog/post/french-polynesia-everything-you-need-to-know', 'render', [
    'language_id' => 1,
]);
echo $blogPost->urlContentMap['title'] . "\n"; // Outputs blog post title
echo $blogPost->isGenerated() ? "Generated" : "Static"; // Outputs: "Generated"

// Async request
$promise = $client->getPageAsync('/index');
$promise->then(function ($page) {
    echo $page->page['title'] . "\n"; // Outputs: "Home"
})->wait();
```

---

## Design Principles
- **Predictable Behavior**: Consistent naming (`getPage`, `getPageAsync`) and parameter usage.
- **Sensible Defaults**: Default `mode` is 'LIVE' and `depth` is 0 for simplicity.
- **Easy Integration**: Minimal dependencies and PSR-4 compliance.
- **Consistent Responses**: Uniform error handling with custom exceptions and a single `PageAsset` response type.
- **Documentation Synergy**: Inline PHPDoc mirrors API logic, with examples in `examples/`.

This architecture provides a solid foundation for implementation, ensuring scalability, maintainability, and ease of use for developers interacting with the dotCMS Page API.