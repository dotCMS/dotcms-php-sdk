<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\Layout;

use Dotcms\PhpSdk\Model\Layout\Column;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        // Test column with all properties
        $containerRef = new ContainerRef('container-1', 'uuid-1', ['history-1']);
        $column = new Column(
            containers: [$containerRef],
            width: 12,
            widthPercent: 100,
            leftOffset: 0,
            styleClass: 'test-column',
            preview: true,
            left: 10
        );

        $this->assertIsArray($column->containers);
        $this->assertCount(1, $column->containers);
        $this->assertInstanceOf(ContainerRef::class, $column->containers[0]);
        $this->assertEquals('container-1', $column->containers[0]->identifier);
        $this->assertEquals('uuid-1', $column->containers[0]->uuid);
        $this->assertEquals(['history-1'], $column->containers[0]->historyUUIDs);

        $this->assertEquals(12, $column->width);
        $this->assertEquals(100, $column->widthPercent);
        $this->assertEquals(0, $column->leftOffset);
        $this->assertEquals('test-column', $column->styleClass);
        $this->assertTrue($column->preview);
        $this->assertEquals(10, $column->left);

        // Test column with default values
        $columnWithDefaults = new Column(
            containers: [],
            width: 6,
            widthPercent: 50,
            leftOffset: 5,
            styleClass: 'default-column'
        );

        $this->assertIsArray($columnWithDefaults->containers);
        $this->assertEmpty($columnWithDefaults->containers);
        $this->assertEquals(6, $columnWithDefaults->width);
        $this->assertEquals(50, $columnWithDefaults->widthPercent);
        $this->assertEquals(5, $columnWithDefaults->leftOffset);
        $this->assertEquals('default-column', $columnWithDefaults->styleClass);
        $this->assertFalse($columnWithDefaults->preview);
        $this->assertEquals(0, $columnWithDefaults->left);

        // Test column with multiple containers
        $containerRef2 = new ContainerRef('container-2', 'uuid-2', ['history-2']);
        $columnWithMultipleContainers = new Column(
            containers: [$containerRef, $containerRef2],
            width: 8,
            widthPercent: 75,
            leftOffset: 2,
            styleClass: 'multi-container-column'
        );

        $this->assertIsArray($columnWithMultipleContainers->containers);
        $this->assertCount(2, $columnWithMultipleContainers->containers);
        $this->assertInstanceOf(ContainerRef::class, $columnWithMultipleContainers->containers[0]);
        $this->assertInstanceOf(ContainerRef::class, $columnWithMultipleContainers->containers[1]);
        $this->assertEquals('container-1', $columnWithMultipleContainers->containers[0]->identifier);
        $this->assertEquals('container-2', $columnWithMultipleContainers->containers[1]->identifier);
        $this->assertEquals(8, $columnWithMultipleContainers->width);
        $this->assertEquals(75, $columnWithMultipleContainers->widthPercent);
        $this->assertEquals(2, $columnWithMultipleContainers->leftOffset);
        $this->assertEquals('multi-container-column', $columnWithMultipleContainers->styleClass);
    }
}
