<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\Container;

use Dotcms\PhpSdk\Model\Container\Container;
use Dotcms\PhpSdk\Model\Container\ContainerPage;
use Dotcms\PhpSdk\Model\Container\ContainerStructure;
use Dotcms\PhpSdk\Model\Content\Contentlet;
use PHPUnit\Framework\TestCase;

class ContainerPageTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        $container = new Container(
            identifier: 'test-container',
            inode: '123e4567-e89b-12d3-a456-426614174000',
            title: 'Test Container',
            path: '/test-container',
            live: true,
            working: true,
            locked: false,
            hostId: 'host-1',
            hostName: 'Test Host',
            maxContentlets: 5,
            notes: 'Test notes'
        );

        $containerStructure = new ContainerStructure(
            id: 'structure-1',
            structureId: '123e4567-e89b-12d3-a456-426614174001',
            containerInode: '123e4567-e89b-12d3-a456-426614174000',
            containerId: 'test-container',
            code: '<div>Test Structure</div>',
            contentTypeVar: 'test-content-type'
        );

        $contentlet = new Contentlet(
            identifier: 'test-content',
            inode: '123e4567-e89b-12d3-a456-426614174002',
            title: 'Test Content',
            contentType: 'test-content-type',
            working: true,
            live: true
        );

        $containerPage = new ContainerPage(
            container: $container,
            containerStructures: [$containerStructure],
            rendered: ['123e4567-e89b-12d3-a456-426614174002' => '<div>Rendered Content</div>'],
            contentlets: ['123e4567-e89b-12d3-a456-426614174002' => [$contentlet]]
        );

        // Test container property
        $this->assertInstanceOf(Container::class, $containerPage->container);
        $this->assertEquals('test-container', $containerPage->container->identifier);
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $containerPage->container->inode);
        $this->assertEquals('Test Container', $containerPage->container->title);

        // Test containerStructures array
        $this->assertCount(1, $containerPage->containerStructures);
        $this->assertInstanceOf(ContainerStructure::class, $containerPage->containerStructures[0]);
        $this->assertEquals('structure-1', $containerPage->containerStructures[0]->id);
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174001', $containerPage->containerStructures[0]->structureId);
        $this->assertEquals('test-container', $containerPage->containerStructures[0]->containerId);

        // Test rendered array
        $this->assertCount(1, $containerPage->rendered);
        $this->assertEquals('<div>Rendered Content</div>', $containerPage->rendered['123e4567-e89b-12d3-a456-426614174002']);

        // Test contentlets array
        $this->assertCount(1, $containerPage->contentlets);
        $this->assertCount(1, $containerPage->contentlets['123e4567-e89b-12d3-a456-426614174002']);
        $this->assertInstanceOf(Contentlet::class, $containerPage->contentlets['123e4567-e89b-12d3-a456-426614174002'][0]);
        $this->assertEquals('test-content', $containerPage->contentlets['123e4567-e89b-12d3-a456-426614174002'][0]->identifier);
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174002', $containerPage->contentlets['123e4567-e89b-12d3-a456-426614174002'][0]->inode);
        $this->assertEquals('Test Content', $containerPage->contentlets['123e4567-e89b-12d3-a456-426614174002'][0]->title);
    }

    public function testDefaultValues(): void
    {
        $container = new Container(
            identifier: 'test-container',
            inode: '123e4567-e89b-12d3-a456-426614174000',
            title: 'Test Container',
            path: '/test-container',
            live: true,
            working: true,
            locked: false,
            hostId: 'host-1',
            hostName: 'Test Host',
            maxContentlets: 5,
            notes: 'Test notes'
        );

        $containerPage = new ContainerPage(
            container: $container
        );

        $this->assertEmpty($containerPage->containerStructures);
        $this->assertEmpty($containerPage->rendered);
        $this->assertEmpty($containerPage->contentlets);
    }
}
