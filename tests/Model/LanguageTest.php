<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model;

use Dotcms\PhpSdk\Model\Language;
use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        // Test English (US)
        $enUs = new Language(
            id: 1,
            languageCode: 'en',
            countryCode: 'US',
            language: 'English',
            country: 'United States',
            isoCode: 'en-us'
        );

        $this->assertEquals(1, $enUs->id);
        $this->assertEquals('en', $enUs->languageCode);
        $this->assertEquals('US', $enUs->countryCode);
        $this->assertEquals('English', $enUs->language);
        $this->assertEquals('United States', $enUs->country);
        $this->assertEquals('en-us', $enUs->isoCode);

        // Test Spanish (Spain)
        $esEs = new Language(
            id: 2,
            languageCode: 'es',
            countryCode: 'ES',
            language: 'Spanish',
            country: 'Spain',
            isoCode: 'es-es'
        );

        $this->assertEquals(2, $esEs->id);
        $this->assertEquals('es', $esEs->languageCode);
        $this->assertEquals('ES', $esEs->countryCode);
        $this->assertEquals('Spanish', $esEs->language);
        $this->assertEquals('Spain', $esEs->country);
        $this->assertEquals('es-es', $esEs->isoCode);

        // Test French (France)
        $frFr = new Language(
            id: 3,
            languageCode: 'fr',
            countryCode: 'FR',
            language: 'French',
            country: 'France',
            isoCode: 'fr-fr'
        );

        $this->assertEquals(3, $frFr->id);
        $this->assertEquals('fr', $frFr->languageCode);
        $this->assertEquals('FR', $frFr->countryCode);
        $this->assertEquals('French', $frFr->language);
        $this->assertEquals('France', $frFr->country);
        $this->assertEquals('fr-fr', $frFr->isoCode);
    }
}
