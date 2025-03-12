<?php

namespace App\Controller;

use App\Service\DotCMSService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Dotcms\PhpSdk\Exception\DotCMSException;
use Dotcms\PhpSdk\Exception\HttpException;
use Dotcms\PhpSdk\Exception\ResponseException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
            
            if (!$pageAsset || !isset($pageAsset->page)) {
                throw new NotFoundHttpException('Page not found');
            }

            return $this->render('page.html.twig', [
                'pageAsset' => $pageAsset,
                'layout' => $pageAsset->layout ?? null,
                'page' => $pageAsset->page ?? null,
                'containers' => $pageAsset->containers ?? []
            ]);
        } catch (HttpException $e) {
            // Map HTTP errors to appropriate Symfony exceptions
            $statusCode = $e->getCode();
            throw match($statusCode) {
                401 => new UnauthorizedHttpException('Bearer', $e->getMessage(), $e),
                404 => new NotFoundHttpException($e->getMessage(), $e),
                400 => new BadRequestHttpException($e->getMessage(), $e),
                503 => new ServiceUnavailableHttpException(null, $e->getMessage(), $e),
                default => new NotFoundHttpException($e->getMessage(), $e)
            };
        } catch (ResponseException $e) {
            // Handle response parsing errors
            throw new BadRequestHttpException('Invalid response from DotCMS: ' . $e->getMessage(), $e);
        } catch (DotCMSException $e) {
            // Handle any other DotCMS specific errors
            throw new ServiceUnavailableHttpException(null, 'DotCMS Error: ' . $e->getMessage(), $e);
        } catch (\Exception $e) {
            // Handle any other unexpected errors
            throw new ServiceUnavailableHttpException(null, 'Unexpected error: ' . $e->getMessage(), $e);
        }
    }
}