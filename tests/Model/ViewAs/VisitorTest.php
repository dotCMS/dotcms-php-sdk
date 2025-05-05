<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\ViewAs;

use Dotcms\PhpSdk\Model\ViewAs\GeoLocation;
use Dotcms\PhpSdk\Model\ViewAs\UserAgent;
use Dotcms\PhpSdk\Model\ViewAs\Visitor;
use Dotcms\PhpSdk\Model\ViewAs\VisitorTag;
use PHPUnit\Framework\TestCase;

class VisitorTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        $userAgent = new UserAgent(
            browser: 'Chrome',
            version: '120.0',
            os: 'Windows',
            mobile: false
        );

        $geoLocation = new GeoLocation(
            city: 'New York',
            country: 'United States',
            countryCode: 'US',
            latitude: 40.7128,
            longitude: -74.0060,
            region: 'New York'
        );

        $visitorTag = new VisitorTag(
            tag: 'technology',
            count: 1
        );

        $visitor = new Visitor(
            tags: [$visitorTag],
            device: 'desktop',
            isNew: true,
            userAgent: $userAgent,
            referer: 'https://www.google.com',
            dmid: '123456789',
            geo: $geoLocation,
            personas: ['developer' => 0.8, 'tech-savvy' => 0.9]
        );

        // Test basic properties
        $this->assertEquals('desktop', $visitor->device);
        $this->assertTrue($visitor->isNew);
        $this->assertEquals('https://www.google.com', $visitor->referer);
        $this->assertEquals('123456789', $visitor->dmid);

        // Test tags array
        $this->assertCount(1, $visitor->tags);
        $this->assertInstanceOf(VisitorTag::class, $visitor->tags[0]);
        $this->assertEquals('technology', $visitor->tags[0]->tag);
        $this->assertEquals(1, $visitor->tags[0]->count);

        // Test UserAgent object
        $this->assertInstanceOf(UserAgent::class, $visitor->userAgent);
        $this->assertEquals('Chrome', $visitor->userAgent->browser);
        $this->assertEquals('120.0', $visitor->userAgent->version);
        $this->assertEquals('Windows', $visitor->userAgent->os);
        $this->assertFalse($visitor->userAgent->mobile);

        // Test GeoLocation object
        $this->assertInstanceOf(GeoLocation::class, $visitor->geo);
        $this->assertEquals('New York', $visitor->geo->city);
        $this->assertEquals('United States', $visitor->geo->country);
        $this->assertEquals('US', $visitor->geo->countryCode);
        $this->assertEquals(40.7128, $visitor->geo->latitude);
        $this->assertEquals(-74.0060, $visitor->geo->longitude);
        $this->assertEquals('New York', $visitor->geo->region);

        // Test personas array
        $this->assertCount(2, $visitor->personas);
        $this->assertEquals(0.8, $visitor->personas['developer']);
        $this->assertEquals(0.9, $visitor->personas['tech-savvy']);
    }
}
