<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model;

use Dotcms\PhpSdk\Model\Site\Site;
use PHPUnit\Framework\TestCase;

class SiteTest extends TestCase
{
    public function testConstructorAndBasicProperties(): void
    {
        $site = new Site(
            'site-id',
            'demo.dotcms.com',
            'a1de7e5c-d4b4-43ab-866c-98845673fb74', // inode
            true, // working
            '/demo', // folder
            false, // locked
            false, // archived
            true // live
        );

        $this->assertEquals('site-id', $site->identifier);
        $this->assertEquals('demo.dotcms.com', $site->hostname);
        $this->assertEquals('a1de7e5c-d4b4-43ab-866c-98845673fb74', $site->inode);
        $this->assertTrue($site->working);
        $this->assertEquals('/demo', $site->folder);
        $this->assertFalse($site->locked);
        $this->assertFalse($site->archived);
        $this->assertTrue($site->live);
    }

    public function testDefaultValues(): void
    {
        $site = new Site('site-id', 'demo.dotcms.com');

        $this->assertEquals('site-id', $site->identifier);
        $this->assertEquals('demo.dotcms.com', $site->hostname);
        $this->assertEquals('', $site->inode);
        $this->assertFalse($site->working);
        $this->assertEquals('', $site->folder);
        $this->assertFalse($site->locked);
        $this->assertFalse($site->archived);
        $this->assertFalse($site->live);
    }

    public function testArrayAccess(): void
    {
        $site = new Site(
            'site-id',
            'demo.dotcms.com',
            'a1de7e5c-d4b4-43ab-866c-98845673fb74',
            true,
            '/demo',
            false,
            false,
            true
        );

        // Test offsetExists
        $this->assertTrue(isset($site['identifier']));
        $this->assertTrue(isset($site['hostname']));
        $this->assertTrue(isset($site['inode']));
        $this->assertTrue(isset($site['working']));
        $this->assertTrue(isset($site['folder']));
        $this->assertTrue(isset($site['locked']));
        $this->assertTrue(isset($site['archived']));
        $this->assertTrue(isset($site['live']));
        $this->assertFalse(isset($site['nonExistentProperty']));

        // Test offsetGet
        $this->assertEquals('site-id', $site['identifier']);
        $this->assertEquals('demo.dotcms.com', $site['hostname']);
        $this->assertEquals('a1de7e5c-d4b4-43ab-866c-98845673fb74', $site['inode']);
        $this->assertTrue($site['working']);
        $this->assertEquals('/demo', $site['folder']);
        $this->assertFalse($site['locked']);
        $this->assertFalse($site['archived']);
        $this->assertTrue($site['live']);
        $this->assertNull($site['nonExistentProperty']);
    }

    public function testArrayAccessSetThrowsException(): void
    {
        $site = new Site('site-id', 'demo.dotcms.com');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Properties are read-only');

        $site['identifier'] = 'new-id';
    }

    public function testArrayAccessUnsetThrowsException(): void
    {
        $site = new Site('site-id', 'demo.dotcms.com');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Properties are read-only');

        unset($site['identifier']);
    }

    public function testAdditionalProperties(): void
    {
        $additionalProps = [
            'customProp1' => 'value1',
            'customProp2' => 123,
            'customProp3' => ['nested' => 'value'],
        ];

        $site = new Site(
            'site-id',
            'demo.dotcms.com',
            '',
            false,
            '',
            false,
            false,
            false,
            $additionalProps
        );

        // Test accessing via array access
        $this->assertEquals('value1', $site['customProp1']);
        $this->assertEquals(123, $site['customProp2']);
        $this->assertEquals(['nested' => 'value'], $site['customProp3']);

        // Test accessing via jsonSerialize
        $json = $site->jsonSerialize();
        $this->assertEquals('value1', $json['customProp1']);
        $this->assertEquals(123, $json['customProp2']);
        $this->assertEquals(['nested' => 'value'], $json['customProp3']);
    }

    public function testJsonSerialize(): void
    {
        $site = new Site(
            'site-id',
            'demo.dotcms.com',
            'a1de7e5c-d4b4-43ab-866c-98845673fb74',
            true,
            '/demo',
            false,
            false,
            true,
            ['customProp' => 'value']
        );

        $json = $site->jsonSerialize();

        $this->assertEquals('site-id', $json['identifier']);
        $this->assertEquals('demo.dotcms.com', $json['hostname']);
        $this->assertEquals('a1de7e5c-d4b4-43ab-866c-98845673fb74', $json['inode']);
        $this->assertTrue($json['working']);
        $this->assertEquals('/demo', $json['folder']);
        $this->assertFalse($json['locked']);
        $this->assertFalse($json['archived']);
        $this->assertTrue($json['live']);
        $this->assertEquals('value', $json['customProp']);
    }
}
