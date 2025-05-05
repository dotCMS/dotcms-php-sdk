<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\Page;

use Dotcms\PhpSdk\Model\Container\ContainerPage;
use Dotcms\PhpSdk\Model\Content\Contentlet;
use Dotcms\PhpSdk\Model\Layout\Layout;
use Dotcms\PhpSdk\Model\Page\Page;
use Dotcms\PhpSdk\Model\Page\PageAsset;
use Dotcms\PhpSdk\Model\Page\Template;
use Dotcms\PhpSdk\Model\Page\VanityUrl;
use Dotcms\PhpSdk\Model\Site\Site;
use Dotcms\PhpSdk\Model\View\ViewAs;
use PHPUnit\Framework\TestCase;

class PageAssetTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        $layout = $this->createMock(Layout::class);
        $template = $this->createMock(Template::class);
        $page = $this->createMock(Page::class);
        $containers = ['test-container' => $this->createMock(ContainerPage::class)];
        $site = $this->createMock(Site::class);
        $urlContentMap = $this->createMock(Contentlet::class);
        $viewAs = $this->createMock(ViewAs::class);
        $vanityUrl = $this->createMock(VanityUrl::class);

        $pageAsset = new PageAsset(
            layout: $layout,
            template: $template,
            page: $page,
            containers: $containers,
            site: $site,
            urlContentMap: $urlContentMap,
            viewAs: $viewAs,
            vanityUrl: $vanityUrl
        );

        $this->assertSame($layout, $pageAsset->layout);
        $this->assertSame($template, $pageAsset->template);
        $this->assertSame($page, $pageAsset->page);
        $this->assertSame($containers, $pageAsset->containers);
        $this->assertSame($site, $pageAsset->site);
        $this->assertSame($urlContentMap, $pageAsset->urlContentMap);
        $this->assertSame($viewAs, $pageAsset->viewAs);
        $this->assertSame($vanityUrl, $pageAsset->vanityUrl);
    }

    public function testConstructorWithNullVanityUrl(): void
    {
        $layout = $this->createMock(Layout::class);
        $template = $this->createMock(Template::class);
        $page = $this->createMock(Page::class);
        $containers = ['test-container' => $this->createMock(ContainerPage::class)];
        $site = $this->createMock(Site::class);
        $urlContentMap = $this->createMock(Contentlet::class);
        $viewAs = $this->createMock(ViewAs::class);

        $pageAsset = new PageAsset(
            layout: $layout,
            template: $template,
            page: $page,
            containers: $containers,
            site: $site,
            urlContentMap: $urlContentMap,
            viewAs: $viewAs
        );

        $this->assertSame($layout, $pageAsset->layout);
        $this->assertSame($template, $pageAsset->template);
        $this->assertSame($page, $pageAsset->page);
        $this->assertSame($containers, $pageAsset->containers);
        $this->assertSame($site, $pageAsset->site);
        $this->assertSame($urlContentMap, $pageAsset->urlContentMap);
        $this->assertSame($viewAs, $pageAsset->viewAs);
        $this->assertNull($pageAsset->vanityUrl);
    }
}
