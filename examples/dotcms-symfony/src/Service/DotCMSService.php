<?php

namespace App\Service;

use Dotcms\PhpSdk\DotCMSClient;
use Dotcms\PhpSdk\Model\PageAsset;

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
}