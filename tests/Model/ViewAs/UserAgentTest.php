<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\ViewAs;

use Dotcms\PhpSdk\Model\ViewAs\UserAgent;
use PHPUnit\Framework\TestCase;

class UserAgentTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        // Test desktop user agent
        $desktopUserAgent = new UserAgent(
            browser: 'Chrome',
            version: '120.0',
            os: 'Windows',
            mobile: false
        );

        $this->assertEquals('Chrome', $desktopUserAgent->browser);
        $this->assertEquals('120.0', $desktopUserAgent->version);
        $this->assertEquals('Windows', $desktopUserAgent->os);
        $this->assertFalse($desktopUserAgent->mobile);

        // Test mobile user agent
        $mobileUserAgent = new UserAgent(
            browser: 'Safari',
            version: '17.0',
            os: 'iOS',
            mobile: true
        );

        $this->assertEquals('Safari', $mobileUserAgent->browser);
        $this->assertEquals('17.0', $mobileUserAgent->version);
        $this->assertEquals('iOS', $mobileUserAgent->os);
        $this->assertTrue($mobileUserAgent->mobile);

        // Test different browser and OS combinations
        $firefoxUserAgent = new UserAgent(
            browser: 'Firefox',
            version: '123.0',
            os: 'macOS',
            mobile: false
        );

        $this->assertEquals('Firefox', $firefoxUserAgent->browser);
        $this->assertEquals('123.0', $firefoxUserAgent->version);
        $this->assertEquals('macOS', $firefoxUserAgent->os);
        $this->assertFalse($firefoxUserAgent->mobile);
    }
}
