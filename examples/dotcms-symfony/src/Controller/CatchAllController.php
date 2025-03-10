<?php

namespace App\Controller;

use App\Service\DotCMSService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CatchAllController extends AbstractController
{
    private DotCMSService $dotCMSService;
    
    public function __construct(DotCMSService $dotCMSService)
    {
        $this->dotCMSService = $dotCMSService;
    }

    public function show(string $path = ''): Response
    {
        // Use the actual request path instead of the route parameter if needed
        // This handles the root path case better
        $actualPath = $this->container->get('request_stack')->getCurrentRequest()->getPathInfo();
        $pageAsset = $this->dotCMSService->getPage($actualPath);
        $page = $pageAsset->page;
        $title = $page['friendlyName'];
        
        return new Response(
            '<html><body><h1>' . htmlspecialchars($title) . '</h1></body></html>'
        );
    }
}