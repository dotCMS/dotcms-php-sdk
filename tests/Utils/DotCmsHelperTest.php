<?php

namespace Dotcms\PhpSdk\Tests\Utils;

use Dotcms\PhpSdk\Model\Container\Container;
use Dotcms\PhpSdk\Model\Container\ContainerPage;
use Dotcms\PhpSdk\Model\Container\ContainerStructure;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;
use Dotcms\PhpSdk\Utils\DotCmsHelper;
use PHPUnit\Framework\TestCase;

class DotCmsHelperTest extends TestCase
{
    public function testGetContainerData(): void
    {
        $container = new Container(
            identifier: 'abc123',
            inode: 'inode123',
            title: 'Test Container',
            path: '/test',
            maxContentlets: 0,
            additionalProperties: [
                'parentPermissionable' => ['variantId' => null],
            ]
        );

        $containerStructures = [
            new ContainerStructure(
                id: 'struct1',
                structureId: 'struct1',
                containerInode: 'inode123',
                containerId: 'abc123',
                code: '',
                contentTypeVar: 'Banner'
            ),
            new ContainerStructure(
                id: 'struct2',
                structureId: 'struct2',
                containerInode: 'inode123',
                containerId: 'abc123',
                code: '',
                contentTypeVar: 'Widget'
            ),
        ];

        $containerPage = new ContainerPage(
            container: $container,
            containerStructures: $containerStructures,
            rendered: [],
            contentlets: [
                'uuid-123' => [
                    ['title' => 'Test Content'],
                ],
            ]
        );

        $containers = [
            'abc123' => $containerPage,
        ];

        // Test with valid container
        $containerRef = new ContainerRef(
            identifier: 'abc123',
            uuid: '123',
            historyUUIDs: [],
            contentlets: [],
            acceptTypes: '',
            maxContentlets: 0
        );

        $result = DotCmsHelper::getContainerData($containers, $containerRef);
        $this->assertEquals([
            'identifier' => 'abc123',
            'inode' => 'inode123',
            'title' => 'Test Container',
            'path' => '/test',
            'live' => false,
            'working' => false,
            'locked' => false,
            'hostId' => '',
            'hostName' => '',
            'maxContentlets' => 0,
            'notes' => '',
            'parentPermissionable' => ['variantId' => null],
            'acceptTypes' => 'Banner,Widget',
            'contentlets' => [['title' => 'Test Content']],
            'variantId' => null,
        ], $result);

        // Test with non-existent identifier
        $nonExistentRef = new ContainerRef(
            identifier: 'nonexistent',
            uuid: '',
            historyUUIDs: [],
            contentlets: [],
            acceptTypes: '',
            maxContentlets: 0
        );
        $result = DotCmsHelper::getContainerData($containers, $nonExistentRef);
        $this->assertNull($result);

        // Test with empty containers
        $this->assertNull(DotCmsHelper::getContainerData([], $containerRef));
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
