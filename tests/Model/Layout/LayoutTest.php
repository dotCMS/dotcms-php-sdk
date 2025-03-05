<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\Layout;

use Dotcms\PhpSdk\Model\Layout\Column;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;
use Dotcms\PhpSdk\Model\Layout\Layout;
use Dotcms\PhpSdk\Model\Layout\Row;
use PHPUnit\Framework\TestCase;

class LayoutTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        $layout = new Layout();

        $this->assertNull($layout->width);
        $this->assertEquals('', $layout->title);
        $this->assertTrue($layout->header);
        $this->assertTrue($layout->footer);
        $this->assertEquals(['rows' => []], $layout->body);
        $this->assertEquals([
            'containers' => [],
            'location' => '',
            'width' => 'small',
            'widthPercent' => 20,
            'preview' => false,
        ], $layout->sidebar);
        $this->assertEquals(1, $layout->version);
    }

    public function testCustomConstructor(): void
    {
        // Create a container reference
        $containerRef = new ContainerRef('container1', 'uuid1');

        // Create a column with the container
        $column = new Column(
            [$containerRef], // containers
            12, // width
            100, // widthPercent
            0, // leftOffset
            'col-md-12' // styleClass
        );

        // Create a row with the column
        $row = new Row([$column]);

        $layout = new Layout(
            '1200px',
            'Custom Layout',
            false,
            false,
            ['rows' => [$row]],
            [
                'containers' => [new ContainerRef('sidebar-container', 'sidebar-uuid')],
                'location' => 'right',
                'width' => 'medium',
                'widthPercent' => 30,
                'preview' => true,
            ],
            2
        );

        $this->assertEquals('1200px', $layout->width);
        $this->assertEquals('Custom Layout', $layout->title);
        $this->assertFalse($layout->header);
        $this->assertFalse($layout->footer);
        $this->assertCount(1, $layout->body['rows']);

        // Check row and column structure
        $rowFromLayout = $layout->body['rows'][0];
        $this->assertInstanceOf(Row::class, $rowFromLayout);
        $this->assertCount(1, $rowFromLayout->columns);

        $columnFromRow = $rowFromLayout->columns[0];
        $this->assertInstanceOf(Column::class, $columnFromRow);
        $this->assertEquals(12, $columnFromRow->width);
        $this->assertEquals(100, $columnFromRow->widthPercent);

        // Check sidebar containers
        $this->assertCount(1, $layout->sidebar['containers']);
        $sidebarContainer = $layout->sidebar['containers'][0];
        $this->assertInstanceOf(ContainerRef::class, $sidebarContainer);
        $this->assertEquals('sidebar-container', $sidebarContainer->identifier);
        $this->assertEquals('sidebar-uuid', $sidebarContainer->uuid);

        // Check sidebar properties
        $this->assertEquals('right', $layout->sidebar['location']);
        $this->assertEquals('medium', $layout->sidebar['width']);
        $this->assertEquals(30, $layout->sidebar['widthPercent']);
        $this->assertTrue($layout->sidebar['preview']);
        $this->assertEquals(2, $layout->version);
    }

    public function testGetRows(): void
    {
        // Create a container reference
        $containerRef = new ContainerRef('container1', 'uuid1');

        // Create a column with the container
        $column = new Column(
            [$containerRef], // containers
            12, // width
            100, // widthPercent
            0, // leftOffset
            'col-md-12' // styleClass
        );

        // Create a row with the column
        $row = new Row([$column]);

        $layout = new Layout(
            null,
            '',
            true,
            true,
            ['rows' => [$row]],
            ['containers' => []],
            1
        );

        $rows = $layout->getRows();

        $this->assertCount(1, $rows);
        $this->assertSame($row, $rows[0]);
    }

    public function testGetRowsWithEmptyBody(): void
    {
        $layout = new Layout(
            null,
            '',
            true,
            true,
            [],
            ['containers' => []],
            1
        );

        $rows = $layout->getRows();

        $this->assertIsArray($rows);
        $this->assertEmpty($rows);
    }

    public function testGetSidebarContainers(): void
    {
        $container = new ContainerRef('container1', 'uuid1');
        $layout = new Layout(
            null,
            '',
            true,
            true,
            ['rows' => []],
            [
                'containers' => [$container],
                'location' => 'right',
                'width' => 'small',
                'widthPercent' => 20,
                'preview' => false,
            ],
            1
        );

        $containers = $layout->getSidebarContainers();

        $this->assertCount(1, $containers);
        $this->assertSame($container, $containers[0]);
    }

    public function testGetSidebarContainersWithEmptySidebar(): void
    {
        $layout = new Layout(
            null,
            '',
            true,
            true,
            ['rows' => []],
            [],
            1
        );

        $containers = $layout->getSidebarContainers();

        $this->assertIsArray($containers);
        $this->assertEmpty($containers);
    }

    public function testJsonSerialize(): void
    {
        // Create a container reference
        $containerRef = new ContainerRef('container1', 'uuid1');

        // Create a column with the container
        $column = new Column(
            [$containerRef], // containers
            12, // width
            100, // widthPercent
            0, // leftOffset
            'col-md-12' // styleClass
        );

        // Create a row with the column
        $row = new Row([$column]);

        $layout = new Layout(
            '1200px',
            'Custom Layout',
            false,
            false,
            ['rows' => [$row]],
            [
                'containers' => [new ContainerRef('sidebar-container', 'sidebar-uuid')],
                'location' => 'right',
                'width' => 'medium',
                'widthPercent' => 30,
                'preview' => true,
            ],
            2
        );

        $json = $layout->jsonSerialize();

        $this->assertEquals('1200px', $json['width']);
        $this->assertEquals('Custom Layout', $json['title']);
        $this->assertFalse($json['header']);
        $this->assertFalse($json['footer']);
        $this->assertIsArray($json['body']);
        $this->assertIsArray($json['sidebar']);
        $this->assertEquals(2, $json['version']);

        // Check that the body contains rows
        $this->assertArrayHasKey('rows', $json['body']);
        $this->assertIsArray($json['body']['rows']);
        $this->assertCount(1, $json['body']['rows']);

        // Since the Row object is not automatically serialized in the JSON output,
        // we need to manually check the Row object
        $rowObject = $layout->body['rows'][0];
        $this->assertInstanceOf(Row::class, $rowObject);

        // Check the Row's columns
        $this->assertIsArray($rowObject->columns);
        $this->assertCount(1, $rowObject->columns);
        $this->assertInstanceOf(Column::class, $rowObject->columns[0]);
        $this->assertEquals(12, $rowObject->columns[0]->width);

        // Check sidebar containers
        $this->assertIsArray($json['sidebar']['containers']);
        $this->assertCount(1, $json['sidebar']['containers']);

        // Since the ContainerRef object is not automatically serialized in the JSON output,
        // we need to manually check the ContainerRef object
        $containerObject = $layout->sidebar['containers'][0];
        $this->assertInstanceOf(ContainerRef::class, $containerObject);
        $this->assertEquals('sidebar-container', $containerObject->identifier);
        $this->assertEquals('sidebar-uuid', $containerObject->uuid);
    }
}
