<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\Layout;

use Dotcms\PhpSdk\Model\Layout\Body;
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
        $this->assertInstanceOf(Body::class, $layout->body);
        $this->assertEmpty($layout->body->rows);
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
        $containerRef = new ContainerRef('//demo.dotcms.com/application/containers/banner/', '1', ['1']);
        $column = new Column(
            containers: [$containerRef],
            width: 12,
            widthPercent: 100,
            leftOffset: 1,
            styleClass: 'banner-tall'
        );
        $row = new Row(
            columns: [$column],
            styleClass: 'p-0 banner-tall'
        );

        $body = new Body([$row]);

        $layout = new Layout(
            width: '100%',
            title: 'Test Layout',
            header: false,
            footer: false,
            body: $body,
            sidebar: [
                'containers' => [$containerRef],
                'location' => 'left',
                'width' => 'medium',
                'widthPercent' => 30,
                'preview' => true,
            ],
            version: 2
        );

        $this->assertEquals('100%', $layout->width);
        $this->assertEquals('Test Layout', $layout->title);
        $this->assertFalse($layout->header);
        $this->assertFalse($layout->footer);
        $this->assertInstanceOf(Body::class, $layout->body);
        $this->assertCount(1, $layout->body->rows);
        $this->assertEquals('p-0 banner-tall', $layout->body->rows[0]->styleClass);
        $this->assertEquals([
            'containers' => [$containerRef],
            'location' => 'left',
            'width' => 'medium',
            'widthPercent' => 30,
            'preview' => true,
        ], $layout->sidebar);
        $this->assertEquals(2, $layout->version);
    }

    public function testGetSidebarContainers(): void
    {
        $containerRef = new ContainerRef('//demo.dotcms.com/application/containers/banner/', '1', ['1']);
        $body = new Body();

        $layout = new Layout(
            body: $body,
            sidebar: [
                'containers' => [$containerRef],
                'location' => 'left',
                'width' => 'medium',
                'widthPercent' => 30,
                'preview' => true,
            ]
        );

        $containers = $layout->getSidebarContainers();
        $this->assertCount(1, $containers);
        $this->assertEquals($containerRef, $containers[0]);
    }

    public function testGetSidebarContainersWithEmptySidebar(): void
    {
        $body = new Body();

        $layout = new Layout(
            body: $body,
            sidebar: [
                'containers' => [],
                'location' => '',
                'width' => 'small',
                'widthPercent' => 20,
                'preview' => false,
            ]
        );

        $containers = $layout->getSidebarContainers();
        $this->assertEmpty($containers);
    }

    public function testJsonSerialize(): void
    {
        $containerRef = new ContainerRef('//demo.dotcms.com/application/containers/banner/', '1', ['1']);
        $column = new Column(
            containers: [$containerRef],
            width: 12,
            widthPercent: 100,
            leftOffset: 1,
            styleClass: 'banner-tall'
        );
        $row = new Row(
            columns: [$column],
            styleClass: 'p-0 banner-tall'
        );

        $body = new Body([$row]);

        $layout = new Layout(
            width: '100%',
            title: 'Test Layout',
            header: false,
            footer: false,
            body: $body,
            sidebar: [
                'containers' => [$containerRef],
                'location' => 'left',
                'width' => 'medium',
                'widthPercent' => 30,
                'preview' => true,
            ],
            version: 2
        );

        $json = $layout->jsonSerialize();

        $this->assertEquals('100%', $json['width']);
        $this->assertEquals('Test Layout', $json['title']);
        $this->assertFalse($json['header']);
        $this->assertFalse($json['footer']);
        $this->assertArrayHasKey('body', $json);
        $this->assertArrayHasKey('rows', $json['body']);
        $this->assertCount(1, $json['body']['rows']);
        $this->assertEquals('p-0 banner-tall', $json['body']['rows'][0]['styleClass']);
        $this->assertEquals([
            'containers' => [$containerRef],
            'location' => 'left',
            'width' => 'medium',
            'widthPercent' => 30,
            'preview' => true,
        ], $json['sidebar']);
        $this->assertEquals(2, $json['version']);
    }
}
