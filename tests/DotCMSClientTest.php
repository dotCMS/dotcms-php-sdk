<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests;

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\DotCMSClient;
use Dotcms\PhpSdk\Http\HttpClient;
use Dotcms\PhpSdk\Model\PageAsset;
use Dotcms\PhpSdk\Request\PageRequest;
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
}
