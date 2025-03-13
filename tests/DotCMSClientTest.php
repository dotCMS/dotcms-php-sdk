<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests;

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\DotCMSClient;
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
        // Create a mock PageAsset
        $pageAssetMock = $this->createMock(PageAsset::class);

        // Create a mock PageRequest
        $pageRequestMock = $this->createMock(PageRequest::class);

        // Create a mock PageService that will return our mock PageAsset
        $pageServiceMock = $this->createMock(PageService::class);
        $pageServiceMock->expects($this->once())
            ->method('getPage')
            ->with($pageRequestMock)
            ->willReturn($pageAssetMock);

        // Create a partial mock of DotCMSClient with the pageService property replaced
        $clientMock = $this->createPartialMock(DotCMSClient::class, []);

        // Set the mocked pageService using reflection
        $reflection = new \ReflectionClass($clientMock);
        $property = $reflection->getProperty('pageService');
        $property->setAccessible(true);
        $property->setValue($clientMock, $pageServiceMock);

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

        // Create a mock PageService that will return our mock Promise
        $pageServiceMock = $this->createMock(PageService::class);
        $pageServiceMock->expects($this->once())
            ->method('getPageAsync')
            ->with($pageRequestMock)
            ->willReturn($promiseMock);

        // Create a partial mock of DotCMSClient with the pageService property replaced
        $clientMock = $this->createPartialMock(DotCMSClient::class, []);

        // Set the mocked pageService using reflection
        $reflection = new \ReflectionClass($clientMock);
        $property = $reflection->getProperty('pageService');
        $property->setAccessible(true);
        $property->setValue($clientMock, $pageServiceMock);

        // Call the method
        $result = $clientMock->getPageAsync($pageRequestMock);

        // Assert the result
        $this->assertSame($promiseMock, $result);
    }
}
