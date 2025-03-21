<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Service;

use Dotcms\PhpSdk\Exception\ResponseException;
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
}
