<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Service;

use Dotcms\PhpSdk\Exception\ResponseException;
use Dotcms\PhpSdk\Model\NavigationItem;
use Dotcms\PhpSdk\Request\NavigationRequest;
use Dotcms\PhpSdk\Service\NavigationService;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use PHPUnit\Framework\TestCase;

class NavigationServiceTest extends TestCase
{
    private MockHandler $mockHandler;

    private NavigationService $service;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $httpClient = new TestHttpClient($this->mockHandler);
        $this->service = new NavigationService($httpClient);
    }

    /**
     * Test that getNavigation returns a NavigationItem
     */
    public function testGetNavigationReturnsNavigationItem(): void
    {
        // Create a mock response
        $responseData = [
            'entity' => [
                'code' => null,
                'folder' => 'test-folder',
                'host' => 'test-host',
                'languageId' => 1,
                'href' => '/test',
                'title' => 'Test Title',
                'type' => 'folder',
                'hash' => 12345,
                'target' => '_self',
                'order' => 1,
                'children' => [
                    [
                        'code' => null,
                        'folder' => null,
                        'host' => 'test-host',
                        'languageId' => 1,
                        'href' => '/test/child',
                        'title' => 'Child Title',
                        'type' => 'htmlpage',
                        'hash' => 67890,
                        'target' => '_self',
                        'order' => 1,
                    ],
                ],
            ],
            'errors' => [],
            'messages' => [],
            'i18nMessagesMap' => [],
            'permissions' => [],
        ];

        $this->mockHandler->append(
            new GuzzleResponse(200, ['Content-Type' => 'application/json'], json_encode($responseData))
        );

        $request = new NavigationRequest('/test', 2);
        $result = $this->service->getNavigation($request);

        $this->assertInstanceOf(NavigationItem::class, $result);
        $this->assertEquals('Test Title', $result->title);
        $this->assertEquals('/test', $result->href);
        $this->assertEquals('folder', $result->type);
        $this->assertTrue($result->hasChildren());
        $this->assertCount(1, $result->getChildren());
        $this->assertEquals('Child Title', $result->getChildren()[0]->title);
    }

    /**
     * Test that getNavigation returns a NavigationItem with default values when entity is missing
     */
    public function testGetNavigationReturnsNavigationItemWithDefaultValuesWhenEntityIsMissing(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(200, ['Content-Type' => 'application/json'], json_encode([
                'errors' => [],
                'messages' => [],
            ]))
        );

        $request = new NavigationRequest('/test');
        $result = $this->service->getNavigation($request);

        $this->assertInstanceOf(NavigationItem::class, $result);
        $this->assertEquals('', $result->title);
        $this->assertEquals('', $result->href);
        $this->assertEquals('folder', $result->type);
        $this->assertEquals('', $result->host);
        $this->assertEquals(1, $result->languageId);
        $this->assertEquals(0, $result->hash);
        $this->assertEquals('_self', $result->target);
        $this->assertEquals(0, $result->order);
        $this->assertFalse($result->hasChildren());
    }

    /**
     * Test that getNavigation returns a NavigationItem with default values when fields are missing
     */
    public function testGetNavigationReturnsNavigationItemWithDefaultValuesWhenFieldsAreMissing(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(200, ['Content-Type' => 'application/json'], json_encode([
                'entity' => [
                    'title' => 'Test Title',
                    // Missing other fields
                ],
                'errors' => [],
                'messages' => [],
            ]))
        );

        $request = new NavigationRequest('/test');
        $result = $this->service->getNavigation($request);

        $this->assertInstanceOf(NavigationItem::class, $result);
        $this->assertEquals('Test Title', $result->title);
        $this->assertEquals('', $result->href);
        $this->assertEquals('folder', $result->type);
        $this->assertEquals('', $result->host);
        $this->assertEquals(1, $result->languageId);
        $this->assertEquals(0, $result->hash);
        $this->assertEquals('_self', $result->target);
        $this->assertEquals(0, $result->order);
        $this->assertNull($result->code);
        $this->assertNull($result->folder);
    }

    /**
     * Test that getNavigation returns a NavigationItem even when API returns errors
     */
    public function testGetNavigationReturnsNavigationItemEvenWhenApiReturnsErrors(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(200, ['Content-Type' => 'application/json'], json_encode([
                'entity' => [
                    'code' => null,
                    'folder' => 'test-folder',
                    'host' => 'test-host',
                    'languageId' => 1,
                    'href' => '/test',
                    'title' => 'Test Title',
                    'type' => 'folder',
                    'hash' => 12345,
                    'target' => '_self',
                    'order' => 1,
                ],
                'errors' => ['Error 1', 'Error 2'],
                'messages' => [],
            ]))
        );

        $request = new NavigationRequest('/test');
        $result = $this->service->getNavigation($request);

        $this->assertInstanceOf(NavigationItem::class, $result);
        $this->assertEquals('Test Title', $result->title);
        $this->assertEquals('/test', $result->href);
        $this->assertEquals('folder', $result->type);
    }

    /**
     * Test that getNavigationAsync returns a promise that resolves to a NavigationItem
     */
    public function testGetNavigationAsyncReturnsPromiseThatResolvesToNavigationItem(): void
    {
        // Create a mock response
        $responseData = [
            'entity' => [
                'code' => null,
                'folder' => 'test-folder',
                'host' => 'test-host',
                'languageId' => 1,
                'href' => '/test',
                'title' => 'Test Title',
                'type' => 'folder',
                'hash' => 12345,
                'target' => '_self',
                'order' => 1,
            ],
            'errors' => [],
            'messages' => [],
            'i18nMessagesMap' => [],
            'permissions' => [],
        ];

        $this->mockHandler->append(
            new GuzzleResponse(200, ['Content-Type' => 'application/json'], json_encode($responseData))
        );

        $request = new NavigationRequest('/test');
        $promise = $this->service->getNavigationAsync($request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $result = $promise->wait();
        $this->assertInstanceOf(NavigationItem::class, $result);
        $this->assertEquals('Test Title', $result->title);
        $this->assertEquals('/test', $result->href);
        $this->assertEquals('folder', $result->type);
    }

    /**
     * Test that getNavigationAsync throws an exception when API returns an error
     */
    public function testGetNavigationAsyncThrowsExceptionWhenApiReturnsError(): void
    {
        $this->mockHandler->append(
            new GuzzleResponse(500, ['Content-Type' => 'application/json'], json_encode([
                'message' => 'Internal Server Error',
            ]))
        );

        $request = new NavigationRequest('/test');
        $promise = $this->service->getNavigationAsync($request);

        $this->expectException(ResponseException::class);
        $promise->wait();
    }
}
