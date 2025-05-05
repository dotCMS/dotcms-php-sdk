<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\Layout;

use Dotcms\PhpSdk\Model\Layout\Column;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;
use Dotcms\PhpSdk\Model\Layout\Row;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        // Test row with style class
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

        $this->assertIsArray($row->columns);
        $this->assertCount(1, $row->columns);
        $this->assertInstanceOf(Column::class, $row->columns[0]);
        $this->assertEquals('test-row', $row->styleClass);

        // Test row without style class
        $rowWithoutStyle = new Row(
            columns: [$column]
        );

        $this->assertIsArray($rowWithoutStyle->columns);
        $this->assertCount(1, $rowWithoutStyle->columns);
        $this->assertInstanceOf(Column::class, $rowWithoutStyle->columns[0]);
        $this->assertNull($rowWithoutStyle->styleClass);

        // Test row with multiple columns
        $column2 = new Column(
            containers: [],
            width: 6,
            widthPercent: 50,
            leftOffset: 0,
            styleClass: 'test-column-2'
        );

        $rowWithMultipleColumns = new Row(
            columns: [$column, $column2],
            styleClass: 'multi-column-row'
        );

        $this->assertIsArray($rowWithMultipleColumns->columns);
        $this->assertCount(2, $rowWithMultipleColumns->columns);
        $this->assertInstanceOf(Column::class, $rowWithMultipleColumns->columns[0]);
        $this->assertInstanceOf(Column::class, $rowWithMultipleColumns->columns[1]);
        $this->assertEquals('multi-column-row', $rowWithMultipleColumns->styleClass);
        $this->assertEquals(12, $rowWithMultipleColumns->columns[0]->width);
        $this->assertEquals(6, $rowWithMultipleColumns->columns[1]->width);
    }
}
