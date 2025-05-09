<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\ViewAs;

class GeoLocation
{
    /**
     * @param string $city City name
     * @param string $country Country name
     * @param string $countryCode Country code
     * @param float $latitude Latitude coordinate
     * @param float $longitude Longitude coordinate
     * @param string $region Region name
     */
    public function __construct(
        public readonly string $city,
        public readonly string $country,
        public readonly string $countryCode,
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly string $region,
    ) {
    }
}
