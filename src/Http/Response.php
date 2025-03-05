<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Http;

use Dotcms\PhpSdk\Exception\HttpException;
use Dotcms\PhpSdk\Exception\ResponseException;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

/**
 * Wrapper for PSR-7 ResponseInterface that provides convenient methods for handling HTTP responses.
 */
class Response
{
    /**
     * Creates a new Response instance.
     *
     * @throws HttpException If the response has an error status code (4xx or 5xx)
     */
    public function __construct(
        private readonly ResponseInterface $response
    ) {
        $this->throwIfError();
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * Gets all response headers.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     * @return array<string, string[]> Returns an associative array of the message's headers.
     */
    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    /**
     * Gets a specific response header by name.
     *
     * This method returns an array of all the header values if the header exists,
     * and an empty array if header does not exist.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given header.
     */
    public function getHeader(string $name): array
    {
        try {
            v::stringType()->notEmpty()->assert($name);

            return $this->response->getHeader($name);
        } catch (ValidationException $e) {
            return [];
        }
    }

    /**
     * Converts the JSON response body to an array.
     *
     * This method will:
     * - Return an empty array if the response body is empty
     * - Attempt to decode the response body as JSON
     * - Validate that the decoded data is an array
     *
     * @return array<string, mixed> The response body decoded as an array
     * @throws ResponseException When the response body is not valid JSON or not an array
     */
    public function toArray(): array
    {
        $contents = $this->response->getBody()->getContents();

        if (empty($contents)) {
            return [];
        }

        $data = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ResponseException::invalidJson(json_last_error_msg());
        }

        if (! is_array($data)) {
            $type = gettype($data);
            $preview = $this->getValuePreview($data);

            throw ResponseException::invalidType($type, $preview);
        }

        return $data;
    }

    /**
     * Gets a preview of a value for error messages.
     *
     * @param mixed $value The value to preview
     * @return string A string representation of the value
     */
    private function getValuePreview(mixed $value): string
    {
        if (is_scalar($value)) {
            $preview = json_encode($value);
            if ($preview === false) {
                return sprintf('(%s unable to encode)', gettype($value));
            }

            try {
                v::stringType()->length(1, 100)->assert($preview);

                return $preview;
            } catch (ValidationException $e) {
                return substr($preview, 0, 97) . '...';
            }
        }

        return sprintf('(%s)', gettype($value));
    }

    /**
     * Gets the underlying PSR-7 ResponseInterface instance.
     *
     * This method provides access to the raw response object when needed for
     * advanced use cases not covered by the wrapper methods.
     *
     * @return ResponseInterface The underlying PSR-7 ResponseInterface instance
     */
    public function getRawResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Checks if the response has an error status code and throws an exception if it does.
     *
     * @throws HttpException If the response has an error status code (4xx or 5xx)
     */
    private function throwIfError(): void
    {
        $statusCode = $this->response->getStatusCode();

        if ($statusCode >= 400) {
            throw HttpException::fromResponse(
                $this->response,
                $this->response->getBody()->getContents()
            );
        }
    }
}
