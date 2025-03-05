<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Model\ViewAs;

class GeoLocation implements \JsonSerializable
{
    /**
     * @param string $city City name
     * @param string $country Country name
     * @param string $countryCode Country code
     * @param string $latitude Latitude coordinate
     * @param string $longitude Longitude coordinate
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

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'city' => $this->city,
            'country' => $this->country,
            'countryCode' => $this->countryCode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'region' => $this->region,
        ];
    }
}
