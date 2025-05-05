<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\ViewAs;

use Dotcms\PhpSdk\Model\ViewAs\GeoLocation;
use PHPUnit\Framework\TestCase;

class GeoLocationTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        // Test US location
        $usLocation = new GeoLocation(
            city: 'New York',
            country: 'United States',
            countryCode: 'US',
            latitude: 40.7128,
            longitude: -74.0060,
            region: 'New York'
        );

        $this->assertEquals('New York', $usLocation->city);
        $this->assertEquals('United States', $usLocation->country);
        $this->assertEquals('US', $usLocation->countryCode);
        $this->assertEquals(40.7128, $usLocation->latitude);
        $this->assertEquals(-74.0060, $usLocation->longitude);
        $this->assertEquals('New York', $usLocation->region);

        // Test UK location
        $ukLocation = new GeoLocation(
            city: 'London',
            country: 'United Kingdom',
            countryCode: 'GB',
            latitude: 51.5074,
            longitude: -0.1278,
            region: 'England'
        );

        $this->assertEquals('London', $ukLocation->city);
        $this->assertEquals('United Kingdom', $ukLocation->country);
        $this->assertEquals('GB', $ukLocation->countryCode);
        $this->assertEquals(51.5074, $ukLocation->latitude);
        $this->assertEquals(-0.1278, $ukLocation->longitude);
        $this->assertEquals('England', $ukLocation->region);

        // Test Japanese location
        $jpLocation = new GeoLocation(
            city: 'Tokyo',
            country: 'Japan',
            countryCode: 'JP',
            latitude: 35.6762,
            longitude: 139.6503,
            region: 'Kanto'
        );

        $this->assertEquals('Tokyo', $jpLocation->city);
        $this->assertEquals('Japan', $jpLocation->country);
        $this->assertEquals('JP', $jpLocation->countryCode);
        $this->assertEquals(35.6762, $jpLocation->latitude);
        $this->assertEquals(139.6503, $jpLocation->longitude);
        $this->assertEquals('Kanto', $jpLocation->region);
    }
}
