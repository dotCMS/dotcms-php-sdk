<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Service;

use Dotcms\PhpSdk\Exception\ResponseException;
use Dotcms\PhpSdk\Http\HttpClient;
use Dotcms\PhpSdk\Http\Response;
use Dotcms\PhpSdk\Model\NavigationItem;
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
     * @param array<string, mixed> $response The response from the API
     * @return NavigationItem
     */
    private function mapResponseToNavigationItem(array $response): NavigationItem
    {
        $entity = [];
        if (isset($response['entity']) && is_array($response['entity'])) {
            $entity = $response['entity'];
        }

        $code = null;
        if (isset($entity['code']) && (is_string($entity['code']) || is_null($entity['code']))) {
            $code = $entity['code'];
        }

        $folder = null;
        if (isset($entity['folder']) && (is_string($entity['folder']) || is_null($entity['folder']))) {
            $folder = $entity['folder'];
        }

        $host = '';
        if (isset($entity['host']) && is_string($entity['host'])) {
            $host = $entity['host'];
        }

        $languageId = 1; // Default languageId
        if (isset($entity['languageId']) && is_numeric($entity['languageId'])) {
            $languageId = (int)$entity['languageId'];
        }

        $href = '';
        if (isset($entity['href']) && is_string($entity['href'])) {
            $href = $entity['href'];
        }

        $title = '';
        if (isset($entity['title']) && is_string($entity['title'])) {
            $title = $entity['title'];
        }

        $type = 'folder'; // Default type
        if (isset($entity['type']) && is_string($entity['type'])) {
            $type = $entity['type'];
        }

        $hash = 0;
        if (isset($entity['hash']) && is_numeric($entity['hash'])) {
            $hash = (int)$entity['hash'];
        }

        $target = '_self'; // Default target
        if (isset($entity['target']) && is_string($entity['target'])) {
            $target = $entity['target'];
        }

        $order = 0;
        if (isset($entity['order']) && is_numeric($entity['order'])) {
            $order = (int)$entity['order'];
        }

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
