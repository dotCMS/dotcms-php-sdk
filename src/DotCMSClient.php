<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk;

use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\Http\HttpClient;
use Dotcms\PhpSdk\Model\PageAsset;
use Dotcms\PhpSdk\Request\PageRequest;
use Dotcms\PhpSdk\Service\PageService;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Main client for interacting with dotCMS API
 */
class DotCMSClient
{
    private readonly HttpClient $httpClient;

    private readonly PageService $pageService;

    /**
     * Create a new DotCMSClient instance
     *
     * @param Config $config The configuration for the client
     */
    public function __construct(Config $config)
    {
        $this->httpClient = new HttpClient($config);
        $this->pageService = new PageService($this->httpClient);
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
}
