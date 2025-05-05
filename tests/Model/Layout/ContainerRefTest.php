<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\Layout;

use Dotcms\PhpSdk\Model\Layout\ContainerRef;
use PHPUnit\Framework\TestCase;

class ContainerRefTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        // Test container with history UUIDs
        $container = new ContainerRef(
            identifier: 'container-1',
            uuid: 'uuid-1',
            historyUUIDs: ['history-1', 'history-2']
        );

        $this->assertEquals('container-1', $container->identifier);
        $this->assertEquals('uuid-1', $container->uuid);
        $this->assertIsArray($container->historyUUIDs);
        $this->assertCount(2, $container->historyUUIDs);
        $this->assertEquals('history-1', $container->historyUUIDs[0]);
        $this->assertEquals('history-2', $container->historyUUIDs[1]);

        // Test container without history UUIDs
        $containerWithoutHistory = new ContainerRef(
            identifier: 'container-2',
            uuid: 'uuid-2'
        );

        $this->assertEquals('container-2', $containerWithoutHistory->identifier);
        $this->assertEquals('uuid-2', $containerWithoutHistory->uuid);
        $this->assertIsArray($containerWithoutHistory->historyUUIDs);
        $this->assertEmpty($containerWithoutHistory->historyUUIDs);

        // Test container with empty history UUIDs array
        $containerWithEmptyHistory = new ContainerRef(
            identifier: 'container-3',
            uuid: 'uuid-3',
            historyUUIDs: []
        );

        $this->assertEquals('container-3', $containerWithEmptyHistory->identifier);
        $this->assertEquals('uuid-3', $containerWithEmptyHistory->uuid);
        $this->assertIsArray($containerWithEmptyHistory->historyUUIDs);
        $this->assertEmpty($containerWithEmptyHistory->historyUUIDs);
    }
}
