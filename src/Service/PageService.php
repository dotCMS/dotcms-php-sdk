<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Service;

use Dotcms\PhpSdk\Exception\ResponseException;
use Dotcms\PhpSdk\Http\HttpClient;
use Dotcms\PhpSdk\Http\Response;
use Dotcms\PhpSdk\Model\Container\Container;
use Dotcms\PhpSdk\Model\Container\ContainerPage;
use Dotcms\PhpSdk\Model\Container\ContainerStructure;
use Dotcms\PhpSdk\Model\Content\Contentlet;
use Dotcms\PhpSdk\Model\Core\Language;
use Dotcms\PhpSdk\Model\Layout\Body;
use Dotcms\PhpSdk\Model\Layout\Column;
use Dotcms\PhpSdk\Model\Layout\ContainerRef;
use Dotcms\PhpSdk\Model\Layout\Layout;
use Dotcms\PhpSdk\Model\Layout\Row;
use Dotcms\PhpSdk\Model\Page\Page;
use Dotcms\PhpSdk\Model\Page\PageAsset;
use Dotcms\PhpSdk\Model\Page\Template;
use Dotcms\PhpSdk\Model\Site\Site;
use Dotcms\PhpSdk\Model\View\ViewAs;
use Dotcms\PhpSdk\Model\ViewAs\GeoLocation;
use Dotcms\PhpSdk\Model\ViewAs\UserAgent;
use Dotcms\PhpSdk\Model\ViewAs\Visitor;
use Dotcms\PhpSdk\Request\PageRequest;
use Dotcms\PhpSdk\Utils\DotCmsHelper;
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
     * @param array<string, mixed> $response The response to validate
     * @throws ResponseException If the response is missing required parts
     */
    private function validateResponse(array $response): void
    {
        try {
            // Check if entity exists in the response
            if (! isset($response['entity']) || ! is_array($response['entity'])) {
                throw new ResponseException('Entity data not found in response');
            }

            $entity = $response['entity'];

            // Check if entity.page exists in the response
            if (! isset($entity['page'])) {
                throw new ResponseException('Page data not found in response: entity.page is missing');
            }

            // Check if entity.layout exists in the response
            if (! isset($entity['layout']) || empty($entity['layout'])) {
                throw new ResponseException("This page don't have a layout, maybe because you're using an advanced template");
            }

            // Check if entity.template exists in the response
            if (! isset($entity['template']) || empty($entity['template'])) {
                throw new ResponseException('Template data not found in response: entity.template is missing');
            }

            // Check if entity.site exists in the response
            if (! isset($entity['site']) || empty($entity['site'])) {
                throw new ResponseException('Site data not found in response: entity.site is missing');
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
     * @param array<string, mixed> $response The response to map
     * @return Page The mapped page
     * @throws ResponseException If the response cannot be mapped to a Page
     */
    private function mapResponseToPage(array $response): Page
    {
        try {
            // Ensure entity is an array
            if (! isset($response['entity']) || ! is_array($response['entity'])) {
                throw new ResponseException('Can\'t map response to Page: Response entity is not an array');
            }

            $pageData = $response['entity']['page'] ?? [];

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
                    'identifier',
                    'inode',
                    'title',
                    'contentType',
                    'pageUrl',
                    'live',
                    'working',
                    'hostName',
                    'host',
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
     * @param array<string, mixed> $response The response to map
     * @return PageAsset The mapped page asset
     * @throws ResponseException If the response cannot be mapped to a PageAsset
     */
    private function mapResponseToPageAsset(array $response): PageAsset
    {
        try {
            // Ensure entity exists and is an array
            if (! isset($response['entity']) || ! is_array($response['entity'])) {
                throw new ResponseException('Entity data not found in response');
            }

            $entity = $response['entity'];

            // Extract page data
            $page = $this->mapResponseToPage($response);

            // Extract layout data
            $layoutData = isset($entity['layout']) && is_array($entity['layout']) ? $entity['layout'] : [];
            $rowsData = $layoutData['body']['rows'] ?? [];

            // Extract page asset containers
            $containers = [];
            if (isset($entity['containers']) && is_array($entity['containers'])) {
                foreach ($entity['containers'] as $identifier => $containerData) {
                    if (! isset($containerData['container']) || ! is_array($containerData['container'])) {
                        continue;
                    }

                    $container = new Container(
                        identifier: $containerData['container']['identifier'] ?? '',
                        inode: $containerData['container']['inode'] ?? '',
                        title: $containerData['container']['title'] ?? '',
                        path: $containerData['container']['path'] ?? '',
                        live: $containerData['container']['live'] ?? false,
                        working: $containerData['container']['working'] ?? false,
                        locked: $containerData['container']['locked'] ?? false,
                        hostId: $containerData['container']['hostId'] ?? '',
                        hostName: $containerData['container']['hostName'] ?? '',
                        maxContentlets: $containerData['container']['maxContentlets'] ?? 0,
                        notes: $containerData['container']['notes'] ?? '',
                        additionalProperties: array_diff_key($containerData['container'], array_flip([
                            'identifier',
                            'inode',
                            'title',
                            'path',
                            'live',
                            'working',
                            'locked',
                            'hostId',
                            'hostName',
                            'maxContentlets',
                            'notes',
                        ]))
                    );

                    $containerStructures = [];
                    if (isset($containerData['containerStructures']) && is_array($containerData['containerStructures'])) {
                        foreach ($containerData['containerStructures'] as $structureData) {
                            $containerStructures[] = new ContainerStructure(
                                id: $structureData['id'] ?? '',
                                structureId: $structureData['structureId'] ?? '',
                                containerInode: $structureData['containerInode'] ?? '',
                                containerId: $structureData['containerId'] ?? '',
                                code: $structureData['code'] ?? '',
                                contentTypeVar: $structureData['contentTypeVar'] ?? ''
                            );
                        }
                    }

                    $rendered = $containerData['rendered'] ?? [];
                    $contentlets = [];
                    if (isset($containerData['contentlets']) && is_array($containerData['contentlets'])) {
                        foreach ($containerData['contentlets'] as $uuid => $contentletData) {
                            $contentlets[$uuid] = array_map(function ($contentlet) {
                                return new Contentlet(
                                    identifier: $contentlet['identifier'] ?? '',
                                    inode: $contentlet['inode'] ?? '',
                                    title: $contentlet['title'] ?? '',
                                    contentType: $contentlet['contentType'] ?? '',
                                    additionalProperties: array_diff_key($contentlet, array_flip([
                                        'identifier',
                                        'inode',
                                        'title',
                                        'contentType',
                                    ]))
                                );
                            }, is_array($contentletData) ? $contentletData : []);
                        }
                    }

                    $containers[$identifier] = new ContainerPage(
                        container: $container,
                        containerStructures: $containerStructures,
                        rendered: $rendered,
                        contentlets: $contentlets
                    );
                }
            }

            // Map rows and columns
            $rows = array_map(function ($rowData) use ($containers) {
                $columns = array_map(function ($columnData) use ($containers) {
                    $containersRefs = array_map(function ($containerRef) use ($containers) {
                        $uuid = $containerRef['uuid'] ?? '';
                        $identifier = $containerRef['identifier'] ?? '';
                        $containerPage = $containers[$identifier] ?? null;

                        $variantId = null;
                        if (isset($containerPage->container['parentPermissionable'])
                            && is_array($containerPage->container['parentPermissionable'])
                            && isset($containerPage->container['parentPermissionable']['variantId'])) {
                            $variantId = (string)$containerPage->container['parentPermissionable']['variantId'];
                        }

                        $contentlets = DotCmsHelper::extractContentlets($containerPage, $uuid);

                        return new ContainerRef(
                            identifier: $identifier,
                            uuid: $uuid,
                            historyUUIDs: $containerRef['historyUUIDs'] ?? [],
                            contentlets: $contentlets,
                            acceptTypes: DotCmsHelper::extractAcceptTypes($containerPage->containerStructures),
                            maxContentlets: $containerPage->container->maxContentlets,
                            variantId: $variantId
                        );
                    }, $columnData['containers'] ?? []);

                    return new Column(
                        containers: $containersRefs,
                        width: $columnData['width'] ?? 0,
                        widthPercent: $columnData['widthPercent'] ?? 0,
                        leftOffset: $columnData['leftOffset'] ?? 0,
                        styleClass: $columnData['styleClass'] ?? '',
                        preview: $columnData['preview'] ?? false,
                        left: $columnData['left'] ?? 0
                    );
                }, $rowData['columns'] ?? []);

                return new Row(
                    columns: $columns,
                    styleClass: $rowData['styleClass'] ?? null
                );
            }, $rowsData);

            $body = new Body($rows);

            $layout = new Layout(
                width: $layoutData['width'] ?? null,
                title: $layoutData['title'] ?? '',
                header: $layoutData['header'] ?? false,
                footer: $layoutData['footer'] ?? false,
                body: $body,
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
            $templateData = isset($entity['template']) && is_array($entity['template']) ? $entity['template'] : [];

            $template = new Template(
                identifier: $templateData['identifier'] ?? '',
                title: $templateData['title'] ?? '',
                drawed: $templateData['drawed'] ?? false,
                inode: $templateData['inode'] ?? '',
                friendlyName: $templateData['friendlyName'] ?? '',
                header: $templateData['header'] ?? true,
                footer: $templateData['footer'] ?? true,
                working: $templateData['working'] ?? false,
                live: $templateData['live'] ?? false
            );

            // Extract site data
            $siteData = isset($entity['site']) && is_array($entity['site']) ? $entity['site'] : [];

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

            // Extract urlContentMap if available
            $urlContentMap = null;
            $urlContentMapData = $entity['urlContentMap'] ?? null;
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
            $viewAsData = isset($entity['viewAs']) && is_array($entity['viewAs']) ? $entity['viewAs'] : [];

            $visitorData = isset($viewAsData['visitor']) && is_array($viewAsData['visitor']) ? $viewAsData['visitor'] : [];

            // Create UserAgent
            $userAgentData = isset($visitorData['userAgent']) && is_array($visitorData['userAgent'])
                ? $visitorData['userAgent']
                : [];

            $userAgent = new UserAgent(
                browser: $userAgentData['browser'] ?? '',
                version: $userAgentData['version'] ?? '',
                os: $userAgentData['os'] ?? '',
                mobile: $userAgentData['mobile'] ?? false
            );

            // Create GeoLocation
            $geoData = isset($visitorData['geo']) && is_array($visitorData['geo']) ? $visitorData['geo'] : [];

            $geoLocation = new GeoLocation(
                city: $geoData['city'] ?? '',
                country: $geoData['country'] ?? '',
                countryCode: $geoData['countryCode'] ?? '',
                latitude: (float)($geoData['latitude'] ?? 0),
                longitude: (float)($geoData['longitude'] ?? 0),
                region: $geoData['region'] ?? ''
            );

            // Create Visitor
            $visitor = new Visitor(
                tags: [],
                device: $visitorData['device'] ?? '',
                isNew: $visitorData['isNew'] ?? false,
                userAgent: $userAgent,
                referer: $visitorData['referer'] ?? '',
                dmid: $visitorData['dmid'] ?? '',
                geo: $geoLocation,
                personas: []
            );

            // Create ViewAs
            $languageData = isset($viewAsData['language']) && is_array($viewAsData['language']) ? $viewAsData['language'] : [];
            $language = new Language(
                id: $languageData['id'] ?? 0,
                languageCode: $languageData['languageCode'] ?? '',
                countryCode: $languageData['countryCode'] ?? '',
                language: $languageData['language'] ?? '',
                country: $languageData['country'] ?? '',
                isoCode: $languageData['isoCode'] ?? ''
            );

            $viewAs = new ViewAs(
                visitor: $visitor,
                language: $language,
                mode: $viewAsData['mode'] ?? 'LIVE',
                variantId: $viewAsData['variantId'] ?? ''
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
