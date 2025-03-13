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
            $this->validateResponse($responseData);

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
                $this->validateResponse($responseData);
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
     * Validate the response from the API
     *
     * @param array $response The response from the API
     * @throws ResponseException If the response is invalid
     */
    private function validateResponse(array $response): void
    {
        if (!isset($response['entity'])) {
            throw new ResponseException('Invalid response: entity missing');
        }

        $entity = $response['entity'];

        // Check for required fields
        $requiredFields = ['host', 'languageId', 'href', 'title', 'type', 'hash', 'target', 'order'];
        foreach ($requiredFields as $field) {
            if (!isset($entity[$field])) {
                throw new ResponseException("Invalid response: {$field} missing");
            }
        }

        // Check for errors in the response
        if (isset($response['errors']) && !empty($response['errors'])) {
            $errorMessages = implode(', ', $response['errors']);
            throw new ResponseException("API returned errors: {$errorMessages}");
        }
    }

    /**
     * Map the API response to a NavigationItem
     *
     * @param array $response The response from the API
     * @return NavigationItem
     */
    private function mapResponseToNavigationItem(array $response): NavigationItem
    {
        $entity = $response['entity'];

        return new NavigationItem(
            $entity['code'] ?? null,
            $entity['folder'] ?? null,
            $entity['host'],
            $entity['languageId'],
            $entity['href'],
            $entity['title'],
            $entity['type'],
            $entity['hash'],
            $entity['target'],
            $entity['order'],
            $entity['children'] ?? null
        );
    }
} 