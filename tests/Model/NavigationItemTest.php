<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model;

use Dotcms\PhpSdk\Model\NavigationItem;
use PHPUnit\Framework\TestCase;

class NavigationItemTest extends TestCase
{
    /**
     * Test that the constructor properly sets properties
     */
    public function testConstructorSetsProperties(): void
    {
        $item = new NavigationItem(
            code: 'test-code',
            folder: 'test-folder',
            host: 'test-host',
            languageId: 1,
            href: '/test',
            title: 'Test Title',
            type: 'folder',
            hash: 12345,
            target: '_self',
            order: 1
        );

        $this->assertEquals('test-code', $item->code);
        $this->assertEquals('test-folder', $item->folder);
        $this->assertEquals('test-host', $item->host);
        $this->assertEquals(1, $item->languageId);
        $this->assertEquals('/test', $item->href);
        $this->assertEquals('Test Title', $item->title);
        $this->assertEquals('folder', $item->type);
        $this->assertEquals(12345, $item->hash);
        $this->assertEquals('_self', $item->target);
        $this->assertEquals(1, $item->order);
    }

    /**
     * Test that isFolder returns true for folder type
     */
    public function testIsFolderReturnsTrueForFolderType(): void
    {
        $item = new NavigationItem(
            code: null,
            folder: 'test-folder',
            host: 'test-host',
            languageId: 1,
            href: '/test',
            title: 'Test Title',
            type: 'folder',
            hash: 12345,
            target: '_self',
            order: 1
        );

        $this->assertTrue($item->isFolder());
        $this->assertFalse($item->isPage());
    }

    /**
     * Test that isPage returns true for htmlpage type
     */
    public function testIsPageReturnsTrueForHtmlpageType(): void
    {
        $item = new NavigationItem(
            code: null,
            folder: null,
            host: 'test-host',
            languageId: 1,
            href: '/test',
            title: 'Test Title',
            type: 'htmlpage',
            hash: 12345,
            target: '_self',
            order: 1
        );

        $this->assertTrue($item->isPage());
        $this->assertFalse($item->isFolder());
    }

    /**
     * Test that hasChildren returns false when no children
     */
    public function testHasChildrenReturnsFalseWhenNoChildren(): void
    {
        $item = new NavigationItem(
            code: null,
            folder: null,
            host: 'test-host',
            languageId: 1,
            href: '/test',
            title: 'Test Title',
            type: 'folder',
            hash: 12345,
            target: '_self',
            order: 1
        );

        $this->assertFalse($item->hasChildren());
        $this->assertNull($item->getChildren());
    }

    /**
     * Test that hasChildren returns true when children exist
     */
    public function testHasChildrenReturnsTrueWhenChildrenExist(): void
    {
        $item = new NavigationItem(
            code: null,
            folder: 'test-folder',
            host: 'test-host',
            languageId: 1,
            href: '/test',
            title: 'Test Title',
            type: 'folder',
            hash: 12345,
            target: '_self',
            order: 1,
            children: [
                [
                    'code' => null,
                    'folder' => null,
                    'host' => 'test-host',
                    'languageId' => 1,
                    'href' => '/test/child',
                    'title' => 'Child Title',
                    'type' => 'htmlpage',
                    'hash' => 67890,
                    'target' => '_self',
                    'order' => 1,
                ],
            ]
        );

        $this->assertTrue($item->hasChildren());
        $this->assertCount(1, $item->getChildren());
        $this->assertInstanceOf(NavigationItem::class, $item->getChildren()[0]);
        $this->assertEquals('Child Title', $item->getChildren()[0]->title);
        $this->assertEquals('/test/child', $item->getChildren()[0]->href);
    }

    /**
     * Test that jsonSerialize returns the correct structure
     */
    public function testJsonSerializeReturnsCorrectStructure(): void
    {
        $item = new NavigationItem(
            code: 'test-code',
            folder: 'test-folder',
            host: 'test-host',
            languageId: 1,
            href: '/test',
            title: 'Test Title',
            type: 'folder',
            hash: 12345,
            target: '_self',
            order: 1,
            children: [
                [
                    'code' => null,
                    'folder' => null,
                    'host' => 'test-host',
                    'languageId' => 1,
                    'href' => '/test/child',
                    'title' => 'Child Title',
                    'type' => 'htmlpage',
                    'hash' => 67890,
                    'target' => '_self',
                    'order' => 1,
                ],
            ]
        );

        $json = $item->jsonSerialize();

        $this->assertIsArray($json);
        $this->assertEquals('test-code', $json['code']);
        $this->assertEquals('test-folder', $json['folder']);
        $this->assertEquals('test-host', $json['host']);
        $this->assertEquals(1, $json['languageId']);
        $this->assertEquals('/test', $json['href']);
        $this->assertEquals('Test Title', $json['title']);
        $this->assertEquals('folder', $json['type']);
        $this->assertEquals(12345, $json['hash']);
        $this->assertEquals('_self', $json['target']);
        $this->assertEquals(1, $json['order']);
        $this->assertIsArray($json['children']);
        $this->assertCount(1, $json['children']);
        $this->assertInstanceOf(NavigationItem::class, $json['children'][0]);
    }

    /**
     * Test that nested children are properly created
     */
    public function testNestedChildrenAreProperlyCreated(): void
    {
        $item = new NavigationItem(
            code: null,
            folder: 'test-folder',
            host: 'test-host',
            languageId: 1,
            href: '/test',
            title: 'Test Title',
            type: 'folder',
            hash: 12345,
            target: '_self',
            order: 1,
            children: [
                [
                    'code' => null,
                    'folder' => 'child-folder',
                    'host' => 'test-host',
                    'languageId' => 1,
                    'href' => '/test/child',
                    'title' => 'Child Title',
                    'type' => 'folder',
                    'hash' => 67890,
                    'target' => '_self',
                    'order' => 1,
                    'children' => [
                        [
                            'code' => null,
                            'folder' => null,
                            'host' => 'test-host',
                            'languageId' => 1,
                            'href' => '/test/child/grandchild',
                            'title' => 'Grandchild Title',
                            'type' => 'htmlpage',
                            'hash' => 13579,
                            'target' => '_self',
                            'order' => 1,
                        ],
                    ],
                ],
            ]
        );

        $this->assertTrue($item->hasChildren());
        $this->assertCount(1, $item->getChildren());

        $child = $item->getChildren()[0];
        $this->assertTrue($child->hasChildren());
        $this->assertCount(1, $child->getChildren());

        $grandchild = $child->getChildren()[0];
        $this->assertFalse($grandchild->hasChildren());
        $this->assertEquals('Grandchild Title', $grandchild->title);
        $this->assertEquals('/test/child/grandchild', $grandchild->href);
        $this->assertEquals('htmlpage', $grandchild->type);
    }
}
