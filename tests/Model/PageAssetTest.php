<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model;

use Dotcms\PhpSdk\Model\Container\Container;
use Dotcms\PhpSdk\Model\Container\ContainerPage;
use Dotcms\PhpSdk\Model\Container\ContainerStructure;
use Dotcms\PhpSdk\Model\Content\Contentlet;
use Dotcms\PhpSdk\Model\Core\Language;
use Dotcms\PhpSdk\Model\Layout\Layout;
use Dotcms\PhpSdk\Model\Page\Page;
use Dotcms\PhpSdk\Model\Page\PageAsset;
use Dotcms\PhpSdk\Model\Page\Template;
use Dotcms\PhpSdk\Model\Site\Site;
use Dotcms\PhpSdk\Model\View\ViewAs;
use Dotcms\PhpSdk\Model\ViewAs\GeoLocation;
use Dotcms\PhpSdk\Model\ViewAs\UserAgent;
use Dotcms\PhpSdk\Model\ViewAs\Visitor;
use PHPUnit\Framework\TestCase;

class PageAssetTest extends TestCase
{
    private function createPageAsset(): PageAsset
    {
        // Create a basic visitor
        $visitor = new Visitor(
            [], // tags
            'desktop', // device
            true, // isNew
            new UserAgent('Chrome', '120.0', 'Windows', false), // userAgent
            'https://example.com', // referer
            'test-dmid', // dmid
            new GeoLocation('Miami', 'United States', 'US', 25.7743, -80.1937, 'Florida'), // geo
            [] // personas
        );

        // Create a basic language
        $language = new Language(
            id: 1,
            languageCode: 'en',
            countryCode: 'US',
            language: 'English',
            country: 'United States',
            isoCode: 'en-us'
        );

        // Create container structure
        $containerStructure = new ContainerStructure(
            id: 'structure-123',
            structureId: 'struct-456',
            containerInode: 'inode-789',
            containerId: 'container-101',
            code: 'test-container',
            contentTypeVar: 'content-type-var'
        );

        // Create container
        $container = new Container(
            identifier: 'container-101',
            inode: 'inode-789',
            title: 'Test Container',
            path: '/test-container',
            working: true,
            live: true
        );

        // Create contentlet
        $contentlet = new Contentlet(
            identifier: 'content-123',
            inode: 'content-inode-456',
            title: 'Test Content',
            contentType: 'content-type'
        );

        // Create container page
        $containerPage = new ContainerPage(
            container: $container,
            containerStructures: [$containerStructure],
            rendered: ['uuid-123' => '<div>Test Content</div>'],
            contentlets: ['uuid-123' => [$contentlet]]
        );

        return new PageAsset(
            new Layout(),
            new Template('template-id', 'Test Template', true), // drawed=true
            new Page(
                'page-id', // identifier
                'page-inode', // inode
                'Test Page', // title
                'content', // contentType
                '/test-page', // pageUrl
                true, // live
                true, // working
                'demo.dotcms.com', // hostName
                'site-id' // host
            ),
            [$containerPage], // containers
            new Site('site-id', 'demo.dotcms.com'),
            null, // urlContentMap
            new ViewAs($visitor, $language, 'PREVIEW', 'variant-123')
        );
    }

    public function testConstructorAndBasicProperties(): void
    {
        $pageAsset = $this->createPageAsset();

        $this->assertInstanceOf(Layout::class, $pageAsset->layout);
        $this->assertInstanceOf(Template::class, $pageAsset->template);
        $this->assertInstanceOf(Page::class, $pageAsset->page);
        $this->assertIsArray($pageAsset->containers);
        $this->assertInstanceOf(Site::class, $pageAsset->site);
        $this->assertNull($pageAsset->urlContentMap);
        $this->assertInstanceOf(ViewAs::class, $pageAsset->viewAs);
        $this->assertEquals('variant-123', $pageAsset->viewAs->variantId);

        // Check page properties
        $this->assertEquals('page-id', $pageAsset->page->identifier);
        $this->assertEquals('Test Page', $pageAsset->page->title);
        $this->assertEquals('/test-page', $pageAsset->page->pageUrl);

        // Check container page properties
        $this->assertCount(1, $pageAsset->containers);
        $containerPage = $pageAsset->containers[0];
        $this->assertInstanceOf(ContainerPage::class, $containerPage);

        // Check container
        $this->assertEquals('container-101', $containerPage->container->identifier);
        $this->assertEquals('Test Container', $containerPage->container->title);
        $this->assertTrue($containerPage->container->working);
        $this->assertTrue($containerPage->container->live);

        // Check container structure
        $this->assertCount(1, $containerPage->containerStructures);
        $structure = $containerPage->containerStructures[0];
        $this->assertEquals('structure-123', $structure->id);
        $this->assertEquals('test-container', $structure->code);

        // Check rendered content
        $this->assertArrayHasKey('uuid-123', $containerPage->rendered);
        $this->assertEquals('<div>Test Content</div>', $containerPage->rendered['uuid-123']);

        // Check contentlets
        $this->assertArrayHasKey('uuid-123', $containerPage->contentlets);
        $this->assertCount(1, $containerPage->contentlets['uuid-123']);
        $contentlet = $containerPage->contentlets['uuid-123'][0];
        $this->assertEquals('content-123', $contentlet->identifier);
        $this->assertEquals('Test Content', $contentlet->title);
    }

    public function testJsonSerialize(): void
    {
        $pageAsset = $this->createPageAsset();

        // Check that properties exist and are of correct type
        $this->assertInstanceOf(Layout::class, $pageAsset->layout);
        $this->assertInstanceOf(Template::class, $pageAsset->template);
        $this->assertInstanceOf(Page::class, $pageAsset->page);
        $this->assertIsArray($pageAsset->containers);
        $this->assertInstanceOf(Site::class, $pageAsset->site);
        $this->assertNull($pageAsset->urlContentMap);
        $this->assertInstanceOf(ViewAs::class, $pageAsset->viewAs);

        // Test that nested objects have the expected properties
        $this->assertEquals('template-id', $pageAsset->template->identifier);
        $this->assertEquals('Test Template', $pageAsset->template->title);

        $this->assertEquals('page-id', $pageAsset->page->identifier);
        $this->assertEquals('Test Page', $pageAsset->page->title);
        $this->assertEquals('/test-page', $pageAsset->page->pageUrl);

        $this->assertEquals('site-id', $pageAsset->site->identifier);
        $this->assertEquals('demo.dotcms.com', $pageAsset->site->hostname);

        // Test container page properties
        $this->assertCount(1, $pageAsset->containers);
        $containerPage = $pageAsset->containers[0];
        $this->assertInstanceOf(ContainerPage::class, $containerPage);

        // Check container
        $this->assertEquals('container-101', $containerPage->container->identifier);
        $this->assertEquals('Test Container', $containerPage->container->title);

        // Check container structure
        $this->assertCount(1, $containerPage->containerStructures);
        $structure = $containerPage->containerStructures[0];
        $this->assertEquals('structure-123', $structure->id);
        $this->assertEquals('test-container', $structure->code);

        // Check rendered content
        $this->assertArrayHasKey('uuid-123', $containerPage->rendered);
        $this->assertEquals('<div>Test Content</div>', $containerPage->rendered['uuid-123']);

        // Check contentlets
        $this->assertArrayHasKey('uuid-123', $containerPage->contentlets);
        $this->assertCount(1, $containerPage->contentlets['uuid-123']);
        $contentlet = $containerPage->contentlets['uuid-123'][0];
        $this->assertEquals('content-123', $contentlet->identifier);
        $this->assertEquals('Test Content', $contentlet->title);
    }
}
