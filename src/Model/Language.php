<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model;

class Language
{
    /**
     * @param int $id Language ID
     * @param string $languageCode Language code (e.g., 'en')
     * @param string $countryCode Country code (e.g., 'US')
     * @param string $language Language name (e.g., 'English')
     * @param string $country Country name (e.g., 'United States')
     * @param string $isoCode ISO code (e.g., 'en-us')
     */
    public function __construct(
        public readonly int $id,
        public readonly string $languageCode,
        public readonly string $countryCode,
        public readonly string $language,
        public readonly string $country,
        public readonly string $isoCode,
    ) {
    }
}
