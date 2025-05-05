<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Service;

use Dotcms\PhpSdk\Exception\ResponseException;
use Dotcms\PhpSdk\Http\HttpClient;
use Dotcms\PhpSdk\Http\Response;
use Dotcms\PhpSdk\Model\Content\NavigationItem;
use Dotcms\PhpSdk\Request\NavigationRequest;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Service for interacting with dotCMS Navigation API
 */
class NavigationService
{
    /**
     * @param HttpClient $httpClient The HTTP client to use for requests
     */
    public function __construct(
        private readonly HttpClient $httpClient
    ) {
    }

    /**
     * Fetch navigation items from dotCMS
     *
     * @param NavigationRequest $request The navigation request
     * @return NavigationItem The navigation item with optional children
     * @throws ResponseException If the response cannot be mapped to a NavigationItem
     */
    public function getNavigation(NavigationRequest $request): NavigationItem
    {
        try {
            $response = $this->httpClient->get(
                $request->buildPath(),
                ['query' => $request->buildQueryParams()]
            );

            $responseData = $response->toArray();

            return $this->mapResponseToNavigationItem($responseData);
        } catch (\Exception $e) {
            if ($e instanceof ResponseException) {
                throw $e;
            }

            throw new ResponseException(
                'Failed to fetch navigation: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Fetch navigation items from dotCMS asynchronously
     *
     * @param NavigationRequest $request The navigation request
     * @return PromiseInterface A promise that resolves to a NavigationItem
     */
    public function getNavigationAsync(NavigationRequest $request): PromiseInterface
    {
        return $this->httpClient->requestAsync(
            'GET',
            $request->buildPath(),
            ['query' => $request->buildQueryParams()]
        )->then(
            function (Response $response) {
                $responseData = $response->toArray();

                return $this->mapResponseToNavigationItem($responseData);
            },
            function (\Exception $e) {
                if ($e instanceof ResponseException) {
                    throw $e;
                }

                throw new ResponseException(
                    'Failed to fetch navigation: ' . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
        );
    }

    /**
     * Map the API response to a NavigationItem
     *
     * @param array<string, mixed> $response
     */
    private function mapResponseToNavigationItem(array $response): NavigationItem
    {
        /** @var array<string, mixed> $entity */
        $entity = $response['entity'] ?? [];

        /** @var string|null $code */
        $code = null;
        if (isset($entity['code']) && (is_string($entity['code']) || is_null($entity['code']))) {
            $code = $entity['code'];
        }

        /** @var string|null $folder */
        $folder = null;
        if (isset($entity['folder']) && (is_string($entity['folder']) || is_null($entity['folder']))) {
            $folder = $entity['folder'];
        }

        /** @var string $host */
        $host = '';
        if (isset($entity['host']) && is_string($entity['host'])) {
            $host = $entity['host'];
        }

        /** @var int $languageId */
        $languageId = 1; // Default value
        if (isset($entity['languageId']) && is_numeric($entity['languageId'])) {
            $languageId = (int)$entity['languageId'];
        }

        /** @var string $href */
        $href = '';
        if (isset($entity['href']) && is_string($entity['href'])) {
            $href = $entity['href'];
        }

        /** @var string $title */
        $title = '';
        if (isset($entity['title']) && is_string($entity['title'])) {
            $title = $entity['title'];
        }

        /** @var string $type */
        $type = 'folder'; // Default value
        if (isset($entity['type']) && is_string($entity['type'])) {
            $type = $entity['type'];
        }

        /** @var int $hash */
        $hash = 0; // Default value
        if (isset($entity['hash']) && is_numeric($entity['hash'])) {
            $hash = (int)$entity['hash'];
        }

        /** @var string $target */
        $target = '_self'; // Default value
        if (isset($entity['target']) && is_string($entity['target'])) {
            $target = $entity['target'];
        }

        /** @var int $order */
        $order = 0; // Default value
        if (isset($entity['order']) && is_numeric($entity['order'])) {
            $order = (int)$entity['order'];
        }

        /** @var array<int, array<string, mixed>>|null $children */
        $children = null;
        if (isset($entity['children']) && is_array($entity['children'])) {
            $children = $entity['children'];
        }

        return new NavigationItem(
            $code,
            $folder,
            $host,
            $languageId,
            $href,
            $title,
            $type,
            $hash,
            $target,
            $order,
            $children
        );
    }
}
