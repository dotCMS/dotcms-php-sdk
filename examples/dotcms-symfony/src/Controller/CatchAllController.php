<?php

namespace App\Controller;

use App\Service\DotCMSService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException as SymfonyHttpException;
use Dotcms\PhpSdk\Exception\HttpException;
use Dotcms\PhpSdk\Exception\ResponseException;

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
            
            $languageId = $request->query->get('language_id');
            $mode = $request->query->get('mode');
            $personaId = $request->query->get('personaId');
            $publishDate = $request->query->get('publishDate');
            
            $pageAsset = $this->dotCMSService->getPage(
                $actualPath,
                $languageId ? (int)$languageId : null,
                $mode,
                $personaId,
                $publishDate
            );
            
            if (!$pageAsset || !isset($pageAsset->page)) {
                throw new NotFoundHttpException('Page not found');
            }
            
            // Get navigation with depth of 2 (top level + one level of children)
            $navigation = $this->dotCMSService->getNavigation('/', 2);

            return $this->render('page.html.twig', [
                'pageAsset' => $pageAsset,
                'layout' => $pageAsset->layout ?? null,
                'page' => $pageAsset->page ?? null,
                'containers' => $pageAsset->containers ?? [],
                'navigation' => $navigation
            ]);
        } catch (HttpException $e) {
            // Map HTTP errors to appropriate Symfony exceptions
            throw match($e->getCode()) {
                400 => new BadRequestHttpException($e->getMessage(), $e),
                401 => new UnauthorizedHttpException('Bearer', $e->getMessage(), $e),
                404 => new NotFoundHttpException($e->getMessage(), $e),
                500 => new SymfonyHttpException(500, $e->getMessage(), $e),
                503 => new ServiceUnavailableHttpException(null, $e->getMessage(), $e),
                default => new ServiceUnavailableHttpException(null, $e->getMessage(), $e)
            };
        } catch (ResponseException $e) {
            // ResponseException indicates invalid/missing data in the response
            // This is a server error since the response format is controlled by DotCMS
            throw new ServiceUnavailableHttpException(null, $e->getMessage(), $e);
        }
    }
}