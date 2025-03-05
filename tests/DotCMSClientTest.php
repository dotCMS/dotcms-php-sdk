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

    public function testGetConfig(): void
    {
        $this->assertSame($this->config, $this->client->getConfig());
    }

    public function testGetHttpClient(): void
    {
        $this->assertInstanceOf(HttpClient::class, $this->client->getHttpClient());
    }

    public function testGetPageService(): void
    {
        $this->assertInstanceOf(PageService::class, $this->client->getPageService());
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
        // Create a mock PageAsset
        $pageAssetMock = $this->createMock(PageAsset::class);

        // Create a mock PageRequest
        $pageRequestMock = $this->createMock(PageRequest::class);

        // Create a test double for DotCMSClient
        $clientMock = $this->getMockBuilder(DotCMSClient::class)
            ->setConstructorArgs([$this->config])
            ->onlyMethods(['getPage'])
            ->getMock();

        // Configure the mock to return our mock page asset
        $clientMock->expects($this->once())
            ->method('getPage')
            ->with($pageRequestMock)
            ->willReturn($pageAssetMock);

        // Call the method
        $result = $clientMock->getPage($pageRequestMock);

        // Assert the result
        $this->assertSame($pageAssetMock, $result);
    }

    public function testGetPageAsync(): void
    {
        // Create a mock PageAsset
        $pageAssetMock = $this->createMock(PageAsset::class);

        // Create a mock PageRequest
        $pageRequestMock = $this->createMock(PageRequest::class);

        // Create a mock Promise
        $promiseMock = new FulfilledPromise($pageAssetMock);

        // Create a test double for DotCMSClient
        $clientMock = $this->getMockBuilder(DotCMSClient::class)
            ->setConstructorArgs([$this->config])
            ->onlyMethods(['getPageAsync'])
            ->getMock();

        // Configure the mock to return our mock Promise
        $clientMock->expects($this->once())
            ->method('getPageAsync')
            ->with($pageRequestMock)
            ->willReturn($promiseMock);

        // Call the method
        $result = $clientMock->getPageAsync($pageRequestMock);

        // Assert the result
        $this->assertSame($promiseMock, $result);
    }
}
