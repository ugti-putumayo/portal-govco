<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntityService;
use App\Models\SliderImage;
use App\Models\Publication;
use App\Models\TypePublication;

class HomeController extends Controller
{
    public function index()
    {
        $typeNews = TypePublication::where('name', 'Noticias')->first();
        if (!$typeNews) {
            abort(404, 'Tipo de publicación "Noticias" no encontrado');
        }
        $publications = Publication::where('type_id', $typeNews->id)
                                    ->where('state', 1)
                                    ->with('type')
                                    ->orderBy('date', 'desc')
                                    ->paginate(5);
        
        $callsjob = Publication::where('type_id', 4)
                            ->where('state', 1)
                            ->orderBy('date', 'desc')
                            ->take(6)
                            ->get();

        $images = SliderImage::where('status', 1)
            ->orderByDesc('order')
            ->get();

        $tramiteServices = EntityService::where('status', 1)
        ->where('type_id', 2)
        ->orderBy('order_index', 'asc')
        ->get();

        $citizenServices = EntityService::where('status', 1)
            ->where('type_id', 1)
            ->orderBy('order_index', 'asc')
            ->get();

        $publicationsAnti = Publication::where('type_id', 7)
            ->where('state', 1)
            ->orderBy('date', 'desc')
            ->take(6)
            ->get();

        return view('public.home', compact('publications', 'publicationsAnti', 'callsjob', 'images', 'tramiteServices', 'citizenServices'));
    }

    public function indexPublicPublicationAll(Request $request){
        $typeId = $request->query('type', 2);
        $allNews = Publication::where('type_id', $typeId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('public.home.publications-detail', compact('allNews'));
    }

    public function indexPublicPublications(Request $request, $typeFilter = null)
    {
        $query = Publication::with('type');

        if ($request->has('search') && !empty($request->search)) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('type') && !empty($request->type)) {
            $query->where('type_id', $request->type);
        } elseif ($typeFilter) {
            $query->where('type_id', $typeFilter);
        }

        $publications = $query->orderBy('date', 'desc')->paginate(10);
        $types = TypePublication::all();

        return view('public.home.publications', compact('publications', 'types'));
    }

    public function showPublicPublicationByType(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);
        $publication->increment('views');
        $typeId = $request->query('type', $publication->type_id);

        $previous = Publication::where('type_id', $typeId)
            ->where('id', '<', $id)
            ->orderBy('id', 'desc')
            ->first();

        $next = Publication::where('type_id', $typeId)
            ->where('id', '>', $id)
            ->orderBy('id')
            ->first();

        $otherPublications = Publication::where('type_id', $typeId)
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('public.home.publication-detail', compact(
            'publication',
            'previous',
            'next',
            'otherPublications',
            'typeId'
        ));
    }
}