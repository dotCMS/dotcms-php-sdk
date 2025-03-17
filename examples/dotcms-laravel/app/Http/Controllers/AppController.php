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
            $page = $this->dotCMSClient->getPage($pageRequest);
            
            // Create a navigation request with depth=2
            $navRequest = $this->dotCMSClient->createNavigationRequest('/', 2);
            
            // Get the navigation
            $nav = $this->dotCMSClient->getNavigation($navRequest);

            // Pass the data to the view
            return view('app', [
                'page' => $page,
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
