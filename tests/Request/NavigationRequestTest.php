<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Request;

use Dotcms\PhpSdk\Request\NavigationRequest;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class NavigationRequestTest extends TestCase
{
    /**
     * Test that the constructor properly sets the default values
     */
    public function testConstructorSetsDefaultValues(): void
    {
        $request = new NavigationRequest();

        $this->assertEquals('/', $request->getPath());
        $this->assertEquals(1, $request->getDepth());
        $this->assertEquals(1, $request->getLanguageId());
    }

    /**
     * Test that the constructor properly sets custom values
     */
    public function testConstructorSetsCustomValues(): void
    {
        $request = new NavigationRequest('/about-us', 2, 3);

        $this->assertEquals('/about-us', $request->getPath());
        $this->assertEquals(2, $request->getDepth());
        $this->assertEquals(3, $request->getLanguageId());
    }

    /**
     * Test that the constructor throws an exception for empty path
     */
    public function testConstructorThrowsExceptionForEmptyPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Path must be a non-empty string');

        new NavigationRequest('');
    }

    /**
     * Test that the constructor throws an exception for invalid depth
     */
    public function testConstructorThrowsExceptionForInvalidDepth(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Depth must be a positive integer');

        new NavigationRequest('/', 0);
    }

    /**
     * Test that the constructor throws an exception for invalid language ID
     */
    public function testConstructorThrowsExceptionForInvalidLanguageId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Language ID must be a positive integer');

        new NavigationRequest('/', 1, 0);
    }

    /**
     * Test that buildPath returns the correct path
     */
    public function testBuildPathReturnsCorrectPath(): void
    {
        // Test with root path
        $request = new NavigationRequest();
        $this->assertEquals('/api/v1/nav/', $request->buildPath());

        // Test with custom path
        $request = new NavigationRequest('/about-us');
        $this->assertEquals('/api/v1/nav/about-us', $request->buildPath());

        // Test with path that already has a leading slash
        $request = new NavigationRequest('/products/');
        $this->assertEquals('/api/v1/nav/products', $request->buildPath());
    }

    /**
     * Test that buildQueryParams returns the correct parameters
     */
    public function testBuildQueryParamsReturnsCorrectParams(): void
    {
        // Test with default values
        $request = new NavigationRequest();
        $params = $request->buildQueryParams();
        $this->assertEquals([
            'depth' => 1,
            'languageId' => 1,
        ], $params);

        // Test with custom values
        $request = new NavigationRequest('/about-us', 2, 3);
        $params = $request->buildQueryParams();
        $this->assertEquals([
            'depth' => 2,
            'languageId' => 3,
        ], $params);
    }
}
