<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Config;

use Dotcms\PhpSdk\Exception\ConfigException;
use GuzzleHttp\RequestOptions;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

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
        $this->validatedOptions = $clientOptions;
        $this->validateHost($host);
        $this->validateApiKey($apiKey);
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
        try {
            v::stringType()->notEmpty()
                ->url()
                ->regex('/^https?:\/\/.+/')
                ->assert($host);
        } catch (ValidationException $e) {
            throw ConfigException::invalidHost($host);
        }
    }

    private function validateApiKey(string $apiKey): void
    {
        try {
            v::notEmpty()
                ->assert($apiKey);
        } catch (ValidationException $e) {
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
        if (isset($config['level'])) {
            try {
                v::instance(LogLevel::class)
                    ->assert($config['level']);
            } catch (ValidationException $e) {
                throw ConfigException::invalidLogConfig('level', 'Must be a LogLevel enum');
            }
        }

        // Validate handlers if set
        if (isset($config['handlers'])) {
            try {
                v::arrayVal()
                    ->each(v::instance(HandlerInterface::class))
                    ->assert($config['handlers']);
            } catch (ValidationException $e) {
                throw ConfigException::invalidLogConfig('handlers', 'Must be an array of HandlerInterface');
            }
        }

        // Validate console if set
        if (isset($config['console'])) {
            try {
                v::boolType()
                    ->assert($config['console']);
            } catch (ValidationException $e) {
                throw ConfigException::invalidLogConfig('console', 'Must be a boolean');
            }
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
            if (! is_array($options[RequestOptions::HEADERS])) {
                throw ConfigException::invalidClientOption(
                    RequestOptions::HEADERS,
                    'Must be an array'
                );
            }

            foreach ($options[RequestOptions::HEADERS] as $name => $value) {
                try {
                    v::stringType()->assert($name);
                    v::stringType()->assert($value);
                } catch (ValidationException $e) {
                    throw ConfigException::invalidClientOption(
                        RequestOptions::HEADERS,
                        'Header names and values must be strings'
                    );
                }
            }
        }

        // Validate timeouts
        foreach ([RequestOptions::TIMEOUT, RequestOptions::CONNECT_TIMEOUT] as $option) {
            if (isset($options[$option])) {
                try {
                    v::intType()->positive()
                        ->assert($options[$option]);
                } catch (ValidationException $e) {
                    throw ConfigException::invalidClientOption(
                        $option,
                        'Must be a positive integer'
                    );
                }
            }
        }

        // Validate booleans
        foreach ([RequestOptions::VERIFY, RequestOptions::HTTP_ERRORS, RequestOptions::ALLOW_REDIRECTS] as $option) {
            if (isset($options[$option])) {
                try {
                    v::boolType()
                        ->assert($options[$option]);
                } catch (ValidationException $e) {
                    throw ConfigException::invalidClientOption(
                        $option,
                        'Must be a boolean'
                    );
                }
            }
        }

        $this->validatedOptions = $options;
    }
}
