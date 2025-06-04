<?php

namespace App\Service;

use Dotcms\PhpSdk\DotCMSClient;
use Dotcms\PhpSdk\Model\Page\PageAsset;
use Dotcms\PhpSdk\Model\Content\NavigationItem;

class DotCMSService
{
    private DotCMSClient $client;
    
    public function __construct(DotCMSClient $client)
    {
        $this->client = $client;
    }
    
    public function getClient(): DotCMSClient
    {
        return $this->client;
    }
    
    /**
     * Get a page from DotCMS
     *
     * @param string $path The path of the page
     * @param int|null $languageId The language ID
     * @param string|null $mode The mode (live, edit, preview)
     * @param string|null $personaId The persona ID
     * @param string|null $publishDate The publish date
     * @return PageAsset
     */
    public function getPage(
        string $path,
        ?int $languageId = null,
        ?string $mode = null,
        ?string $personaId = null,
        ?string $publishDate = null
    ): PageAsset {
        $pageRequest = $this->client->createPageRequest($path, 'json');
        
        if ($languageId) {
            $pageRequest = $pageRequest->withLanguageId($languageId);
        }
        if ($mode) {
            $pageRequest = $pageRequest->withMode($mode);
        }
        if ($personaId) {
            $pageRequest = $pageRequest->withPersonaId($personaId);
        }
        if ($publishDate) {
            $pageRequest = $pageRequest->withPublishDate($publishDate);
        }
        
        return $this->client->getPage($pageRequest);
    }
    
    /**
     * Get navigation items from DotCMS
     *
     * @param string $path The root path to begin traversing the folder tree
     * @param int $depth The depth of the folder tree to return
     * @param int $languageId The language ID of content to return
     * @return NavigationItem The navigation item with optional children
     */
    public function getNavigation(string $path = '/', int $depth = 1, int $languageId = 1): NavigationItem
    {
        $navRequest = $this->client->createNavigationRequest($path, $depth, $languageId);
        
        return $this->client->getNavigation($navRequest);
    }
}