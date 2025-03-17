<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Dotcms\PhpSdk\DotCMSClient;

class AppController extends Controller
{
    /**
     * The DotCMS client instance.
     */
    protected $dotCMSClient;
    
    /**
     * Create a new controller instance.
     */
    public function __construct(DotCMSClient $dotCMSClient)
    {
        $this->dotCMSClient = $dotCMSClient;
    }
    
    /**
     * Handle the SPA rendering with dotCMS page data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            // Get the current path from the request
            $path = $request->path();
            $path = $path === '/' ? '/' : '/' . $path;

            // Create a page request for the current path
            $pageRequest = $this->dotCMSClient->createPageRequest($path, 'json');
            
            // Get the page data
            $pageAsset = $this->dotCMSClient->getPage($pageRequest);
            
            // Create a navigation request with depth=2
            $navRequest = $this->dotCMSClient->createNavigationRequest('/', 2);
            
            // Get the navigation
            $nav = $this->dotCMSClient->getNavigation($navRequest);

            // Check for entity wrapper in the response
            if (isset($pageAsset->entity)) {
                // Some dotCMS versions return data in an 'entity' wrapper
                $page = $pageAsset;
            } else {
                // Standard structure already expected by our templates
                $page = $pageAsset;
            }

            // Log the structure for debugging
            Log::debug('DotCMS Page Structure', [
                'hasEntity' => isset($pageAsset->entity) ? 'yes' : 'no',
                'hasLayout' => isset($page->layout) ? 'yes' : 'no',
                'hasContainers' => isset($page->containers) ? 'yes' : 'no'
            ]);

            // Pass the data to the view
            return view('page', [
                'pageAsset' => $page,
                'navigation' => $nav
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('dotCMS API Error: ' . $e->getMessage());
            
            // Rethrow the exception to let Laravel handle it
            throw $e;
        }
    }
}
