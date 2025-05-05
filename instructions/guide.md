# Building Dynamic Websites with the dotCMS PHP SDK: A Complete Guide to Headless CMS Integration

## Introduction

This guide will walk you through rendering dotCMS pages using the dotCMS PHP SDK. Whether you're building a new PHP application or integrating dotCMS into an existing one, this guide will help you get started quickly.

## Requirements

* **PHP:** Version 8.0 or higher
* **Composer:** PHP's dependency manager  
* **A running dotCMS instance:** Accessible via HTTPS, with content (folders, pages, content types like Banner, Product, Blog) published
  * Demo instance available: `https://demo.dotcms.com` (user: `admin@dotcms.com`, password: `admin`)
* **Basic command-line/terminal access**
* **A web browser**

This guide uses `https://demo.dotcms.com` and its content.

## Assumptions

This guide assumes you have:

* **Basic PHP knowledge**: Understanding of PHP syntax, variables, arrays, and control structures
* **Familiarity with HTML/CSS**: Ability to write basic HTML markup and CSS styles
* **Understanding of MVC concepts**: Knowledge of separating logic, templates, and data
* **Command line basics**: Experience with terminal/command prompt navigation and commands
* **Web development fundamentals**: Understanding of HTTP requests, URLs, and how web servers work
* **Basic dotCMS understanding**: Awareness that dotCMS is a content management system with pages, content types, and layout structures
* **JSON familiarity**: Basic understanding of JSON data structures

## Step 1: Project Setup

Create a new PHP project and install the required dependencies:

```bash
mkdir dotcms-php
cd dotcms-php
composer init
composer require dotcms/php-sdk:dev-main
```

This creates a new PHP project directory, initializes Composer, and installs the dotCMS PHP SDK. Note: We're using the development version of the SDK as the stable package may not be available in the default Composer repository.

## Step 2: Project Structure

Create the following file structure:

```
dotcms-php/
├── composer.json
├── composer.lock
├── config/
│   └── dotcms.php
├── templates/
│   ├── content-type/
│   │   ├── banner.php
│   │   ├── content-type-not-found.php
│   ├── partials/
│   │   ├── row.php
│   │   ├── column.php
│   │   └── container.php
│   ├── layout.php
│   ├── navigation.php
│   └── page.php
└── public/
    ├── css/
    │   ├── app.css
    │   └── layout.css
    └── index.php
```

Run this command to create the structure:

```bash
mkdir -p {config,templates/content-type,templates/partials,public/css} && \
touch {config/dotcms.php,templates/content-type/{banner,content-type-not-found}.php,templates/partials/{row,column,container}.php,templates/{layout,navigation,page}.php,public/css/{app,layout}.css,public/index.php}
```

This structure separates configuration, templates, and public assets, following PHP best practices.

## Step 3: Create the Entry Point

Add basic placeholder code to `public/index.php`:

```php
<?php
?>
<h1>Welcome to my dotCMS PHP web app</h1>
```

This file will handle all requests and serve as the entry point for connecting with dotCMS.

## Step 4: Run the App

Start the development server:

```bash
cd public
php -S localhost:8000
```

Open `http://localhost:8000` to see "Welcome to my dotCMS PHP web app".

## Step 5: Add Placeholder Content

Let's add placeholder content to all template files so we can visually debug that they are being added to the page:

Update `templates/navigation.php`:
```php
<p>Navigation Template</p>
```

Update `templates/page.php`:
```php
<p>Page Template</p>
```

Update `templates/content-type/banner.php`:
```php
<p>Banner Content Type Template</p>
```

Update `templates/content-type/content-type-not-found.php`:
```php
<?php
if (!isset($contentlet)) {
    echo "<!-- Error: Contentlet data missing -->";
    return;
}

$contentTypeVar = $contentlet->contentType ?? 'unknown';
?>
<div class="content-type-not-found">
    <p>Template Not Found: <b><?= htmlspecialchars($contentTypeVar) ?></b></p>
</div>
```

Update `templates/partials/row.php`:
```php
<p>Row Partial Template</p>
```

Update `templates/partials/column.php`:
```php
<p>Column Partial Template</p>
```

Update `templates/partials/container.php`:
```php
<p>Container Partial Template</p>
```

## Step 6: Create the main template

Update `templates/layout.php`:

```php
<!DOCTYPE html>
<html>
<head>
    <title>dotCMS Page</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/layout.css">
</head>
<body>
    <header>
        <?php include __DIR__ . '/navigation.php'; ?>
    </header>
    
    <main>
        <?php include __DIR__ . '/page.php'; ?>
    </main>
    
    <footer>
        <div class="footer-content container">
            <p>&copy; <?= date('Y') ?> Your Company Name. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
```

Update `public/index.php` to see the template:

```php
<?php
include __DIR__ . '/../templates/layout.php';
?>
```

This creates a basic layout with placeholders that we'll update later.

## Step 7: Get Your API Token

To initialize the dotCMS client, you need an API token. Get one through:

1. **dotCMS UI**:
   - Log in to your dotCMS instance
   - Go to System → Users
   - Select your user account
   - Click "API Access Tokens" tab
   - Click "Request a new Token"
   - Copy the generated token

2. **REST API**:
   ```bash
   curl -s -H "Content-Type:application/json" -X POST -d '{
     "user":"admin@dotcms.com",
     "password":"admin",
     "expirationDays": 10
   }' https://demo.dotcms.com/api/v1/authentication/api-token | jq -r '.entity.token'
   ```

Save your API token securely.

## Step 8: Configure the Client

Update `config/dotcms.php`:

```php
<?php
use Dotcms\PhpSdk\Config\Config;

return new Config(
    host: 'https://demo.dotcms.com',
    apiKey: 'your-api-token',
    clientOptions: [
        'timeout' => 30,
        'verify' => true,
    ]
);
?>
```

This configuration establishes the connection to dotCMS with proper security and performance options.

## Step 9: Initialize the dotCMS PHP Client

Update `public/index.php` to initialize the client and fetch page data:

```php
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
```

This code loads dependencies, sets up the client, fetches page data from dotCMS, and handles errors gracefully. Note: We're using `$pageAsset` to avoid confusion with the nested `page` property.

## Step 10: Update Layout to Use Page Data

Now that we have the page data, update `templates/layout.php`:

```php
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($pageAsset->page->title ?? 'dotCMS Page') ?></title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/layout.css">
</head>
<body>
    <?php if ($pageAsset->layout->header): ?>
    <header>
        <?php include __DIR__ . '/navigation.php'; ?>
    </header>
    <?php endif; ?>
    
    <main>
        <?php include __DIR__ . '/page.php'; ?>
    </main>
    
    <?php if ($pageAsset->layout->footer): ?>
    <footer>
        <div class="footer-content container">
            <p>&copy; <?= date('Y') ?> Your Company Name. All rights reserved.</p>
        </div>
    </footer>
    <?php endif; ?>
</body>
</html>
```

This template now uses the `$pageAsset` object to dynamically set the title and conditionally render header and footer sections.

## Step 11: Update Navigation

Update `templates/navigation.php` to render dynamic navigation:

```php
<nav class="container" role="navigation" aria-label="Main navigation">
    <ul>
        <?php if (isset($nav)): ?>
            <?php 
            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            foreach ($nav->children as $item): 
                $isActive = $currentPath === $item->href;
            ?>
                <li>
                    <a href="<?= htmlspecialchars($item->href ?? '#') ?>" 
                       target="<?= htmlspecialchars($item->target ?? '_self') ?>"
                       <?= $isActive ? 'class="active"' : '' ?>>
                        <?= htmlspecialchars($item->title ?? 'Menu Item') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li><a href="/">Home (Fallback)</a></li>
        <?php endif; ?>
    </ul>
</nav>
```

This template iterates through navigation items from the API, adds active state handling, and includes accessibility attributes.

## Step 12: Add Grid System Styling

The grid system is essential for rendering dotCMS pages because it matches the layout structure defined in dotCMS. Here's how it works:

### Understanding the Grid System

#### Layout Structure in dotCMS

When you create a page in dotCMS, the layout consists of:
- **Rows**: Horizontal sections of the page
- **Columns**: Vertical divisions within each row (up to 12 columns)
- **Containers**: Content areas within columns that hold actual content (contentlets)

Each column has two important properties:
- `leftOffset`: Where the column starts (1-12)
- `width`: How many grid units the column spans (1-12)

#### Why We Need This Grid System

1. **Visual Editor Compatibility**: The grid system ensures what you see in the dotCMS visual editor is exactly what renders on your front-end
2. **Responsive Design**: The 12-column grid is a standard approach that works well across different screen sizes
3. **Flexible Layouts**: Content editors can create complex layouts without requiring developer intervention
4. **Precise Positioning**: Columns can start at any point and span any width, enabling sophisticated designs

#### How the Grid System Works

The CSS Grid system uses:
- `grid-template-columns: repeat(12, 1fr)` creates 12 equal-width columns
- `col-start-{n}` positions a column starting at grid line n
- `col-end-{n}` ends a column at grid line n
- The grid automatically handles gaps between columns

For example:
- A column with `leftOffset: 3` and `width: 6` spans from grid line 3 to 9
- A full-width column has `leftOffset: 1` and `width: 12`
- Two equal columns might have `leftOffset: 1, width: 6` and `leftOffset: 7, width: 6`

### Add the CSS

Add CSS for the grid system in `public/css/app.css`:

```css
/* Reset box-sizing to border-box for all elements and pseudo-elements
   This ensures padding and borders are included in element width calculations */
*, *::before, *::after {
    box-sizing: border-box;
}

/* Grid System */
.row {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 1rem;
}

.col-start-1 { grid-column-start: 1; }
.col-start-2 { grid-column-start: 2; }
.col-start-3 { grid-column-start: 3; }
.col-start-4 { grid-column-start: 4; }
.col-start-5 { grid-column-start: 5; }
.col-start-6 { grid-column-start: 6; }
.col-start-7 { grid-column-start: 7; }
.col-start-8 { grid-column-start: 8; }
.col-start-9 { grid-column-start: 9; }
.col-start-10 { grid-column-start: 10; }
.col-start-11 { grid-column-start: 11; }
.col-start-12 { grid-column-start: 12; }

.col-end-1 { grid-column-end: 1; }
.col-end-2 { grid-column-end: 2; }
.col-end-3 { grid-column-end: 3; }
.col-end-4 { grid-column-end: 4; }
.col-end-5 { grid-column-end: 5; }
.col-end-6 { grid-column-end: 6; }
.col-end-7 { grid-column-end: 7; }
.col-end-8 { grid-column-end: 8; }
.col-end-9 { grid-column-end: 9; }
.col-end-10 { grid-column-end: 10; }
.col-end-11 { grid-column-end: 11; }
.col-end-12 { grid-column-end: 12; }
.col-end-13 { grid-column-end: 13; }

/* Development helpers - remove in production */
.row {
    border: 1px solid blue;
    padding: 1rem;
}

.col-start-1[class*="col-end-"] {
    border: 2px solid green;
    padding: 0.5rem;
}
```

Note: Note: The blue and green borders are development helpers to visualize the grid system. Remove these styles in production.

Add layout styles in `public/css/layout.css`:

```css
/* Basic layout styling */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

body {
    font-family: Arial, sans-serif;
    line-height: 1.5;
    margin: 0;
    padding: 0;
}

header {
    background: #f4f4f4;
    padding: 1rem 0;
}

nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    gap: 1rem;
}

nav a {
    text-decoration: none;
    color: #333;
}

nav a:hover {
    color: #666;
}

nav a.active {
    font-weight: bold;
    color: #000;
}

main {
    padding: 2rem 0;
}

footer {
    background: #f4f4f4;
    padding: 1rem 0;
    text-align: center;
}
```

## Step 13: Render the Page Structure

Update `templates/page.php` to iterate through the page layout:

```php
<?php
global $pageAsset;

if (!isset($pageAsset)) {
    echo '<div class="container"><p>Error: Page asset is not set.</p></div>';
    return;
}

if (!isset($pageAsset->layout)) {
    echo '<div class="container"><p>Error: Layout data is not set.</p></div>';
    return;
}

if (!isset($pageAsset->layout->body->rows) || !is_array($pageAsset->layout->body->rows)) {
    echo '<div class="container"><p>No layout rows found or page data unavailable.</p></div>';
    return;
}

foreach ($pageAsset->layout->body->rows as $row) {
    include __DIR__ . '/partials/row.php'; 
}
```

This processes the page structure by iterating through rows with proper error handling.

## Step 14: Render Rows

Update `templates/partials/row.php`:

```php
<?php
if (!isset($row)) {
    echo "<!-- Error: Row data missing -->";
    return;
}

$rowStyleClass = isset($row->styleClass) ? ' ' . htmlspecialchars($row->styleClass) : '';
?>
<div class="container">
    <div class="row<?= $rowStyleClass ?>" 
         data-dot-object="row"
         data-dot-layout-row>
        <?php
        if (isset($row->columns) && is_array($row->columns)) {
            foreach ($row->columns as $column) {
                include __DIR__ . '/column.php';
            }
        } else {
            echo "<!-- No Columns found in this Row -->";
        }
        ?>
    </div>
</div>
```

Note: The row data uses array syntax (`$row->columns`), not object syntax.

## Step 15: Render Columns

Update `templates/partials/column.php`:

```php
<?php
if (!isset($column)) {
    echo "<!-- Error: Column data missing -->";
    return;
}

// Calculate grid classes based on dotCMS layout properties
$leftOffset = $column->leftOffset ?? 1;
$width = $column->width ?? 12;
$columnClasses = 'col-start-' . $leftOffset . ' col-end-' . ($width + $leftOffset);

if (isset($column->styleClass)) {
    $columnClasses .= ' ' . htmlspecialchars($column->styleClass);
}
?>
<div class="<?= $columnClasses ?>">
    <?php
    if (isset($column->containers) && is_array($column->containers)) {
        foreach ($column->containers as $containerRef) {
            include __DIR__ . '/container.php';
        }
    } else {
        echo "<!-- No Containers found in this Column -->";
    }
    ?>
</div>
```

Note: Column data also uses array syntax (`$column->leftOffset`).

## Step 16: Render Containers

Update `templates/partials/container.php`:

```php
<?php
if (!isset($containerRef)) {
    echo "<!-- Error: Container identifier missing -->";
    return;
}

global $pageAsset;

$identifier = $containerRef->identifier ?? null;
$contentlets = $containerRef->contentlets ?? null

if ($contentlets) {
    foreach ($contentlets as $contentlet) {
        $contentTypeVar = $contentlet->contentType ?? 'unknown';
        $templatePath = dirname(__DIR__) . '/content-type/' . strtolower($contentTypeVar) . '.php';

        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            include dirname(__DIR__) . '/content-type/content-type-not-found.php';
        }
    }
} else {
    echo "<!-- Container $identifier doesn't have contentlets -->";
}
?>
```

Note: This code handles the actual data structure where contentlets are nested under UUIDs.

## Step 17: Render Content Types

Update `templates/content-type/banner.php`:

```php
<?php
$title = $contentlet['title'] ?? '';
$caption = $contentlet['caption'] ?? '';
$image = $contentlet['image'] ?? null;
$link = $contentlet['link'] ?? '#';
$buttonText = $contentlet['buttonText'] ?? 'Learn More';
?>
<div style="position: relative; width: 100%; padding: 1rem; background-color: #e5e7eb; height: 24rem;">
    <?php if ($image): ?>
        <img 
            src="https://demo.dotcms.com/dA/<?= htmlspecialchars($image['idPath'] ?? $image) ?>" 
            style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;"
            alt="<?= htmlspecialchars($title) ?>"
        />
    <?php endif; ?>
    <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1rem; text-align: center; color: white;">
        <h2 style="margin-bottom: 0.5rem; font-size: 3.75rem; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
            <?= htmlspecialchars($title) ?>
        </h2>
        <?php if (!empty($caption)): ?>
            <p style="margin-bottom: 1rem; font-size: 1.25rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);"><?= htmlspecialchars($caption) ?></p>
        <?php endif; ?>
        <a 
            href="<?= htmlspecialchars($link) ?>"
            style="padding: 1rem; font-size: 1.25rem; background-color: #8b5cf6; color: white; text-decoration: none; border-radius: 0.25rem; transition: background-color 0.3s;"
            onmouseover="this.style.backgroundColor='#7c3aed'"
            onmouseout="this.style.backgroundColor='#8b5cf6'"
        >
            <?= htmlspecialchars($buttonText) ?>
        </a>
    </div>
</div>
```

The /dA/ path is dotCMS's image API for delivery image with top performance.

Create similar templates for other content types based on their fields.

## Step 18: Implementing Universal Visual Editor Support

The Universal Visual Editor (UVE) in dotCMS requires specific data attributes to enable in-context editing. We'll implement these attributes in a top-down approach: rows, columns, containers, and contentlets.

### Step 18.1: Adding UVE Support for Rows

Update `templates/partials/row.php` to include UVE data attributes:

```php
<?php
if (!isset($row)) {
    echo "<!-- Error: Row data missing -->";
    return;
}

$rowStyleClass = isset($row->styleClass) ? ' ' . htmlspecialchars($row->styleClass) : '';
?>
<div class="container">
    <div 
        data-dot-object="row"
        class="row<?= $rowStyleClass ?>"
    >
        <?php
        if (isset($row->columns) && is_array($row->columns)) {
            foreach ($row->columns as $column) {
                include __DIR__ . '/column.php';
            }
        } else {
            echo "<!-- No Columns found in this Row -->";
        }
        ?>
    </div>
</div>
```

The `data-dot-object="row"` attribute identifies this element as a row in the UVE.

### Step 18.2: Adding UVE Support for Columns

Update `templates/partials/column.php` to include UVE data attributes:

```php
<?php
if (!isset($column)) {
    echo "<!-- Error: Column data missing -->";
    return;
}

// Calculate grid classes based on dotCMS layout properties
$leftOffset = $column->leftOffset ?? 1;
$width = $column->width ?? 12;
$columnClasses = 'col-start-' . $leftOffset . ' col-end-' . ($width + $leftOffset);

if (isset($column->styleClass)) {
    $columnClasses .= ' ' . htmlspecialchars($column->styleClass);
}
?>
<div 
    data-dot-object="column"
    class="<?= $columnClasses ?>"
>
    <?php
    if (isset($column->containers) && is_array($column->containers)) {
        foreach ($column->containers as $containerRef) {
            include __DIR__ . '/container.php';
        }
    } else {
        echo "<!-- No Containers found in this Column -->";
    }
    ?>
</div>
```

The `data-dot-object="column"` attribute identifies this element as a column in the UVE.

### Step 18.3: Adding UVE Support for Containers and Contentlets

Update `templates/partials/container.php` to include UVE data attributes for both containers and contentlets:

```php
<?php
if (!isset($containerRef)) {
    echo "<!-- Error: Container identifier missing -->";
    return;
}

global $pageAsset;

$identifier = $containerRef->identifier ?? null;
$contentlets = $containerRef->contentlets ?? null

// Container attributes for UVE
$containerAttrs = [
    'data-dot-object' => 'container',
    'data-dot-identifier' => $identifier,
    'data-dot-uuid' => $uuid,
    'data-dot-accept-types' => $container->acceptTypes ?? '',
    'data-max-contentlets' => $container->maxContentlets ?? 0
];

// Build HTML attributes string
$htmlAttrs = '';
foreach ($containerAttrs as $attr => $value) {
    if ($value !== null && $value !== '') {
        $htmlAttrs .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
    }
}
?>
<div<?= $htmlAttrs ?>>
    <?php
    // Note: dotCMS stores contentlets with a "uuid-" prefix in lowercase
    $contentlets = $pageAsset->containers[$identifier]->contentlets[strtolower("uuid-" . $uuid)] ?? null;

    if ($contentlets) {
        foreach ($contentlets as $contentlet) {
            // Contentlet attributes for UVE
            $contentletAttrs = [
                'data-dot-object' => 'contentlet',
                'data-dot-identifier' => $contentlet->identifier ?? '',
                'data-dot-inode' => $contentlet->inode ?? '',
                'data-dot-type' => $contentlet->contentType ?? '',
                'data-dot-basetype' => $contentlet->baseType ?? '',
                'data-dot-title' => $contentlet->title ?? '',
                'data-dot-container' => json_encode([
                    'identifier' => $identifier,
                    'uuid' => $uuid,
                    'acceptTypes' => $container->acceptTypes ?? [],
                    'maxContentlets' => $container->maxContentlets ?? 0,
                    'variantId' => $containerRef->variantId ?? null
                ])
            ];

            // Build contentlet HTML attributes string
            $contentletHtmlAttrs = '';
            foreach ($contentletAttrs as $attr => $value) {
                if ($value !== null && $value !== '') {
                    $contentletHtmlAttrs .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
                }
            }

            $contentTypeVar = $contentlet->contentType ?? 'unknown';
            $templatePath = dirname(__DIR__) . '/content-type/' . strtolower($contentTypeVar) . '.php';
            ?>
            <div<?= $contentletHtmlAttrs ?>>
                <?php
                if (file_exists($templatePath)) {
                    include $templatePath;
                } else {
                    include dirname(__DIR__) . '/content-type/content-type-not-found.php';
                }
                ?>
            </div>
            <?php
        }
    } else {
        echo "<!-- Container $identifier doesn't have contentlets -->";
    }
    ?>
</div>
```
### Important Notes About UVE Implementation

1. **Data Attributes**: All UVE data attributes start with `data-dot-` prefix
2. **Container JSON**: The container data in contentlets must be JSON-encoded
3. **Unique Identifiers**: Make sure all identifiers (uuid, inode) are properly passed
4. **Error Handling**: Always validate data before outputting attributes
5. **Content Type Templates**: Keep content type templates focused on rendering content only
6. **Inheritance**: The UVE attributes follow the same hierarchy as the page structure

## Troubleshooting

### Common Issues

1. **Blank Page**: Check PHP error logs and ensure all file paths are correct
2. **No Content Showing**: Verify your API token is valid and the page exists in dotCMS
3. **Navigation Not Appearing**: Ensure pages are marked "Show on Menu" in dotCMS
4. **Grid Layout Not Working**: Check that CSS files are loading properly and the classes in the columns are set correctly

## Important Notes

1. **Data Structure**: All data from the dotCMS PHP SDK uses array syntax (`$data['property']`), not object syntax (`$data->property`)
2. **Variable Naming**: We use `$pageAsset` instead of `$page` to avoid confusion with the nested `page` property
3. **Error Handling**: Always check if data exists before using it
4. **File Naming**: Content type templates should match the `contentType` value in lowercase