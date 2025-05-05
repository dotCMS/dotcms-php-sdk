<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model;

use Dotcms\PhpSdk\Model\ViewAs;
use Dotcms\PhpSdk\Model\ViewAs\GeoLocation;
use Dotcms\PhpSdk\Model\ViewAs\UserAgent;
use Dotcms\PhpSdk\Model\ViewAs\Visitor;
use PHPUnit\Framework\TestCase;

class ViewAsTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        $visitor = new Visitor(
            [], // tags
            'desktop', // device
            true, // isNew
            new UserAgent('Chrome', '120.0', 'Windows', false), // userAgent
            'https://example.com', // referer
            'test-dmid', // dmid
            new GeoLocation('Miami', 'United States', 'US', 25.7743, -80.1937, 'Florida'), // geo
            [] // personas
        );

        $language = [
            'id' => 1,
            'languageCode' => 'en',
            'countryCode' => 'US',
            'language' => 'English',
            'country' => 'United States',
        ];

        $viewAs = new ViewAs($visitor, $language, 'PREVIEW');

        $this->assertInstanceOf(Visitor::class, $viewAs->visitor);
        $this->assertEquals($language, $viewAs->language);
        $this->assertEquals('PREVIEW', $viewAs->mode);

        // Test visitor properties
        $this->assertEquals('desktop', $viewAs->visitor->device);
        $this->assertTrue($viewAs->visitor->isNew);
        $this->assertEquals('https://example.com', $viewAs->visitor->referer);
        $this->assertEquals('test-dmid', $viewAs->visitor->dmid);
        $this->assertEmpty($viewAs->visitor->tags);
        $this->assertEmpty($viewAs->visitor->personas);

        // Test user agent
        $this->assertEquals('Chrome', $viewAs->visitor->userAgent->browser);
        $this->assertEquals('120.0', $viewAs->visitor->userAgent->version);
        $this->assertEquals('Windows', $viewAs->visitor->userAgent->os);
        $this->assertFalse($viewAs->visitor->userAgent->mobile);

        // Test geo location
        $this->assertEquals('Miami', $viewAs->visitor->geo->city);
        $this->assertEquals('United States', $viewAs->visitor->geo->country);
        $this->assertEquals('US', $viewAs->visitor->geo->countryCode);
        $this->assertEquals(25.7743, $viewAs->visitor->geo->latitude);
        $this->assertEquals(-80.1937, $viewAs->visitor->geo->longitude);
        $this->assertEquals('Florida', $viewAs->visitor->geo->region);
    }
}
