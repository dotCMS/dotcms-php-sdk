<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk;

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\Http\HttpClient;
use Dotcms\PhpSdk\Model\NavigationItem;
use Dotcms\PhpSdk\Model\PageAsset;
use Dotcms\PhpSdk\Request\NavigationRequest;
use Dotcms\PhpSdk\Request\PageRequest;
use Dotcms\PhpSdk\Service\NavigationService;
use Dotcms\PhpSdk\Service\PageService;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Main client for interacting with dotCMS API
 */
class DotCMSClient
{
    private readonly HttpClient $httpClient;

    private readonly PageService $pageService;

    private readonly NavigationService $navigationService;

    /**
     * Create a new DotCMSClient instance
     *
     * @param Config $config The configuration for the client
     */
    public function __construct(Config $config)
    {
        $this->httpClient = new HttpClient($config);
        $this->pageService = new PageService($this->httpClient);
        $this->navigationService = new NavigationService($this->httpClient);
    }

    /**
     * Fetch a page from dotCMS
     *
     * @param PageRequest $request The page request
     * @return PageAsset The complete page asset
     */
    public function getPage(PageRequest $request): PageAsset
    {
        return $this->pageService->getPage($request);
    }

    /**
     * Fetch a page from dotCMS asynchronously
     *
     * @param PageRequest $request The page request
     * @return PromiseInterface A promise that resolves to a PageAsset
     */
    public function getPageAsync(PageRequest $request): PromiseInterface
    {
        return $this->pageService->getPageAsync($request);
    }

    /**
     * Create a new page request
     *
     * @param string $pagePath The path to the page
     * @param string $format The format of the response (json or render)
     * @return PageRequest The page request
     */
    public function createPageRequest(string $pagePath, string $format = 'json'): PageRequest
    {
        return new PageRequest($pagePath, $format);
    }

    /**
     * Fetch navigation items from dotCMS
     *
     * @param NavigationRequest $request The navigation request
     * @return NavigationItem The navigation item with optional children
     */
    public function getNavigation(NavigationRequest $request): NavigationItem
    {
        return $this->navigationService->getNavigation($request);
    }

    /**
     * Fetch navigation items from dotCMS asynchronously
     *
     * @param NavigationRequest $request The navigation request
     * @return PromiseInterface A promise that resolves to a NavigationItem
     */
    public function getNavigationAsync(NavigationRequest $request): PromiseInterface
    {
        return $this->navigationService->getNavigationAsync($request);
    }

    /**
     * Create a new navigation request
     *
     * @param string $path The root path to begin traversing the folder tree
     * @param int $depth The depth of the folder tree to return
     * @param int $languageId The language ID of content to return
     * @return NavigationRequest The navigation request
     */
    public function createNavigationRequest(string $path = '/', int $depth = 1, int $languageId = 1): NavigationRequest
    {
        return new NavigationRequest($path, $depth, $languageId);
    }
}
