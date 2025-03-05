<?php

namespace Dotcms\PhpSdk\Tests\Request;

use Dotcms\PhpSdk\Request\PageRequest;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PageRequestTest extends TestCase
{
    /**
     * Valid UUID for testing
     */
    private const VALID_UUID = '48190c8c-42c4-46af-8d1a-0cd5db894797';

    /**
     * Test that the constructor properly sets the format and page path
     */
    public function testConstructorSetsFormatAndPagePath(): void
    {
        $request = new PageRequest('/test', 'json');

        $this->assertEquals('json', $request->getFormat());
        $this->assertEquals('/test', $request->getPagePath());
    }

    /**
     * Test that the constructor uses the default format when not specified
     */
    public function testConstructorUsesDefaultFormat(): void
    {
        $request = new PageRequest('/test');

        $this->assertEquals('json', $request->getFormat());
    }

    /**
     * Test that the constructor throws an exception for invalid format
     */
    public function testConstructorThrowsExceptionForInvalidFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid format "invalid". Valid formats are: json, render');

        new PageRequest('/test', 'invalid');
    }

    /**
     * Test that the constructor throws an exception for empty page path
     */
    public function testConstructorThrowsExceptionForEmptyPagePath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Page path cannot be empty');

        new PageRequest('');
    }

    /**
     * Test that the constructor adds a leading slash to page path if missing
     */
    public function testConstructorAddsLeadingSlashToPagePath(): void
    {
        $request = new PageRequest('test');

        $this->assertEquals('/test', $request->getPagePath());
    }

    /**
     * Test that the constructor appends 'index' to page path if it ends with a slash
     */
    public function testConstructorAppendsIndexToPagePathEndingWithSlash(): void
    {
        $request = new PageRequest('/test/');

        $this->assertEquals('/test/index', $request->getPagePath());
    }

    /**
     * Test that withMode sets the mode and returns a new instance
     */
    public function testWithModeReturnsNewInstance(): void
    {
        $request1 = new PageRequest('/test');
        $request2 = $request1->withMode('WORKING');

        $this->assertNotSame($request1, $request2);
        $this->assertNull($request1->getMode());
        $this->assertEquals('WORKING', $request2->getMode());
    }

    /**
     * Test that withMode throws an exception for invalid mode
     */
    public function testWithModeThrowsExceptionForInvalidMode(): void
    {
        $request = new PageRequest('/test');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid mode "INVALID". Valid modes are: LIVE, WORKING, EDIT_MODE');

        $request->withMode('INVALID');
    }

    /**
     * Test that withHostId sets the host ID and returns a new instance
     */
    public function testWithHostIdReturnsNewInstance(): void
    {
        $request1 = new PageRequest('/test');
        $request2 = $request1->withHostId(self::VALID_UUID);

        $this->assertNotSame($request1, $request2);
        $this->assertNull($request1->getHostId());
        $this->assertEquals(self::VALID_UUID, $request2->getHostId());
    }

    /**
     * Test that withHostId throws an exception for invalid UUID
     */
    public function testWithHostIdThrowsExceptionForInvalidUuid(): void
    {
        $request = new PageRequest('/test');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid host ID "not-a-uuid". Host ID must be a valid UUID.');

        $request->withHostId('not-a-uuid');
    }

    /**
     * Test that withLanguageId sets the language ID and returns a new instance
     */
    public function testWithLanguageIdReturnsNewInstance(): void
    {
        $request1 = new PageRequest('/test');
        $request2 = $request1->withLanguageId(1);

        $this->assertNotSame($request1, $request2);
        $this->assertNull($request1->getLanguageId());
        $this->assertEquals(1, $request2->getLanguageId());
    }

    /**
     * Test that withLanguageId throws an exception for non-positive integer
     */
    public function testWithLanguageIdThrowsExceptionForNonPositiveInteger(): void
    {
        $request = new PageRequest('/test');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid language ID "-1". Language ID must be a positive integer.');

        $request->withLanguageId(-1);
    }

    /**
     * Test that withPersonaId sets the persona ID and returns a new instance
     */
    public function testWithPersonaIdReturnsNewInstance(): void
    {
        $request1 = new PageRequest('/test');
        $request2 = $request1->withPersonaId('persona123');

        $this->assertNotSame($request1, $request2);
        $this->assertNull($request1->getPersonaId());
        $this->assertEquals('persona123', $request2->getPersonaId());
    }

    /**
     * Test that withPersonaId throws an exception for empty string
     */
    public function testWithPersonaIdThrowsExceptionForEmptyString(): void
    {
        $request = new PageRequest('/test');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Persona ID cannot be empty');

        $request->withPersonaId('');
    }

    /**
     * Test that withFireRules sets the fire rules flag and returns a new instance
     */
    public function testWithFireRulesReturnsNewInstance(): void
    {
        $request1 = new PageRequest('/test');
        $request2 = $request1->withFireRules(true);

        $this->assertNotSame($request1, $request2);
        $this->assertNull($request1->getFireRules());
        $this->assertTrue($request2->getFireRules());
    }

    /**
     * Test that withDepth sets the depth and returns a new instance
     */
    public function testWithDepthReturnsNewInstance(): void
    {
        $request1 = new PageRequest('/test');
        $request2 = $request1->withDepth(2);

        $this->assertNotSame($request1, $request2);
        $this->assertNull($request1->getDepth());
        $this->assertEquals(2, $request2->getDepth());
    }

    /**
     * Test that withDepth throws an exception for invalid depth
     */
    public function testWithDepthThrowsExceptionForInvalidDepth(): void
    {
        $request = new PageRequest('/test');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid depth "4". Depth must be between 0 and 3');

        $request->withDepth(4);
    }

    /**
     * Test that buildPath returns the correct path
     */
    public function testBuildPathReturnsCorrectPath(): void
    {
        $request = new PageRequest('/test');

        $this->assertEquals('/api/v1/page/json/test', $request->buildPath());
    }

    /**
     * Test that buildQueryParams returns empty array when no params are set
     */
    public function testBuildQueryParamsReturnsEmptyArrayWhenNoParamsSet(): void
    {
        $request = new PageRequest('/test');

        $this->assertEquals([], $request->buildQueryParams());
    }

    /**
     * Test that buildQueryParams returns correct params when all params are set
     */
    public function testBuildQueryParamsReturnsCorrectParamsWhenAllParamsSet(): void
    {
        $request = new PageRequest('/test');
        $request = $request
            ->withMode('WORKING')
            ->withHostId(self::VALID_UUID)
            ->withLanguageId(1)
            ->withPersonaId('persona123')
            ->withFireRules(true)
            ->withDepth(2);

        $expectedParams = [
            'mode' => 'WORKING',
            'host_id' => self::VALID_UUID,
            'language_id' => 1,
            'com.dotmarketing.persona.id' => 'persona123',
            'fireRules' => 'true',
            'depth' => 2,
        ];

        $this->assertEquals($expectedParams, $request->buildQueryParams());
    }

    /**
     * Test method chaining works correctly
     */
    public function testMethodChainingWorksCorrectly(): void
    {
        $request = (new PageRequest('/test'))
            ->withMode('WORKING')
            ->withHostId(self::VALID_UUID)
            ->withLanguageId(1)
            ->withPersonaId('persona123')
            ->withFireRules(true)
            ->withDepth(2);

        $this->assertEquals('json', $request->getFormat());
        $this->assertEquals('/test', $request->getPagePath());
        $this->assertEquals('WORKING', $request->getMode());
        $this->assertEquals(self::VALID_UUID, $request->getHostId());
        $this->assertEquals(1, $request->getLanguageId());
        $this->assertEquals('persona123', $request->getPersonaId());
        $this->assertTrue($request->getFireRules());
        $this->assertEquals(2, $request->getDepth());
    }
}
