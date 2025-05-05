<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model;

use Dotcms\PhpSdk\Model\Language;
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

        $language = new Language(
            id: 1,
            languageCode: 'en',
            countryCode: 'US',
            language: 'English',
            country: 'United States',
            isoCode: 'en-US'
        );

        $viewAs = new ViewAs($visitor, $language, 'PREVIEW', 'variant-123');

        $this->assertInstanceOf(Visitor::class, $viewAs->visitor);
        $this->assertInstanceOf(Language::class, $viewAs->language);
        $this->assertEquals('PREVIEW', $viewAs->mode);
        $this->assertEquals('variant-123', $viewAs->variantId);

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

        // Test language
        $this->assertEquals(1, $viewAs->language->id);
        $this->assertEquals('en', $viewAs->language->languageCode);
        $this->assertEquals('US', $viewAs->language->countryCode);
        $this->assertEquals('English', $viewAs->language->language);
        $this->assertEquals('United States', $viewAs->language->country);
        $this->assertEquals('en-US', $viewAs->language->isoCode);
    }

    public function testDefaultVariantId(): void
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

        $language = new Language(
            id: 1,
            languageCode: 'en',
            countryCode: 'US',
            language: 'English',
            country: 'United States',
            isoCode: 'en-US'
        );

        $viewAs = new ViewAs($visitor, $language, 'PREVIEW');
        $this->assertEquals('', $viewAs->variantId);
    }
}
