<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Dotcms\PhpSdk\Config\Config;
use Dotcms\PhpSdk\DotCMSClient;

class AppController extends Controller
{
    /**
     * Handle the SPA rendering with dotCMS page data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Create dotCMS client configuration
        $config = new Config(
            host: env('DOTCMS_HOST', 'https://demo.dotcms.com'),
            apiKey: env('DOTCMS_API_TOKEN', '')
        );

        // Create dotCMS client
        $client = new DotCMSClient($config);

        try {
            // Get the current path from the request
            $path = $request->path();
            $path = $path === '/' ? '/' : '/' . $path;

            // Create a page request for the current path
            $pageRequest = $client->createPageRequest($path);
            
            // Get the page data
            $page = $client->getPage($pageRequest);
            
            // Create a navigation request with depth=2
            $navRequest = $client->createNavigationRequest('/', 2);
            
            // Get the navigation
            $nav = $client->getNavigation($navRequest);

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
