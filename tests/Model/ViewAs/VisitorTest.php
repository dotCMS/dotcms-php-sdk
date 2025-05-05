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
            tags: [new VisitorTag(tag: 'french alps', count: 3)],
            device: 'desktop',
            isNew: false,
            userAgent: new UserAgent(
                browser: 'Chrome',
                version: '100.0',
                os: 'macOS',
                mobile: false
            ),
            referer: 'https://example.com',
            dmid: '123456',
            geo: new GeoLocation(
                city: 'Miami',
                country: 'United States',
                countryCode: 'US',
                latitude: 25.7617,
                longitude: -80.1918,
                region: 'Florida'
            ),
            personas: []
        );

        // Test basic properties
        $this->assertEquals('desktop', $visitor->device);
        $this->assertFalse($visitor->isNew);
        $this->assertEquals('https://example.com', $visitor->referer);
        $this->assertEquals('123456', $visitor->dmid);

        // Test tags array
        $this->assertCount(1, $visitor->tags);
        $this->assertInstanceOf(VisitorTag::class, $visitor->tags[0]);
        $this->assertEquals('french alps', $visitor->tags[0]->tag);
        $this->assertEquals(3, $visitor->tags[0]->count);

        // Test UserAgent object
        $this->assertInstanceOf(UserAgent::class, $visitor->userAgent);
        $this->assertEquals('Chrome', $visitor->userAgent->browser);
        $this->assertEquals('100.0', $visitor->userAgent->version);
        $this->assertEquals('macOS', $visitor->userAgent->os);
        $this->assertFalse($visitor->userAgent->mobile);

        // Test GeoLocation object
        $this->assertInstanceOf(GeoLocation::class, $visitor->geo);
        $this->assertEquals('Miami', $visitor->geo->city);
        $this->assertEquals('United States', $visitor->geo->country);
        $this->assertEquals('US', $visitor->geo->countryCode);
        $this->assertEquals(25.7617, $visitor->geo->latitude);
        $this->assertEquals(-80.1918, $visitor->geo->longitude);
        $this->assertEquals('Florida', $visitor->geo->region);

        // Test personas array
        $this->assertCount(0, $visitor->personas);
    }
}
