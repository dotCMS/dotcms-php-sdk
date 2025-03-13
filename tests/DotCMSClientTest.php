<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests;

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\DotCMSClient;
use Dotcms\PhpSdk\Http\HttpClient;
use Dotcms\PhpSdk\Model\NavigationItem;
use Dotcms\PhpSdk\Model\PageAsset;
use Dotcms\PhpSdk\Request\NavigationRequest;
use Dotcms\PhpSdk\Request\PageRequest;
use Dotcms\PhpSdk\Service\NavigationService;
use Dotcms\PhpSdk\Service\PageService;
use GuzzleHttp\Promise\FulfilledPromise;
use PHPUnit\Framework\TestCase;

class DotCMSClientTest extends TestCase
{
    private Config $config;

    private DotCMSClient $client;

    protected function setUp(): void
    {
        $this->config = new Config(
            host: 'https://demo.dotcms.com',
            apiKey: 'test-api-key'
        );

        $this->client = new DotCMSClient($this->config);
    }

    public function testCreatePageRequest(): void
    {
        $pagePath = '/about-us/index';
        $format = 'json';

        $request = $this->client->createPageRequest($pagePath, $format);

        $this->assertInstanceOf(PageRequest::class, $request);
        $this->assertEquals($pagePath, $request->getPagePath());
        $this->assertEquals($format, $request->getFormat());
    }

    public function testCreateNavigationRequest(): void
    {
        // Test with default values
        $request = $this->client->createNavigationRequest();
        $this->assertInstanceOf(NavigationRequest::class, $request);
        $this->assertEquals('/', $request->getPath());
        $this->assertEquals(1, $request->getDepth());
        $this->assertEquals(1, $request->getLanguageId());

        // Test with custom values
        $path = '/about-us';
        $depth = 2;
        $languageId = 3;
        $request = $this->client->createNavigationRequest($path, $depth, $languageId);
        $this->assertInstanceOf(NavigationRequest::class, $request);
        $this->assertEquals($path, $request->getPath());
        $this->assertEquals($depth, $request->getDepth());
        $this->assertEquals($languageId, $request->getLanguageId());
    }

    public function testGetPage(): void
    {
        /*
         * This test is currently skipped because it would make a real HTTP request.
         *
         * To properly test this method without making HTTP requests, consider one of these approaches:
         *
         * 1. Modify DotCMSClient to accept a PageService in the constructor for testing:
         *    - Change constructor to: __construct(Config $config, ?PageService $pageService = null)
         *    - Initialize pageService as: $this->pageService = $pageService ?? new PageService($this->httpClient);
         *    - Then in tests, create a mock PageService and inject it
         *
         * 2. Use a mocking framework that can mock final/private methods and properties:
         *    - Tools like Mockery or PHPUnit's MockBuilder with disableOriginalConstructor()
         *    - Create a partial mock of DotCMSClient and override the getPage method
         *
         * 3. Create an integration test suite separate from unit tests:
         *    - Set up a test environment with a mock API server
         *    - Run these tests only in specific environments
         *
         * Example implementation with approach #1:
         *
         * // Create mocks
         * $pageAssetMock = $this->createMock(PageAsset::class);
         * $pageRequestMock = $this->createMock(PageRequest::class);
         * $pageServiceMock = $this->createMock(PageService::class);
         *
         * // Configure the mock
         * $pageServiceMock->expects($this->once())
         *     ->method('getPage')
         *     ->with($pageRequestMock)
         *     ->willReturn($pageAssetMock);
         *
         * // Create client with mock service
         * $client = new DotCMSClient($this->config, $pageServiceMock);
         *
         * // Call the method
         * $result = $client->getPage($pageRequestMock);
         *
         * // Assert the result
         * $this->assertSame($pageAssetMock, $result);
         */
        $this->markTestSkipped('This test would make an HTTP request. See comments for implementation options.');
    }

    public function testGetPageAsync(): void
    {
        /*
         * This test is currently skipped because it would make a real HTTP request.
         *
         * To properly test this method without making HTTP requests, consider one of these approaches:
         *
         * 1. Modify DotCMSClient to accept a PageService in the constructor for testing:
         *    - Change constructor to: __construct(Config $config, ?PageService $pageService = null)
         *    - Initialize pageService as: $this->pageService = $pageService ?? new PageService($this->httpClient);
         *    - Then in tests, create a mock PageService and inject it
         *
         * 2. Use a mocking framework that can mock final/private methods and properties:
         *    - Tools like Mockery or PHPUnit's MockBuilder with disableOriginalConstructor()
         *    - Create a partial mock of DotCMSClient and override the getPageAsync method
         *
         * 3. Create an integration test suite separate from unit tests:
         *    - Set up a test environment with a mock API server
         *    - Run these tests only in specific environments
         *
         * Example implementation with approach #1:
         *
         * // Create mocks
         * $pageAssetMock = $this->createMock(PageAsset::class);
         * $pageRequestMock = $this->createMock(PageRequest::class);
         * $promiseMock = new FulfilledPromise($pageAssetMock);
         * $pageServiceMock = $this->createMock(PageService::class);
         *
         * // Configure the mock
         * $pageServiceMock->expects($this->once())
         *     ->method('getPageAsync')
         *     ->with($pageRequestMock)
         *     ->willReturn($promiseMock);
         *
         * // Create client with mock service
         * $client = new DotCMSClient($this->config, $pageServiceMock);
         *
         * // Call the method
         * $result = $client->getPageAsync($pageRequestMock);
         *
         * // Assert the result
         * $this->assertSame($promiseMock, $result);
         */
        $this->markTestSkipped('This test would make an HTTP request. See comments for implementation options.');
    }

    public function testGetNavigation(): void
    {
        /*
         * This test is currently skipped because it would make a real HTTP request.
         *
         * To properly test this method without making HTTP requests, consider one of these approaches:
         *
         * 1. Modify DotCMSClient to accept a NavigationService in the constructor for testing:
         *    - Change constructor to: __construct(Config $config, ?PageService $pageService = null, ?NavigationService $navigationService = null)
         *    - Initialize navigationService as: $this->navigationService = $navigationService ?? new NavigationService($this->httpClient);
         *    - Then in tests, create a mock NavigationService and inject it
         *
         * Example implementation with approach #1:
         *
         * // Create mocks
         * $navigationItemMock = $this->createMock(NavigationItem::class);
         * $navigationRequestMock = $this->createMock(NavigationRequest::class);
         * $navigationServiceMock = $this->createMock(NavigationService::class);
         *
         * // Configure the mock
         * $navigationServiceMock->expects($this->once())
         *     ->method('getNavigation')
         *     ->with($navigationRequestMock)
         *     ->willReturn($navigationItemMock);
         *
         * // Create client with mock service
         * $client = new DotCMSClient($this->config, null, $navigationServiceMock);
         *
         * // Call the method
         * $result = $client->getNavigation($navigationRequestMock);
         *
         * // Assert the result
         * $this->assertSame($navigationItemMock, $result);
         */
        $this->markTestSkipped('This test would make an HTTP request. See comments for implementation options.');
    }

    public function testGetNavigationAsync(): void
    {
        /*
         * This test is currently skipped because it would make a real HTTP request.
         *
         * To properly test this method without making HTTP requests, consider one of these approaches:
         *
         * 1. Modify DotCMSClient to accept a NavigationService in the constructor for testing:
         *    - Change constructor to: __construct(Config $config, ?PageService $pageService = null, ?NavigationService $navigationService = null)
         *    - Initialize navigationService as: $this->navigationService = $navigationService ?? new NavigationService($this->httpClient);
         *    - Then in tests, create a mock NavigationService and inject it
         *
         * Example implementation with approach #1:
         *
         * // Create mocks
         * $navigationItemMock = $this->createMock(NavigationItem::class);
         * $navigationRequestMock = $this->createMock(NavigationRequest::class);
         * $promiseMock = new FulfilledPromise($navigationItemMock);
         * $navigationServiceMock = $this->createMock(NavigationService::class);
         *
         * // Configure the mock
         * $navigationServiceMock->expects($this->once())
         *     ->method('getNavigationAsync')
         *     ->with($navigationRequestMock)
         *     ->willReturn($promiseMock);
         *
         * // Create client with mock service
         * $client = new DotCMSClient($this->config, null, $navigationServiceMock);
         *
         * // Call the method
         * $result = $client->getNavigationAsync($navigationRequestMock);
         *
         * // Assert the result
         * $this->assertSame($promiseMock, $result);
         */
        $this->markTestSkipped('This test would make an HTTP request. See comments for implementation options.');
    }
}
