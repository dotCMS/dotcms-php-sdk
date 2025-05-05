<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\Site;

class VanityUrl
{
    /**
     * @param string $pattern The URL pattern to match
     * @param string $vanityUrlId The unique identifier for the vanity URL
     * @param string $url The vanity URL path
     * @param string $siteId The site identifier
     * @param int $languageId The language ID
     * @param string $forwardTo The URL to forward to
     * @param int $response The HTTP response code
     * @param int $order The order of the vanity URL
     * @param bool $forward Whether to forward the request
     * @param bool $temporaryRedirect Whether to use temporary redirect
     * @param bool $permanentRedirect Whether to use permanent redirect
     */
    public function __construct(
        public readonly string $pattern,
        public readonly string $vanityUrlId,
        public readonly string $url,
        public readonly string $siteId,
        public readonly int $languageId,
        public readonly string $forwardTo,
        public readonly int $response,
        public readonly int $order,
        public readonly bool $forward,
        public readonly bool $temporaryRedirect,
        public readonly bool $permanentRedirect,
    ) {
    }
}
