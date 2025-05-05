<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model;

use Dotcms\PhpSdk\Model\Language;
use Dotcms\PhpSdk\Model\Layout\Layout;
use Dotcms\PhpSdk\Model\Page;
use Dotcms\PhpSdk\Model\PageAsset;
use Dotcms\PhpSdk\Model\Site;
use Dotcms\PhpSdk\Model\Template;
use Dotcms\PhpSdk\Model\ViewAs;
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
            [], // containers
            new Site('site-id', 'demo.dotcms.com'),
            null, // urlContentMap
            new ViewAs($visitor, $language, 'PREVIEW')
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

        // Check page properties
        $this->assertEquals('page-id', $pageAsset->page->identifier);
        $this->assertEquals('Test Page', $pageAsset->page->title);
        $this->assertEquals('/test-page', $pageAsset->page->pageUrl);
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
    }
}
