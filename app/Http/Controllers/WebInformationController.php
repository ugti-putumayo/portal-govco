<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class WebInformationController extends Controller
{
    public function index()
    {
        return view('public.web-information');
    }

    public function otherIndex()
    {
        return view('public.web-information-others');
    }
}
