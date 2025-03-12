<?php

namespace App\Controller;

use App\Service\DotCMSService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CatchAllController extends AbstractController
{
    private DotCMSService $dotCMSService;
    
    public function __construct(DotCMSService $dotCMSService)
    {
        $this->dotCMSService = $dotCMSService;
    }

    public function show(string $path = ''): Response
    {
        try {
            $request = $this->container->get('request_stack')->getCurrentRequest();
            $actualPath = $request->getPathInfo();
            $pageAsset = $this->dotCMSService->getPage($actualPath);
            $page = $pageAsset->page;
            
            return $this->render('page.html.twig', [
                'pageAsset' => $pageAsset,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}