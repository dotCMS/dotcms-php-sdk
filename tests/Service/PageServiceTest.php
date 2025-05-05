<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Service;

use Dotcms\PhpSdk\Exception\ResponseException;
use Dotcms\PhpSdk\Model\Core\Language;
use Dotcms\PhpSdk\Model\Layout\Body;
use Dotcms\PhpSdk\Model\Layout\Column;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;
use Dotcms\PhpSdk\Model\Layout\Layout;
use Dotcms\PhpSdk\Model\Layout\Row;
use Dotcms\PhpSdk\Model\Page\PageAsset;
use Dotcms\PhpSdk\Model\View\ViewAs;
use Dotcms\PhpSdk\Model\ViewAs\Visitor;
use Dotcms\PhpSdk\Request\PageRequest;
use Dotcms\PhpSdk\Service\PageService;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use PHPUnit\Framework\TestCase;
use Dotcms\PhpSdk\Http\HttpClient;
use Dotcms\PhpSdk\Http\Response;
use Dotcms\PhpSdk\Model\Container\Container;
use Dotcms\PhpSdk\Model\Container\ContainerPage;
use Dotcms\PhpSdk\Model\Container\ContainerStructure;
use Dotcms\PhpSdk\Model\Content\Contentlet;
use PHPUnit\Framework\MockObject\MockObject;

class PageServiceTest extends TestCase
{
    private MockHandler $mockHandler;

    private HttpClient&MockObject $httpClient;

    private PageService $pageService;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();

        // Create our test-specific HttpClient with the mock handler
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->pageService = new PageService($this->httpClient);
    }

    public function testGetPage(): void
    {
        $responseData = [
            'entity' => [
                'page' => [
                    'identifier' => 'about-us',
                    'title' => 'About Us',
                    'pageUrl' => 'about-us',
                    'hostName' => 'demo.dotcms.com',
                    'host' => '48190c8c-42c4-46af-8d1a-0cd5db894797',
                    'contentType' => 'WebPageContent',
                    'live' => true,
                    'working' => true
                ],
                'layout' => [
                    'title' => 'Default Layout',
                    'version' => 1,
                    'header' => true,
                    'footer' => true,
                    'body' => [
                        'rows' => []
                    ]
                ],
                'template' => [
                    'identifier' => 'c541abb1-69b3-4bc5-8430-5e09e5239cc8',
                    'title' => 'Default',
                    'drawed' => true,
                    'header' => true,
                    'footer' => true,
                    'live' => true,
                    'working' => true
                ],
                'site' => [
                    'identifier' => '48190c8c-42c4-46af-8d1a-0cd5db894797',
                    'hostname' => 'demo.dotcms.com',
                    'live' => true,
                    'working' => true,
                    'locked' => false,
                    'archived' => false
                ],
                'viewAs' => [
                    'mode' => 'LIVE',
                    'variantId' => 'variant-123',
                    'visitor' => [
                        'isNew' => true
                    ],
                    'language' => [
                        'id' => 1,
                        'languageCode' => 'en'
                    ]
                ]
            ]
        ];

        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn($responseData);

        $this->httpClient->expects($this->once())
            ->method('get')
            ->willReturn($response);

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

        // Test viewAs
        $this->assertInstanceOf(ViewAs::class, $page->viewAs);
        $this->assertEquals('LIVE', $page->viewAs->mode);
        $this->assertEquals('variant-123', $page->viewAs->variantId);
        $this->assertInstanceOf(Visitor::class, $page->viewAs->visitor);
        $this->assertInstanceOf(Language::class, $page->viewAs->language);
    }

    public function testGetPageAsync(): void
    {
        $responseData = [
            'entity' => [
                'page' => [
                    'identifier' => 'about-us',
                    'title' => 'About Us',
                    'pageUrl' => 'about-us'
                ],
                'layout' => [
                    'title' => 'Default Layout',
                    'version' => 1,
                    'header' => true,
                    'footer' => true,
                    'body' => [
                        'rows' => []
                    ]
                ],
                'template' => [
                    'identifier' => 'test-template',
                    'title' => 'Test Template'
                ],
                'site' => [
                    'identifier' => 'test-site',
                    'hostname' => 'test.com'
                ],
                'viewAs' => [
                    'mode' => 'LIVE',
                    'visitor' => [
                        'isNew' => true
                    ],
                    'language' => [
                        'id' => 1,
                        'languageCode' => 'en'
                    ]
                ]
            ]
        ];

        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn($responseData);

        $this->httpClient->expects($this->once())
            ->method('requestAsync')
            ->willReturn(new \GuzzleHttp\Promise\FulfilledPromise($response));

        $request = new PageRequest('/about-us');
        $promise = $this->pageService->getPageAsync($request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $page = $promise->wait();
        $this->assertInstanceOf(PageAsset::class, $page);
        $this->assertEquals('about-us', $page->page->identifier);
        $this->assertEquals('About Us', $page->page->title);
    }

    public function testGetPageWithInvalidResponse(): void
    {
        $responseData = [];

        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn($responseData);

        $this->httpClient->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $request = new PageRequest('/about-us');

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Entity data not found in response');

        $this->pageService->getPage($request);
    }

    public function testGetPageWithEmptyPageData(): void
    {
        $responseData = [
            'entity' => [
                'page' => [],
                'layout' => [
                    'title' => 'Test Layout',
                    'body' => [
                        'rows' => []
                    ]
                ],
                'template' => [
                    'identifier' => 'test-template',
                    'title' => 'Test Template'
                ],
                'site' => [
                    'identifier' => 'test-site',
                    'hostname' => 'test.com'
                ],
                'viewAs' => [
                    'mode' => 'LIVE',
                    'visitor' => [
                        'isNew' => true
                    ],
                    'language' => [
                        'id' => 1,
                        'languageCode' => 'en'
                    ]
                ]
            ]
        ];

        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn($responseData);

        $this->httpClient->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $request = new PageRequest('/about-us');
        $page = $this->pageService->getPage($request);

        $this->assertInstanceOf(PageAsset::class, $page);
        $this->assertEquals('', $page->page->identifier);
        $this->assertEquals('', $page->page->title);
    }

    public function testGetPageWithMissingLayout(): void
    {
        $responseData = [
            'entity' => [
                'page' => [
                    'identifier' => 'test-page'
                ],
                'template' => [
                    'identifier' => 'test-template'
                ],
                'site' => [
                    'identifier' => 'test-site'
                ],
                'viewAs' => [
                    'mode' => 'LIVE',
                    'visitor' => [
                        'isNew' => true
                    ],
                    'language' => [
                        'id' => 1,
                        'languageCode' => 'en'
                    ]
                ]
            ]
        ];

        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn($responseData);

        $this->httpClient->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $request = new PageRequest('/about-us');

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage("This page don't have a layout, maybe because you're using an advanced template");

        $this->pageService->getPage($request);
    }

    public function testGetPageWithMissingTemplate(): void
    {
        $responseData = [
            'entity' => [
                'page' => [
                    'identifier' => 'test-page'
                ],
                'layout' => [
                    'title' => 'Test Layout',
                    'body' => [
                        'rows' => []
                    ]
                ],
                'site' => [
                    'identifier' => 'test-site'
                ],
                'viewAs' => [
                    'mode' => 'LIVE',
                    'visitor' => [
                        'isNew' => true
                    ],
                    'language' => [
                        'id' => 1,
                        'languageCode' => 'en'
                    ]
                ]
            ]
        ];

        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn($responseData);

        $this->httpClient->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $request = new PageRequest('/about-us');

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Template data not found in response: entity.template is missing');

        $this->pageService->getPage($request);
    }

    public function testGetPageWithMissingSite(): void
    {
        $responseData = [
            'entity' => [
                'page' => [
                    'identifier' => 'test-page'
                ],
                'layout' => [
                    'title' => 'Test Layout',
                    'body' => [
                        'rows' => []
                    ]
                ],
                'template' => [
                    'identifier' => 'test-template'
                ],
                'viewAs' => [
                    'mode' => 'LIVE',
                    'visitor' => [
                        'isNew' => true
                    ],
                    'language' => [
                        'id' => 1,
                        'languageCode' => 'en'
                    ]
                ]
            ]
        ];

        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn($responseData);

        $this->httpClient->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $request = new PageRequest('/about-us');

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Site data not found in response: entity.site is missing');

        $this->pageService->getPage($request);
    }

    public function testGetPageWithDifferentLayoutFormat(): void
    {
        $responseData = [
            'entity' => [
                'page' => [
                    'identifier' => 'test-page'
                ],
                'layout' => [
                    'title' => 'Test Layout',
                    'body' => [
                        'rows' => [
                            [
                                'columns' => [
                                    [
                                        'width' => 12,
                                        'containers' => []
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'template' => [
                    'identifier' => 'test-template',
                    'title' => 'Test Template'
                ],
                'site' => [
                    'identifier' => 'test-site',
                    'hostname' => 'test.com'
                ],
                'viewAs' => [
                    'mode' => 'LIVE',
                    'visitor' => [
                        'isNew' => true
                    ],
                    'language' => [
                        'id' => 1,
                        'languageCode' => 'en'
                    ]
                ]
            ]
        ];

        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn($responseData);

        $this->httpClient->expects($this->once())
            ->method('get')
            ->willReturn($response);

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

    public function testMapResponseToPageAssetWithContainers(): void
    {
        // Mock response data
        $responseData = [
            'entity' => [
                'page' => [
                    'identifier' => 'test-page',
                    'title' => 'Test Page',
                    'pageUrl' => '/test-page'
                ],
                'layout' => [
                    'body' => [
                        'rows' => [
                            [
                                'columns' => [
                                    [
                                        'containers' => [
                                            [
                                                'identifier' => 'test-container',
                                                'uuid' => 'test-uuid',
                                                'historyUUIDs' => ['history-1']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'containers' => [
                    'test-container' => [
                        'container' => [
                            'identifier' => 'test-container',
                            'inode' => 'test-inode',
                            'title' => 'Test Container',
                            'path' => '/test-container',
                            'maxContentlets' => 5,
                            'parentPermissionable' => [
                                'variantId' => 123
                            ]
                        ],
                        'containerStructures' => [
                            [
                                'id' => 'structure-1',
                                'structureId' => 'struct-1',
                                'containerInode' => 'test-inode',
                                'containerId' => 'test-container',
                                'code' => '<div>Test</div>',
                                'contentTypeVar' => 'test-type'
                            ]
                        ],
                        'contentlets' => [
                            'uuid-test-uuid' => [
                                [
                                    'identifier' => 'content-1',
                                    'inode' => 'content-inode-1',
                                    'title' => 'Test Content',
                                    'contentType' => 'test-type'
                                ]
                            ]
                        ]
                    ]
                ],
                'template' => [
                    'identifier' => 'test-template',
                    'title' => 'Test Template'
                ],
                'site' => [
                    'identifier' => 'test-site',
                    'hostname' => 'test.com'
                ],
                'viewAs' => [
                    'mode' => 'LIVE',
                    'visitor' => [
                        'isNew' => true
                    ],
                    'language' => [
                        'id' => 1,
                        'languageCode' => 'en'
                    ]
                ]
            ]
        ];

        // Mock HTTP client response
        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn($responseData);

        $this->httpClient->expects($this->once())
            ->method('get')
            ->willReturn($response);

        // Create page request
        $request = new PageRequest('test-page');

        // Get page asset
        $pageAsset = $this->pageService->getPage($request);

        // Assert page asset structure
        $this->assertInstanceOf(PageAsset::class, $pageAsset);
        $this->assertArrayHasKey('test-container', $pageAsset->containers);

        // Get container page
        $containerPage = $pageAsset->containers['test-container'];
        $this->assertInstanceOf(ContainerPage::class, $containerPage);

        // Assert container properties
        $this->assertInstanceOf(Container::class, $containerPage->container);
        $this->assertEquals('test-container', $containerPage->container->identifier);
        $this->assertEquals(5, $containerPage->container->maxContentlets);

        // Assert container structures
        $this->assertCount(1, $containerPage->containerStructures);
        $this->assertInstanceOf(ContainerStructure::class, $containerPage->containerStructures[0]);
        $this->assertEquals('test-type', $containerPage->containerStructures[0]->contentTypeVar);

        // Assert contentlets
        $this->assertArrayHasKey('uuid-test-uuid', $containerPage->contentlets);
        $this->assertCount(1, $containerPage->contentlets['uuid-test-uuid']);
        $this->assertInstanceOf(Contentlet::class, $containerPage->contentlets['uuid-test-uuid'][0]);
        $this->assertEquals('content-1', $containerPage->contentlets['uuid-test-uuid'][0]->identifier);

        // Get container ref from layout
        $containerRef = $pageAsset->layout->body->rows[0]->columns[0]->containers[0];
        $this->assertInstanceOf(ContainerRef::class, $containerRef);

        // Assert container ref properties
        $this->assertEquals('test-container', $containerRef->identifier);
        $this->assertEquals('test-uuid', $containerRef->uuid);
        $this->assertEquals(['history-1'], $containerRef->historyUUIDs);
        $this->assertEquals(5, $containerRef->maxContentlets);
        $this->assertEquals(123, $containerRef->variantId);
        $this->assertEquals('test-type', $containerRef->acceptTypes);
        $this->assertCount(1, $containerRef->contentlets);
        $this->assertInstanceOf(Contentlet::class, $containerRef->contentlets[0]);
        $this->assertEquals('content-1', $containerRef->contentlets[0]->identifier);
    }

    public function testMapResponseToPageAssetWithMissingContainer(): void
    {
        // Mock response data with missing container
        $responseData = [
            'entity' => [
                'page' => [
                    'identifier' => 'test-page',
                    'title' => 'Test Page',
                    'pageUrl' => '/test-page'
                ],
                'layout' => [
                    'body' => [
                        'rows' => [
                            [
                                'columns' => [
                                    [
                                        'containers' => [
                                            [
                                                'identifier' => 'missing-container',
                                                'uuid' => 'test-uuid',
                                                'historyUUIDs' => ['history-1']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'containers' => [],
                'template' => [
                    'identifier' => 'test-template',
                    'title' => 'Test Template'
                ],
                'site' => [
                    'identifier' => 'test-site',
                    'hostname' => 'test.com'
                ],
                'viewAs' => [
                    'mode' => 'LIVE',
                    'visitor' => [
                        'isNew' => true
                    ],
                    'language' => [
                        'id' => 1,
                        'languageCode' => 'en'
                    ]
                ]
            ]
        ];

        // Mock HTTP client response
        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn($responseData);

        $this->httpClient->expects($this->once())
            ->method('get')
            ->willReturn($response);

        // Create page request
        $request = new PageRequest('test-page');

        // Get page asset
        $pageAsset = $this->pageService->getPage($request);

        // Get container ref from layout
        $containerRef = $pageAsset->layout->body->rows[0]->columns[0]->containers[0];
        $this->assertInstanceOf(ContainerRef::class, $containerRef);

        // Assert container ref properties for missing container
        $this->assertEquals('missing-container', $containerRef->identifier);
        $this->assertEquals('test-uuid', $containerRef->uuid);
        $this->assertEquals(['history-1'], $containerRef->historyUUIDs);
        $this->assertEquals(0, $containerRef->maxContentlets);
        $this->assertNull($containerRef->variantId);
        $this->assertEquals('', $containerRef->acceptTypes);
        $this->assertEmpty($containerRef->contentlets);
    }
}
