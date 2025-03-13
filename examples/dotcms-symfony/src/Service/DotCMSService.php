<?php

namespace App\Service;

use Dotcms\PhpSdk\DotCMSClient;
use Dotcms\PhpSdk\Model\PageAsset;
use Dotcms\PhpSdk\Model\NavigationItem;

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
    
    // Add any common DotCMS operations here as methods
    // For example:
    public function getPage(string $path): PageAsset
    {
        $pageRequest = $this->client->createPageRequest($path, 'json');
        
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