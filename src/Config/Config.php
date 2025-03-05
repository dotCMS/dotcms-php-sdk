<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Config;

use Dotcms\PhpSdk\Exception\ConfigException;
use GuzzleHttp\RequestOptions;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

class Config
{
    /**
     * @var array{
     *     headers?: array<string, string>,
     *     verify?: bool,
     *     timeout?: positive-int,
     *     connect_timeout?: positive-int,
     *     http_errors?: bool,
     *     allow_redirects?: bool
     * }
     */
    private array $validatedOptions;

    private readonly Logger $logger;

    private const ALLOWED_OPTIONS = [
        RequestOptions::HEADERS,
        RequestOptions::VERIFY,
        RequestOptions::TIMEOUT,
        RequestOptions::CONNECT_TIMEOUT,
        RequestOptions::HTTP_ERRORS,
        RequestOptions::ALLOW_REDIRECTS,
    ];

    /**
     * @param array{
     *     headers?: array<string, string>,
     *     verify?: bool,
     *     timeout?: positive-int,
     *     connect_timeout?: positive-int,
     *     http_errors?: bool,
     *     allow_redirects?: bool
     * } $clientOptions
     * @param array{
     *    level?: LogLevel,
     *    handlers?: HandlerInterface[],
     *    console?: bool
     * } $logConfig
     */
    public function __construct(
        private readonly string $host,
        private readonly string $apiKey,
        array $clientOptions = [
            'headers' => [],
            'verify' => true,
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => true,
            'allow_redirects' => true,
        ],
        private readonly array $logConfig = []
    ) {
        $this->validateHost($host);
        $this->validateApiKey($apiKey);
        $this->validatedOptions = $clientOptions;
        $this->validateClientOptions($clientOptions);
        $this->validateLogConfig($logConfig);

        $this->logger = new Logger('dotcms-sdk');

        // If no config provided, use NullHandler
        if (empty($logConfig)) {
            $this->logger->pushHandler(new NullHandler());

            return;
        }

        $level = isset($this->logConfig['level'])
            ? $this->logConfig['level']->toMonologLevel()
            : LogLevel::INFO->toMonologLevel();

        // Add console handler by default unless explicitly disabled
        if ($this->logConfig['console'] ?? true) {
            $this->logger->pushHandler(new StreamHandler('php://stdout', $level));
        }

        // Add custom handlers if provided
        if (! empty($this->logConfig['handlers'])) {
            foreach ($this->logConfig['handlers'] as $handler) {
                $this->logger->pushHandler($handler);
            }
        }

        // If no handlers were added (console disabled and no custom handlers), use NullHandler
        if (empty($this->logger->getHandlers())) {
            $this->logger->pushHandler(new NullHandler());
        }
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    public function getLogLevel(): LogLevel
    {
        return $this->logConfig['level'] ?? LogLevel::INFO;
    }

    /**
     * @return array{
     *    level?: LogLevel,
     *    handlers?: HandlerInterface[],
     *    console?: bool
     * }
     */
    public function getLogConfig(): array
    {
        return $this->logConfig;
    }

    /**
     * @return array{
     *     base_uri: string,
     *     headers: array<string, string>,
     *     verify?: bool,
     *     timeout?: positive-int,
     *     connect_timeout?: positive-int,
     *     http_errors?: bool,
     *     allow_redirects?: bool
     * }
     */
    public function getClientOptions(): array
    {
        /** @var array<string, string> $headers */
        $headers = $this->validatedOptions['headers'] ?? [];

        return array_merge($this->validatedOptions, [
            'base_uri' => $this->host,
            'headers' => array_merge(
                $headers,
                ['Authorization' => 'Bearer ' . $this->apiKey]
            ),
        ]);
    }

    private function validateHost(string $host): void
    {
        if (empty(trim($host))) {
            throw ConfigException::invalidHost('');
        }

        if (!filter_var($host, FILTER_VALIDATE_URL)) {
            throw ConfigException::invalidHost($host);
        }

        $parsedUrl = parse_url($host);
        if (!isset($parsedUrl['scheme']) || !in_array($parsedUrl['scheme'], ['http', 'https'])) {
            throw ConfigException::invalidHost($host);
        }
    }

    private function validateApiKey(string $apiKey): void
    {
        if (empty(trim($apiKey))) {
            throw ConfigException::emptyApiKey();
        }
    }

    /**
     * @param array{
     *    level?: LogLevel,
     *    handlers?: HandlerInterface[],
     *    console?: bool
     * } $config
     */
    private function validateLogConfig(array $config): void
    {
        // Validate level if set
        if (isset($config['level']) && !($config['level'] instanceof LogLevel)) {
            throw ConfigException::invalidLogConfig('level', 'Must be a LogLevel enum');
        }

        // Validate handlers if set
        if (isset($config['handlers'])) {
            if (!is_array($config['handlers'])) {
                throw ConfigException::invalidLogConfig('handlers', 'Must be an array of HandlerInterface');
            }

            foreach ($config['handlers'] as $handler) {
                if (!$handler instanceof HandlerInterface) {
                    throw ConfigException::invalidLogConfig('handlers', 'Must be an array of HandlerInterface');
                }
            }
        }

        // Validate console if set
        if (isset($config['console']) && !is_bool($config['console'])) {
            throw ConfigException::invalidLogConfig('console', 'Must be a boolean');
        }
    }

    /**
     * @param array{
     *     headers?: array<string, string>,
     *     verify?: bool,
     *     timeout?: positive-int,
     *     connect_timeout?: positive-int,
     *     http_errors?: bool,
     *     allow_redirects?: bool
     * } $options
     */
    private function validateClientOptions(array $options): void
    {
        // Check for unknown options
        $unknownOptions = array_diff(array_keys($options), self::ALLOWED_OPTIONS);
        if (! empty($unknownOptions)) {
            throw ConfigException::invalidClientOption(
                (string) array_key_first($unknownOptions),
                'Unknown option'
            );
        }

        // Validate headers
        if (isset($options[RequestOptions::HEADERS])) {
            if (!is_array($options[RequestOptions::HEADERS])) {
                throw ConfigException::invalidClientOption(
                    RequestOptions::HEADERS,
                    'Must be an array'
                );
            }

            foreach ($options[RequestOptions::HEADERS] as $name => $value) {
                if (!is_string($name) || !is_string($value)) {
                    throw ConfigException::invalidClientOption(
                        RequestOptions::HEADERS,
                        'Header names and values must be strings'
                    );
                }
            }
        }

        // Validate timeout
        if (isset($options[RequestOptions::TIMEOUT])) {
            if (!is_int($options[RequestOptions::TIMEOUT]) || $options[RequestOptions::TIMEOUT] <= 0) {
                throw ConfigException::invalidClientOption(
                    RequestOptions::TIMEOUT,
                    'Must be a positive integer'
                );
            }
        }

        // Validate connect_timeout
        if (isset($options[RequestOptions::CONNECT_TIMEOUT])) {
            if (!is_int($options[RequestOptions::CONNECT_TIMEOUT]) || $options[RequestOptions::CONNECT_TIMEOUT] <= 0) {
                throw ConfigException::invalidClientOption(
                    RequestOptions::CONNECT_TIMEOUT,
                    'Must be a positive integer'
                );
            }
        }

        // Validate verify
        if (isset($options[RequestOptions::VERIFY]) && !is_bool($options[RequestOptions::VERIFY])) {
            throw ConfigException::invalidClientOption(
                RequestOptions::VERIFY,
                'Must be a boolean'
            );
        }

        // Validate http_errors
        if (isset($options[RequestOptions::HTTP_ERRORS]) && !is_bool($options[RequestOptions::HTTP_ERRORS])) {
            throw ConfigException::invalidClientOption(
                RequestOptions::HTTP_ERRORS,
                'Must be a boolean'
            );
        }

        // Validate allow_redirects
        if (isset($options[RequestOptions::ALLOW_REDIRECTS]) && !is_bool($options[RequestOptions::ALLOW_REDIRECTS])) {
            throw ConfigException::invalidClientOption(
                RequestOptions::ALLOW_REDIRECTS,
                'Must be a boolean'
            );
        }

        $this->validatedOptions = $options;
    }
}
