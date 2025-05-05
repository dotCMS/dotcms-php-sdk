<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\Layout;

use Dotcms\PhpSdk\Model\Layout\ContainerRef;
use Dotcms\PhpSdk\Model\Content\Contentlet;
use PHPUnit\Framework\TestCase;

class ContainerRefTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        $contentlet = new Contentlet(
            identifier: 'content-1',
            inode: 'inode-1',
            title: 'Test Content',
            contentType: 'test-type'
        );

        // Test container with all properties
        $container = new ContainerRef(
            identifier: 'container-1',
            uuid: 'uuid-1',
            historyUUIDs: ['history-1', 'history-2'],
            contentlets: [$contentlet],
            acceptTypes: 'test-type,other-type',
            maxContentlets: 5,
            variantId: 123
        );

        $this->assertEquals('container-1', $container->identifier);
        $this->assertEquals('uuid-1', $container->uuid);
        $this->assertIsArray($container->historyUUIDs);
        $this->assertCount(2, $container->historyUUIDs);
        $this->assertEquals('history-1', $container->historyUUIDs[0]);
        $this->assertEquals('history-2', $container->historyUUIDs[1]);
        $this->assertIsArray($container->contentlets);
        $this->assertCount(1, $container->contentlets);
        $this->assertInstanceOf(Contentlet::class, $container->contentlets[0]);
        $this->assertEquals('test-type,other-type', $container->acceptTypes);
        $this->assertEquals(5, $container->maxContentlets);
        $this->assertEquals(123, $container->variantId);

        // Test container with minimal properties
        $containerMinimal = new ContainerRef(
            identifier: 'container-2',
            uuid: 'uuid-2'
        );

        $this->assertEquals('container-2', $containerMinimal->identifier);
        $this->assertEquals('uuid-2', $containerMinimal->uuid);
        $this->assertIsArray($containerMinimal->historyUUIDs);
        $this->assertEmpty($containerMinimal->historyUUIDs);
        $this->assertIsArray($containerMinimal->contentlets);
        $this->assertEmpty($containerMinimal->contentlets);
        $this->assertEquals('', $containerMinimal->acceptTypes);
        $this->assertEquals(0, $containerMinimal->maxContentlets);
        $this->assertNull($containerMinimal->variantId);
    }
}
