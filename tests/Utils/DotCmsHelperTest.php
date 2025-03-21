<?php

namespace Dotcms\PhpSdk\Tests\Utils;

use Dotcms\PhpSdk\Utils\DotCmsHelper;
use PHPUnit\Framework\TestCase;

class DotCmsHelperTest extends TestCase
{
    public function testGetContainerData(): void
    {
        $containers = [
            'abc123' => ['title' => 'Test Container'],
            'def456' => ['title' => 'Another Container'],
        ];

        $container = ['identifier' => 'abc123'];
        $result = DotCmsHelper::getContainerData($containers, $container);
        $this->assertEquals(['title' => 'Test Container'], $result);

        // Test with non-existent identifier
        $container = ['identifier' => 'nonexistent'];
        $result = DotCmsHelper::getContainerData($containers, $container);
        $this->assertNull($result);

        // Test with empty inputs
        $this->assertNull(DotCmsHelper::getContainerData([], []));
        $this->assertNull(DotCmsHelper::getContainerData($containers, []));
        $this->assertNull(DotCmsHelper::getContainerData([], $container));
    }

    public function testHtmlAttributes(): void
    {
        // Test with regular attributes
        $attributes = [
            'id' => 'test-id',
            'class' => 'test-class',
            'data-test' => 'value',
        ];
        $expected = ' id="test-id" class="test-class" data-test="value"';
        $this->assertEquals($expected, DotCmsHelper::htmlAttributes($attributes));

        // Test with boolean attributes
        $attributes = [
            'disabled' => true,
            'readonly' => false,
        ];
        $expected = ' disabled';
        $this->assertEquals($expected, DotCmsHelper::htmlAttributes($attributes));

        // Test with mixed attributes
        $attributes = [
            'id' => 'test-id',
            'disabled' => true,
            'readonly' => false,
        ];
        $expected = ' id="test-id" disabled';
        $this->assertEquals($expected, DotCmsHelper::htmlAttributes($attributes));

        // Test with special characters
        $attributes = [
            'data-content' => 'A "quoted" & <special> string',
        ];
        $expected = ' data-content="A &quot;quoted&quot; &amp; &lt;special&gt; string"';
        $this->assertEquals($expected, DotCmsHelper::htmlAttributes($attributes));

        // Test with empty input
        $this->assertEquals('', DotCmsHelper::htmlAttributes([]));
    }

    public function testSimpleContentHtml(): void
    {
        // Test with title
        $content = [
            'contentType' => 'Article',
            'title' => 'Test Title',
        ];
        $expected = '<div class="dotcms-content" data-content-type="Article"><h3>Test Title</h3></div>';
        $this->assertEquals($expected, DotCmsHelper::simpleContentHtml($content));

        // Test with name but no title
        $content = [
            'contentType' => 'Page',
            'name' => 'Test Name',
        ];
        $expected = '<div class="dotcms-content" data-content-type="Page"><h3>Test Name</h3></div>';
        $this->assertEquals($expected, DotCmsHelper::simpleContentHtml($content));

        // Test with no title or name
        $content = [
            'contentType' => 'Widget',
        ];
        $expected = '<div class="dotcms-content" data-content-type="Widget"><h3>No Title</h3></div>';
        $this->assertEquals($expected, DotCmsHelper::simpleContentHtml($content));

        // Test with no content type
        $content = [
            'title' => 'Test Title',
        ];
        $expected = '<div class="dotcms-content" data-content-type="unknown"><h3>Test Title</h3></div>';
        $this->assertEquals($expected, DotCmsHelper::simpleContentHtml($content));

        // Test with empty content
        $this->assertEquals('', DotCmsHelper::simpleContentHtml([]));
    }
}
