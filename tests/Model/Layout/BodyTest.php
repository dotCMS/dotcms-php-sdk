<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\Layout;

use Dotcms\PhpSdk\Model\Layout\Body;
use Dotcms\PhpSdk\Model\Layout\Column;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;
use Dotcms\PhpSdk\Model\Layout\Row;
use PHPUnit\Framework\TestCase;

class BodyTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        // Test empty body
        $emptyBody = new Body();
        $this->assertIsArray($emptyBody->rows);
        $this->assertEmpty($emptyBody->rows);

        // Test body with rows
        $containerRef = new ContainerRef('container-1', 'uuid-1', ['history-1']);
        $column = new Column(
            containers: [$containerRef],
            width: 12,
            widthPercent: 100,
            leftOffset: 0,
            styleClass: 'test-column'
        );
        $row = new Row(
            columns: [$column],
            styleClass: 'test-row'
        );

        $body = new Body([$row]);

        $this->assertIsArray($body->rows);
        $this->assertCount(1, $body->rows);
        $this->assertInstanceOf(Row::class, $body->rows[0]);
        $this->assertEquals('test-row', $body->rows[0]->styleClass);

        // Test row properties
        $firstRow = $body->rows[0];
        $this->assertIsArray($firstRow->columns);
        $this->assertCount(1, $firstRow->columns);
        $this->assertInstanceOf(Column::class, $firstRow->columns[0]);

        // Test column properties
        $firstColumn = $firstRow->columns[0];
        $this->assertEquals(12, $firstColumn->width);
        $this->assertEquals(100, $firstColumn->widthPercent);
        $this->assertEquals(0, $firstColumn->leftOffset);
        $this->assertEquals('test-column', $firstColumn->styleClass);
        $this->assertFalse($firstColumn->preview);
        $this->assertEquals(0, $firstColumn->left);

        // Test container properties
        $this->assertIsArray($firstColumn->containers);
        $this->assertCount(1, $firstColumn->containers);
        $this->assertInstanceOf(ContainerRef::class, $firstColumn->containers[0]);
        $this->assertEquals('container-1', $firstColumn->containers[0]->identifier);
        $this->assertEquals('uuid-1', $firstColumn->containers[0]->uuid);
        $this->assertEquals(['history-1'], $firstColumn->containers[0]->historyUUIDs);
    }
}
