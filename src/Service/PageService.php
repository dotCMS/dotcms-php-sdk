<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Service;

use Dotcms\PhpSdk\Exception\ResponseException;
use Dotcms\PhpSdk\Http\HttpClient;
use Dotcms\PhpSdk\Http\Response;
use Dotcms\PhpSdk\Model\Contentlet;
use Dotcms\PhpSdk\Model\Layout\Layout;
use Dotcms\PhpSdk\Model\Page;
use Dotcms\PhpSdk\Model\PageAsset;
use Dotcms\PhpSdk\Model\Site;
use Dotcms\PhpSdk\Model\Template;
use Dotcms\PhpSdk\Model\ViewAs;
use Dotcms\PhpSdk\Model\ViewAs\GeoLocation;
use Dotcms\PhpSdk\Model\ViewAs\UserAgent;
use Dotcms\PhpSdk\Model\ViewAs\Visitor;
use Dotcms\PhpSdk\Request\PageRequest;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Service for interacting with dotCMS Page API
 */
class PageService
{
    /**
     * @param HttpClient $httpClient The HTTP client to use for requests
     */
    public function __construct(
        private readonly HttpClient $httpClient
    ) {
    }

    /**
     * Fetch a page from dotCMS
     *
     * @param PageRequest $request The page request
     * @return PageAsset The complete page asset
     * @throws ResponseException If the response cannot be mapped to a PageAsset
     */
    public function getPage(PageRequest $request): PageAsset
    {
        try {
            $response = $this->httpClient->get(
                $request->buildPath(),
                ['query' => $request->buildQueryParams()]
            );

            $responseData = $response->toArray();

            // Validate the response before mapping
            $this->validateResponse($responseData);

            return $this->mapResponseToPageAsset($responseData);
        } catch (ResponseException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ResponseException(
                'Failed to get page: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Fetch a page from dotCMS asynchronously
     *
     * @param PageRequest $request The page request
     * @return PromiseInterface A promise that resolves to a PageAsset
     */
    public function getPageAsync(PageRequest $request): PromiseInterface
    {
        $promise = $this->httpClient->requestAsync(
            'GET',
            $request->buildPath(),
            ['query' => $request->buildQueryParams()]
        );

        return $promise->then(
            function ($response) {
                // If the response is already a Response object, use it directly
                if ($response instanceof Response) {
                    $dotcmsResponse = $response;
                } else {
                    $dotcmsResponse = new Response($response);
                    // Rewind the body to ensure it can be read
                    $response->getBody()->rewind();
                }

                $responseData = $dotcmsResponse->toArray();

                // Validate the response before mapping
                $this->validateResponse($responseData);

                return $this->mapResponseToPageAsset($responseData);
            },
            function ($reason) {
                if ($reason instanceof ResponseException) {
                    throw $reason;
                }

                throw new ResponseException(
                    'Failed to get page asynchronously: ' . $reason->getMessage(),
                    $reason->getCode(),
                    $reason
                );
            }
        );
    }

    /**
     * Validate the response to ensure it contains all required parts
     *
     * @param Response $response The response to validate
     * @throws ResponseException If the response is missing required parts
     */
    private function validateResponse(array $entity): void
    {
        try {
            $data = $entity['entity'];


            // Ensure $data is an array
            if (! is_array($data)) {
                throw new ResponseException('Response data is not an array');
            }

            // Check if entity.page exists in the response
            if (! isset($data['page'])) {
                throw new ResponseException('Page data not found in response: entity.page is missing');
            }

            // Check if layout exists in the response
            if (! isset($data['layout']) || empty($data['layout'])) {
                throw new ResponseException("This page don't have a layout, maybe because you're using an advanced template");
            }

            // Check if template exists in the response
            if (! isset($data['template']) || empty($data['template'])) {
                throw new ResponseException('Template data not found in response: template is missing');
            }

            // Check if site exists in the response
            if (! isset($data['site']) || empty($data['site'])) {
                throw new ResponseException('Site data not found in response: site is missing');
            }
        } catch (ResponseException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ResponseException(
                'Failed to validate response: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Map a response to a Page object
     *
     * @param Response $response The response to map
     * @return Page The mapped page
     * @throws ResponseException If the response cannot be mapped to a Page
     */
    private function mapResponseToPage(array $entity): Page
    {
        try {
            $data = $entity['entity'];

            // Ensure $data is an array and entity is an array
            if (! is_array($data)) {
                throw new ResponseException('Can\'t map response to Page: Response data is not an array');
            }

            $pageData = $data['page'] ?? [];

            if (! is_array($pageData)) {
                $pageData = [];
            }

            return new Page(
                identifier: $pageData['identifier'] ?? '',
                inode: $pageData['inode'] ?? '',
                title: $pageData['title'] ?? '',
                contentType: $pageData['contentType'] ?? '',
                pageUrl: $pageData['pageUrl'] ?? '',
                live: $pageData['live'] ?? false,
                working: $pageData['working'] ?? false,
                hostName: $pageData['hostName'] ?? '',
                host: $pageData['host'] ?? '',
                additionalProperties: array_diff_key($pageData, array_flip([
                    'identifier', 'inode', 'title', 'contentType', 'pageUrl', 'live', 'working', 'hostName', 'host',
                ]))
            );
        } catch (\Throwable $e) {
            throw new ResponseException(
                'Failed to map response to Page: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Map a response to a PageAsset object
     *
     * @param Response $response The response to map
     * @return PageAsset The mapped page asset
     * @throws ResponseException If the response cannot be mapped to a PageAsset
     */
    private function mapResponseToPageAsset(array $entity): PageAsset
    {
        try {
            $data = $entity['entity'];

            // Extract page data
            $page = $this->mapResponseToPage($entity);

            // Extract layout data
            $layoutData = $data['layout'] ?? [];
            if (! is_array($layoutData)) {
                $layoutData = [];
            }

            $layout = new Layout(
                width: $layoutData['width'] ?? null,
                title: $layoutData['title'] ?? '',
                header: $layoutData['header'] ?? true,
                footer: $layoutData['footer'] ?? true,
                body: $layoutData['body'] ?? ['rows' => []],
                sidebar: $layoutData['sidebar'] ?? [
                    'containers' => [],
                    'location' => '',
                    'width' => 'small',
                    'widthPercent' => 20,
                    'preview' => false,
                ],
                version: $layoutData['version'] ?? 1
            );

            // Extract template data
            $templateData = $data['template'] ?? [];
            if (! is_array($templateData)) {
                $templateData = [];
            }

            $template = new Template(
                identifier: $templateData['identifier'] ?? '',
                title: $templateData['title'] ?? '',
                drawed: $templateData['drawed'] ?? false,
                inode: $templateData['inode'] ?? '',
                friendlyName: $templateData['friendlyName'] ?? '',
                header: $templateData['header'] ?? true,
                footer: $templateData['footer'] ?? true,
                working: $templateData['working'] ?? false,
                live: $templateData['live'] ?? false,
                additionalProperties: array_diff_key($templateData, array_flip([
                    'identifier', 'title', 'drawed', 'inode', 'friendlyName', 'header', 'footer', 'working', 'live',
                ]))
            );

            // Extract site data
            $siteData = $data['site'] ?? [];
            if (! is_array($siteData)) {
                $siteData = [];
            }

            $site = new Site(
                identifier: $siteData['identifier'] ?? '',
                hostname: $siteData['hostname'] ?? '',
                inode: $siteData['inode'] ?? '',
                working: $siteData['working'] ?? false,
                folder: $siteData['folder'] ?? '',
                locked: $siteData['locked'] ?? false,
                archived: $siteData['archived'] ?? false,
                live: $siteData['live'] ?? false,
                additionalProperties: array_diff_key($siteData, array_flip([
                    'identifier', 'hostname', 'inode', 'working', 'folder', 'locked', 'archived', 'live',
                ]))
            );

            // Extract containers
            $containers = $data['containers'] ?? [];
            if (! is_array($containers)) {
                $containers = [];
            }

            // Extract urlContentMap if available
            $urlContentMapData = $data['urlContentMap'] ?? null;
            $urlContentMap = null;
            if (is_array($urlContentMapData)) {
                $urlContentMap = new Contentlet(
                    identifier: $urlContentMapData['identifier'] ?? '',
                    inode: $urlContentMapData['inode'] ?? '',
                    title: $urlContentMapData['title'] ?? '',
                    contentType: $urlContentMapData['contentType'] ?? '',
                    additionalProperties: array_diff_key($urlContentMapData, array_flip([
                        'identifier', 'inode', 'title', 'contentType',
                    ]))
                );
            }

            // Extract viewAs data
            $viewAsData = $data['viewAs'] ?? [];
            if (! is_array($viewAsData)) {
                $viewAsData = [];
            }

            $visitorData = $viewAsData['visitor'] ?? [];
            if (! is_array($visitorData)) {
                $visitorData = [];
            }

            // Create UserAgent
            $userAgentData = $visitorData['userAgent'] ?? [];
            if (! is_array($userAgentData)) {
                $userAgentData = [];
            }

            $userAgent = new UserAgent(
                browser: $userAgentData['browser'] ?? '',
                version: $userAgentData['version'] ?? '',
                os: $userAgentData['os'] ?? '',
                mobile: $userAgentData['mobile'] ?? false
            );

            // Create GeoLocation
            $geoData = $visitorData['geo'] ?? [];
            if (! is_array($geoData)) {
                $geoData = [];
            }

            $geoLocation = new GeoLocation(
                city: $geoData['city'] ?? '',
                country: $geoData['country'] ?? '',
                countryCode: $geoData['countryCode'] ?? '',
                latitude: $geoData['latitude'] ?? '',
                longitude: $geoData['longitude'] ?? '',
                region: $geoData['region'] ?? ''
            );

            // Create Visitor
            $visitor = new Visitor(
                tags: $visitorData['tags'] ?? [],
                device: $visitorData['device'] ?? '',
                isNew: $visitorData['isNew'] ?? false,
                userAgent: $userAgent,
                referer: $visitorData['referer'] ?? '',
                dmid: $visitorData['dmid'] ?? '',
                geo: $geoLocation,
                personas: $visitorData['personas'] ?? []
            );

            // Create ViewAs
            $viewAs = new ViewAs(
                visitor: $visitor,
                language: $viewAsData['language'] ?? [],
                mode: $viewAsData['mode'] ?? 'LIVE'
            );

            return new PageAsset(
                layout: $layout,
                template: $template,
                page: $page,
                containers: $containers,
                site: $site,
                urlContentMap: $urlContentMap,
                viewAs: $viewAs
            );
        } catch (\Throwable $e) {
            throw new ResponseException(
                'Failed to map response to PageAsset: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}
