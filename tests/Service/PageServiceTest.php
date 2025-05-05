<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Service;

use Dotcms\PhpSdk\Exception\ResponseException;
use Dotcms\PhpSdk\Model\Layout\Body;
use Dotcms\PhpSdk\Model\Layout\Column;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;
use Dotcms\PhpSdk\Model\Layout\Layout;
use Dotcms\PhpSdk\Model\Layout\Row;
use Dotcms\PhpSdk\Model\PageAsset;
use Dotcms\PhpSdk\Request\PageRequest;
use Dotcms\PhpSdk\Service\PageService;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use PHPUnit\Framework\TestCase;

class PageServiceTest extends TestCase
{
    private MockHandler $mockHandler;

    private TestHttpClient $httpClient;

    private PageService $pageService;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();

        // Create our test-specific HttpClient with the mock handler
        $this->httpClient = new TestHttpClient($this->mockHandler);
        $this->pageService = new PageService($this->httpClient);
    }

    public function testGetPage(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(
                200,
                ['Content-Type' => 'application/json'],
                file_get_contents(__DIR__ . '/../fixtures/page-response.json')
            )
        );

        $request = new PageRequest('/about-us');
        $page = $this->pageService->getPage($request);

        $this->assertInstanceOf(PageAsset::class, $page);
        $this->assertEquals('about-us', $page->page->identifier);
        $this->assertEquals('About Us', $page->page->title);
        $this->assertEquals('about-us', $page->page->pageUrl);
        $this->assertEquals('demo.dotcms.com', $page->page->hostName);
        $this->assertEquals('48190c8c-42c4-46af-8d1a-0cd5db894797', $page->page->host);
        $this->assertEquals('WebPageContent', $page->page->contentType);
        $this->assertTrue($page->page->live);
        $this->assertTrue($page->page->working);

        // Test layout
        $this->assertEquals('Default Layout', $page->layout->title);
        $this->assertEquals(1, $page->layout->version);
        $this->assertTrue($page->layout->header);
        $this->assertTrue($page->layout->footer);

        // Test template
        $this->assertEquals('Default', $page->template->title);
        $this->assertEquals('c541abb1-69b3-4bc5-8430-5e09e5239cc8', $page->template->identifier);
        $this->assertTrue($page->template->drawed);
        $this->assertTrue($page->template->header);
        $this->assertTrue($page->template->footer);
        $this->assertTrue($page->template->live);
        $this->assertTrue($page->template->working);

        // Test site
        $this->assertEquals('48190c8c-42c4-46af-8d1a-0cd5db894797', $page->site->identifier);
        $this->assertEquals('demo.dotcms.com', $page->site->hostname);
        $this->assertTrue($page->site->live);
        $this->assertTrue($page->site->working);
        $this->assertFalse($page->site->locked);
        $this->assertFalse($page->site->archived);
    }

    public function testGetPageAsync(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(
                200,
                ['Content-Type' => 'application/json'],
                file_get_contents(__DIR__ . '/../fixtures/page-response.json')
            )
        );

        $request = new PageRequest('/about-us');
        $promise = $this->pageService->getPageAsync($request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $page = $promise->wait();


        $this->assertInstanceOf(PageAsset::class, $page);
        $this->assertEquals('about-us', $page->page->identifier);
        $this->assertEquals('About Us', $page->page->title);

        // Test layout
        $this->assertEquals('Default Layout', $page->layout->title);
        $this->assertEquals(1, $page->layout->version);
        $this->assertTrue($page->layout->header);
        $this->assertTrue($page->layout->footer);

        // Test template
        $this->assertEquals('Default', $page->template->title);
        $this->assertEquals('c541abb1-69b3-4bc5-8430-5e09e5239cc8', $page->template->identifier);
        $this->assertTrue($page->template->drawed);
        $this->assertTrue($page->template->header);
        $this->assertTrue($page->template->footer);
        $this->assertTrue($page->template->live);
        $this->assertTrue($page->template->working);

        // Test site
        $this->assertEquals('48190c8c-42c4-46af-8d1a-0cd5db894797', $page->site->identifier);
        $this->assertEquals('demo.dotcms.com', $page->site->hostname);
        $this->assertTrue($page->site->live);
        $this->assertTrue($page->site->working);
        $this->assertFalse($page->site->locked);
        $this->assertFalse($page->site->archived);
    }

    public function testGetPageWithInvalidResponse(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(
                200,
                ['Content-Type' => 'application/json'],
                json_encode(['entity' => []])
            )
        );

        $request = new PageRequest('/about-us');

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Page data not found in response: entity.page is missing');

        $this->pageService->getPage($request);
    }

    public function testGetPageWithEmptyPageData(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(
                200,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'entity' => [
                        'page' => [],
                        'layout' => ['title' => 'Test Layout', 'body' => ['rows' => []]],
                        'template' => ['identifier' => 'test-template'],
                        'site' => ['identifier' => 'test-site'],
                    ],
                ])
            )
        );

        $request = new PageRequest('/about-us');
        $page = $this->pageService->getPage($request);

        $this->assertInstanceOf(PageAsset::class, $page);
        $this->assertEquals('', $page->page->identifier);
        $this->assertEquals('', $page->page->title);
    }

    public function testGetPageWithMissingLayout(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(
                200,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'entity' => [
                        'page' => ['identifier' => 'test-page'],
                        'template' => ['identifier' => 'test-template'],
                        'site' => ['identifier' => 'test-site'],
                    ],
                ])
            )
        );

        $request = new PageRequest('/about-us');

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage("This page don't have a layout, maybe because you're using an advanced template");

        $this->pageService->getPage($request);
    }

    public function testGetPageWithMissingTemplate(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(
                200,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'entity' => [
                        'page' => ['identifier' => 'test-page'],
                        'layout' => ['title' => 'Test Layout', 'body' => ['rows' => []]],
                        'site' => ['identifier' => 'test-site'],
                    ],
                ])
            )
        );

        $request = new PageRequest('/about-us');

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Template data not found in response: entity.template is missing');

        $this->pageService->getPage($request);
    }

    public function testGetPageWithMissingSite(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(
                200,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'entity' => [
                        'page' => ['identifier' => 'test-page'],
                        'layout' => ['title' => 'Test Layout', 'body' => ['rows' => []]],
                        'template' => ['identifier' => 'test-template'],
                    ],
                ])
            )
        );

        $request = new PageRequest('/about-us');

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Site data not found in response: entity.site is missing');

        $this->pageService->getPage($request);
    }

    public function testGetPageWithDifferentLayoutFormat(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(
                200,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'entity' => [
                        'page' => ['identifier' => 'test-page'],
                        'layout' => [
                            'title' => 'Test Layout',
                            'body' => [
                                'rows' => [
                                    [
                                        'columns' => [
                                            ['width' => 12, 'containers' => []],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'template' => ['identifier' => 'test-template'],
                        'site' => ['identifier' => 'test-site'],
                    ],
                ])
            )
        );

        $request = new PageRequest('/about-us');
        $page = $this->pageService->getPage($request);

        $this->assertInstanceOf(PageAsset::class, $page);
        $this->assertEquals('Test Layout', $page->layout->title);
        $this->assertIsArray($page->layout->body->rows);
        $this->assertCount(1, $page->layout->body->rows);
        $this->assertIsArray($page->layout->body->rows[0]->columns);
        $this->assertCount(1, $page->layout->body->rows[0]->columns);
        $this->assertEquals(12, $page->layout->body->rows[0]->columns[0]->width);
    }

    public function testLayoutJsonSerialization(): void
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
            title: 'anonymouslayout1600437132653',
            body: $body
        );

        // Test direct rows access
        $this->assertCount(1, $layout->body->rows);
        $this->assertInstanceOf(Row::class, $layout->body->rows[0]);
        $this->assertEquals('p-0 banner-tall', $layout->body->rows[0]->styleClass);

        // Test body access
        $this->assertInstanceOf(Body::class, $layout->body);
        $this->assertCount(1, $layout->body->rows);
        $this->assertInstanceOf(Row::class, $layout->body->rows[0]);
        $this->assertEquals('p-0 banner-tall', $layout->body->rows[0]->styleClass);

        $this->assertNull($layout->width);
        $this->assertEquals('anonymouslayout1600437132653', $layout->title);
        $this->assertTrue($layout->header);
        $this->assertTrue($layout->footer);
        $this->assertInstanceOf(Body::class, $layout->body);
        $this->assertCount(1, $layout->body->rows);
        $this->assertEquals('p-0 banner-tall', $layout->body->rows[0]->styleClass);

        $firstRow = $layout->body->rows[0];
        $this->assertEquals('p-0 banner-tall', $firstRow->styleClass);
        $this->assertIsArray($firstRow->columns);
        $this->assertCount(1, $firstRow->columns);

        $firstColumn = $firstRow->columns[0];
        $this->assertEquals(12, $firstColumn->width);
        $this->assertEquals(100, $firstColumn->widthPercent);
        $this->assertEquals(1, $firstColumn->leftOffset);
        $this->assertEquals('banner-tall', $firstColumn->styleClass);
        $this->assertFalse($firstColumn->preview);
        $this->assertEquals(0, $firstColumn->left);
        $this->assertIsArray($firstColumn->containers);
        $this->assertCount(1, $firstColumn->containers);

        $firstContainer = $firstColumn->containers[0];
        $this->assertEquals('//demo.dotcms.com/application/containers/banner/', $firstContainer->identifier);
        $this->assertEquals('1', $firstContainer->uuid);
        $this->assertEquals(['1'], $firstContainer->historyUUIDs);

        $this->assertEquals([
            'containers' => [],
            'location' => '',
            'width' => 'small',
            'widthPercent' => 20,
            'preview' => false,
        ], $layout->sidebar);
        $this->assertEquals(1, $layout->version);
    }
}
