<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{
    /**
     * Handle the SPA rendering
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Here you can add your library logic to get data
        // $data = YourLibrary::getData();
        
        return view('app');
    }
}
