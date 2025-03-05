<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Service;

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\Exception\ResponseException;
use Dotcms\PhpSdk\Http\HttpClient;
use Dotcms\PhpSdk\Http\Response as DotcmsResponse;
use Dotcms\PhpSdk\Model\PageAsset;
use Dotcms\PhpSdk\Request\PageRequest;
use Dotcms\PhpSdk\Service\PageService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * Test-specific HttpClient that allows injecting a mock client
 */
class TestHttpClient extends HttpClient
{
    private MockHandler $mockHandler;

    public function __construct(MockHandler $mockHandler)
    {
        $config = new Config(
            'https://demo.dotcms.com/api/v1',
            'test-api-key',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'verify' => false,
                'timeout' => 30,
                'connect_timeout' => 5,
                'http_errors' => false
            ]
        );
        $this->mockHandler = $mockHandler;
        parent::__construct($config);
    }

    protected function createClient(): Client
    {
        $handlerStack = HandlerStack::create($this->mockHandler);
        return new Client(['handler' => $handlerStack]);
    }

    public function request(string $method, string $uri, array $options = []): DotcmsResponse
    {
        $client = $this->createClient();
        $response = $client->request($method, $uri, $options);
        return new TestResponse($response);
    }

    public function requestAsync(string $method, string $uri, array $options = []): PromiseInterface
    {
        $client = $this->createClient();
        $promise = $client->requestAsync($method, $uri, $options);
        
        return $promise->then(function ($response) {
            return new TestResponse($response);
        });
    }
}

/**
 * Custom Response class for testing
 */
class TestResponse extends DotcmsResponse
{
    private ResponseInterface $originalResponse;

    public function __construct(ResponseInterface $response)
    {
        $this->originalResponse = $response;
        parent::__construct($response);
    }

    public function toArray(): array
    {
        $body = (string)$this->originalResponse->getBody();
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ResponseException('Invalid JSON response: ' . json_last_error_msg());
        }
        
        return $data;
    }
}

class PageServiceTest extends TestCase
{
    private TestHttpClient $httpClient;

    private MockHandler $mockHandler;

    private PageService $pageService;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        // Create our test-specific HttpClient with the mock client
        $this->httpClient = new TestHttpClient($this->mockHandler);
        $this->pageService = new PageService($this->httpClient);
    }

    public function testGetPage(): void
    {
        $responseJson = file_get_contents(__DIR__ . '/../fixtures/page-response.json');
        echo "Response JSON: " . $responseJson . "\n";

        $this->mockHandler->append(
            new GuzzleResponse(
                200,
                ['Content-Type' => 'application/json'],
                $responseJson
            )
        );

        $request = new PageRequest('/about-us');
        $page = $this->pageService->getPage($request);

        // Debug output
        var_dump($page);

        $this->assertInstanceOf(PageAsset::class, $page);
        $this->assertEquals('about-us', $page->page->identifier);
        $this->assertEquals('About Us', $page->page->title);
        $this->assertEquals('about-us', $page->page->pageUrl);
        $this->assertEquals('demo.dotcms.com', $page->page->hostName);
        $this->assertEquals('48190c8c-42c4-46af-8d1a-0cd5db894797', $page->page->host);
        $this->assertEquals('WebPageContent', $page->page->contentType);
        $this->assertTrue($page->page->live);
        $this->assertTrue($page->page->working);
    }

    public function testGetPageAsset(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(
                200,
                ['Content-Type' => 'application/json'],
                file_get_contents(__DIR__ . '/../fixtures/page-response.json')
            )
        );

        $request = new PageRequest('/about-us');
        $pageAsset = $this->pageService->getPageAsset($request);

        $this->assertInstanceOf(PageAsset::class, $pageAsset);
        $this->assertEquals('about-us', $pageAsset->page->identifier);
        $this->assertEquals('About Us', $pageAsset->page->title);
        $this->assertEquals('about-us', $pageAsset->page->pageUrl);
        $this->assertEquals('demo.dotcms.com', $pageAsset->page->hostName);
        $this->assertEquals('48190c8c-42c4-46af-8d1a-0cd5db894797', $pageAsset->page->host);
        $this->assertEquals('WebPageContent', $pageAsset->page->contentType);
        $this->assertTrue($pageAsset->page->live);
        $this->assertTrue($pageAsset->page->working);

        // Test layout
        $this->assertEquals('Default Layout', $pageAsset->layout->title);
        $this->assertEquals(1, $pageAsset->layout->version);
        $this->assertTrue($pageAsset->layout->header);
        $this->assertTrue($pageAsset->layout->footer);

        // Test template
        $this->assertEquals('Default', $pageAsset->template->title);
        $this->assertEquals('c541abb1-69b3-4bc5-8430-5e09e5239cc8', $pageAsset->template->identifier);
        $this->assertTrue($pageAsset->template->drawed);
        $this->assertTrue($pageAsset->template->header);
        $this->assertTrue($pageAsset->template->footer);
        $this->assertTrue($pageAsset->template->live);
        $this->assertTrue($pageAsset->template->working);

        // Test site
        $this->assertEquals('48190c8c-42c4-46af-8d1a-0cd5db894797', $pageAsset->site->identifier);
        $this->assertEquals('demo.dotcms.com', $pageAsset->site->hostname);
        $this->assertTrue($pageAsset->site->live);
        $this->assertTrue($pageAsset->site->working);
        $this->assertFalse($pageAsset->site->locked);
        $this->assertFalse($pageAsset->site->archived);
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

        // Debug output
        var_dump($page);

        $this->assertInstanceOf(PageAsset::class, $page);
        $this->assertEquals('about-us', $page->page->identifier);
        $this->assertEquals('About Us', $page->page->title);
    }

    public function testGetPageAssetAsync(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(
                200,
                ['Content-Type' => 'application/json'],
                file_get_contents(__DIR__ . '/../fixtures/page-response.json')
            )
        );

        $request = new PageRequest('/about-us');
        $promise = $this->pageService->getPageAssetAsync($request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $pageAsset = $promise->wait();

        // Debug output
        var_dump($pageAsset);

        $this->assertInstanceOf(PageAsset::class, $pageAsset);
        $this->assertEquals('about-us', $pageAsset->page->identifier);
        $this->assertEquals('About Us', $pageAsset->page->title);
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
                    'entity' => ['page' => []],
                    'layout' => ['title' => 'Test Layout', 'body' => ['rows' => []]],
                    'template' => ['identifier' => 'test-template'],
                    'site' => ['identifier' => 'test-site'],
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
                    'entity' => ['page' => ['identifier' => 'test-page']],
                    'template' => ['identifier' => 'test-template'],
                    'site' => ['identifier' => 'test-site'],
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
                    'entity' => ['page' => ['identifier' => 'test-page']],
                    'layout' => ['title' => 'Test Layout', 'body' => ['rows' => []]],
                    'site' => ['identifier' => 'test-site'],
                ])
            )
        );

        $request = new PageRequest('/about-us');

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Template data not found in response: template is missing');

        $this->pageService->getPage($request);
    }

    public function testGetPageWithMissingSite(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(
                200,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'entity' => ['page' => ['identifier' => 'test-page']],
                    'layout' => ['title' => 'Test Layout', 'body' => ['rows' => []]],
                    'template' => ['identifier' => 'test-template'],
                ])
            )
        );

        $request = new PageRequest('/about-us');

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Site data not found in response: site is missing');

        $this->pageService->getPage($request);
    }
}
