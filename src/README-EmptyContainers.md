# DotCMS Empty Container Support

The DotCMS PHP SDK now includes built-in support for empty container handling with UVE (Universal Visual Editor) compatibility.

## Features

- **Automatic empty container detection** in UVE edit mode
- **Visual placeholders** with "This container is empty." message
- **Full UVE compatibility** with blue highlighting and + buttons
- **Framework-agnostic** implementation that works with Twig, Blade, and plain PHP
- **Built-in CSS** for professional styling

## Quick Start

### 1. Use SDK Container Rendering

Instead of manually building container HTML, use the SDK's `renderContainer()` method:

**Twig (Symfony):**
```twig
{{ renderContainer(container, container.contentlets, mode)|raw }}
```

**Blade (Laravel):**
```blade
{!! $dotCmsHelpers->renderContainer($containerRef, $containerRef->contentlets ?? [], $mode ?? null) !!}
```

**Plain PHP:**
```php
echo DotCmsHelper::renderContainer($containerRef, $contentlets, $mode, $contentRenderer);
```

### 2. Include CSS Styles

**Option A: Dynamic CSS (Recommended)**

**Twig:**
```twig
<style>{{ getEmptyContainerCSS()|raw }}</style>
```

**Blade:**
```blade
<style>{!! $dotCmsHelpers->getEmptyContainerCSS() !!}</style>
```

**Plain PHP:**
```php
echo '<style>' . DotCmsHelper::getEmptyContainerCSS() . '</style>';
```

**Option B: Static CSS**
Copy the CSS from `DotCmsHelper::getEmptyContainerCSS()` into your stylesheet.

## SDK Methods

### DotCmsHelper::renderContainer()

Complete container rendering with empty state support:

```php
DotCmsHelper::renderContainer(
    ContainerRef $containerRef,     // Container reference object
    array $contentlets,             // Array of contentlet objects  
    ?string $mode = null,           // Current mode ('EDIT_MODE' for UVE)
    ?callable $contentRenderer = null // Custom content rendering function
): string
```

### DotCmsHelper::getEmptyContainerCSS()

Returns CSS styles for empty container placeholders:

```php
DotCmsHelper::getEmptyContainerCSS(): string
```

### Individual Helper Methods

For advanced customization, you can use individual helper methods:

```php
// Generate container attributes
DotCmsHelper::getContainerAttributes(ContainerRef $containerRef): array

// Generate contentlet attributes  
DotCmsHelper::getContentletAttributes(Contentlet $content, ContainerRef $containerRef): array

// Generate ghost contentlet attributes for empty containers
DotCmsHelper::getGhostContentletAttributes(ContainerRef $containerRef): array

// Check if in edit mode
DotCmsHelper::isEditMode(?string $mode): bool

// Generate empty placeholder HTML
DotCmsHelper::generateEmptyContainerPlaceholder(string $message = 'This container is empty.'): string
```

## Framework Integration

### Symfony/Twig

Add to your Twig extension:

```php
public function getFunctions(): array
{
    return [
        // ... existing functions
        new TwigFunction('renderContainer', [$this, 'renderContainer'], ['is_safe' => ['html']]),
        new TwigFunction('getEmptyContainerCSS', [$this, 'getEmptyContainerCSS'], ['is_safe' => ['html']])
    ];
}

public function renderContainer(ContainerRef $containerRef, array $contentlets, ?string $mode = null): string
{
    return DotCmsHelper::renderContainer(
        $containerRef,
        $contentlets, 
        $mode,
        function(Contentlet $content) {
            return $this->generateHtmlBasedOnProperty($content);
        }
    );
}

public function getEmptyContainerCSS(): string
{
    return DotCmsHelper::getEmptyContainerCSS();
}
```

### Laravel/Blade

Add to your helper class:

```php
public function renderContainer(ContainerRef $containerRef, array $contentlets, ?string $mode = null)
{
    return DotCmsHelper::renderContainer(
        $containerRef,
        $contentlets,
        $mode,
        function(Contentlet $content) {
            return $this->generateHtmlBasedOnProperty($content);
        }
    );
}

public function getEmptyContainerCSS()
{
    return DotCmsHelper::getEmptyContainerCSS();
}
```

## UVE Compatibility

The SDK automatically handles UVE compatibility by:

1. **Adding required data attributes** for container and contentlet detection
2. **Creating ghost contentlets** for empty containers in edit mode
3. **Ensuring proper hover targets** for UVE interactions
4. **Maintaining event flow** for drag-and-drop functionality

## Customization

### Custom Empty Messages

```php
$placeholder = DotCmsHelper::generateEmptyContainerPlaceholder('Drag content here');
```

### Custom Content Rendering

Pass a custom content renderer to `renderContainer()`:

```php
DotCmsHelper::renderContainer($containerRef, $contentlets, $mode, function($content) {
    return "<div class='custom'>{$content->title}</div>";
});
```

### Custom CSS

Override the CSS classes `.empty-container-placeholder` and `.uve-ghost-contentlet` in your stylesheet to customize the appearance.

## Migration from Manual Implementation

If you have existing manual container implementations:

1. **Replace container templates** with SDK `renderContainer()` calls
2. **Remove manual UVE attribute handling**
3. **Include SDK CSS** for empty container styling
4. **Update mode parameter passing** to ensure `mode` is available to containers

The SDK handles all the complexity of UVE compatibility, empty state detection, and proper HTML generation automatically. 