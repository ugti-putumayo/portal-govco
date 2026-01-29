<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Dependency;
use App\Models\Publication;
use App\Models\Event;

class ApiController extends Controller
{
    public function getMenu()
    {
        $menus = Menu::with('submenus')->orderBy('order')->get();
        return response()->json($menus);
    }

    public function getDependencies()
    {
        return Dependency::all();
    }

    public function getPublications()
    {
        return Publication::all();
    }
    
    public function getEvents()
    {
        return Event::all();
    }
}