<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\Page;

use Dotcms\PhpSdk\Model\Page\VanityUrl;
use PHPUnit\Framework\TestCase;

class VanityUrlTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        $vanityUrl = new VanityUrl(
            pattern: '/cms401Page',
            vanityUrlId: 'f881a946-e1ee-40af-8d20-a75c0c98f8ed',
            url: '/cms401Page',
            siteId: 'SYSTEM_HOST',
            languageId: 1,
            forwardTo: '/login/index',
            response: 302,
            order: 0,
            forward: false,
            temporaryRedirect: true,
            permanentRedirect: false
        );

        // Test all properties
        $this->assertEquals('/cms401Page', $vanityUrl->pattern);
        $this->assertEquals('f881a946-e1ee-40af-8d20-a75c0c98f8ed', $vanityUrl->vanityUrlId);
        $this->assertEquals('/cms401Page', $vanityUrl->url);
        $this->assertEquals('SYSTEM_HOST', $vanityUrl->siteId);
        $this->assertEquals(1, $vanityUrl->languageId);
        $this->assertEquals('/login/index', $vanityUrl->forwardTo);
        $this->assertEquals(302, $vanityUrl->response);
        $this->assertEquals(0, $vanityUrl->order);
        $this->assertFalse($vanityUrl->forward);
        $this->assertTrue($vanityUrl->temporaryRedirect);
        $this->assertFalse($vanityUrl->permanentRedirect);
    }

    public function testDifferentValues(): void
    {
        $vanityUrl = new VanityUrl(
            pattern: '/custom-path',
            vanityUrlId: '123e4567-e89b-12d3-a456-426614174000',
            url: '/custom-path',
            siteId: 'demo.dotcms.com',
            languageId: 2,
            forwardTo: '/custom-destination',
            response: 301,
            order: 1,
            forward: true,
            temporaryRedirect: false,
            permanentRedirect: true
        );

        // Test different values
        $this->assertEquals('/custom-path', $vanityUrl->pattern);
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $vanityUrl->vanityUrlId);
        $this->assertEquals('/custom-path', $vanityUrl->url);
        $this->assertEquals('demo.dotcms.com', $vanityUrl->siteId);
        $this->assertEquals(2, $vanityUrl->languageId);
        $this->assertEquals('/custom-destination', $vanityUrl->forwardTo);
        $this->assertEquals(301, $vanityUrl->response);
        $this->assertEquals(1, $vanityUrl->order);
        $this->assertTrue($vanityUrl->forward);
        $this->assertFalse($vanityUrl->temporaryRedirect);
        $this->assertTrue($vanityUrl->permanentRedirect);
    }

    public function testPropertiesAreReadonly(): void
    {
        $vanityUrl = new VanityUrl(
            pattern: '/test',
            vanityUrlId: 'test-id',
            url: '/test',
            siteId: 'test-site',
            languageId: 1,
            forwardTo: '/test-forward',
            response: 302,
            order: 0,
            forward: false,
            temporaryRedirect: true,
            permanentRedirect: false
        );

        // Test that properties are readonly by attempting to modify them
        $this->expectException(\Error::class);
        $vanityUrl->pattern = '/new-pattern';
    }
}
