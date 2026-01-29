<?php
namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;
use App\Models\Publications;
use App\Models\TypePublication;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class PublicationController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('publication');
    }

    public function index(Request $request)
    {
        $typeId = $request->type_id;
        if ($typeId) {
            $filtered = Publication::activeOfType($typeId)
                ->latest()
                ->get();

            return response()->json([
                'message' => 'Publicaciones filtradas por tipo',
                'publications' => $filtered
            ]);
        }
        $publications = Publication::with('type')->orderBy('date', 'desc')->paginate(15);
        $types = TypePublication::all();
        return view('dashboard.publications.publication-general', compact('publications', 'types'));
    }

    public function create(Request $request)
    {
        $typeId = $request->type_id;
        $types = TypePublication::all();

        return view('publications.create', compact('types', 'typeId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:250',
            'description' => 'required|string',
            'type_id'     => 'required|exists:type_publications,id',
            'state'       => 'nullable|boolean',
            'date'        => 'nullable|date',
            'date_start'  => 'nullable|date',
            'date_end'    => 'nullable|date',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'document'    => 'nullable|file|mimes:pdf,docx|max:5120',
            //'link'        => 'required|string|max:255',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['state'] = $request->state ?? 1;

        $dateFolder = now()->format('Y-m'); // Ej: 2025-04

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store("publications/{$dateFolder}/images", 'public');
            $data['image'] = $imagePath;
        }

        if ($request->hasFile('document')) {
            $docPath = $request->file('document')->store("publications/{$dateFolder}/documents", 'public');
            $data['document'] = $docPath;
        }

        $publication = Publication::create($data);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Publicación creada exitosamente.',
                'publication' => $publication
            ]);
        }

        return redirect()->back()->with('success', 'Publicación creada exitosamente.');
    }

    public function edit($id)
    {
        $publication = Publication::findOrFail($id);
        return response()->json($publication);
    }

    public function update(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:250',
            'description' => 'required|string',
            'state'       => 'nullable|boolean',
            'date'        => 'nullable|date',
            'date_start'  => 'nullable|date',
            'date_end'    => 'nullable|date',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'document'    => 'nullable|file|mimes:pdf,docx|max:5120',
            'link'        => 'required|string|max:255',
        ]);

        $data = $request->all();

        // Imagen nueva
        if ($request->hasFile('image')) {
            if ($publication->image && file_exists(public_path('img/publications/' . $publication->image))) {
                unlink(public_path('img/publications/' . $publication->image));
            }

            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('img/publications'), $filename);
            $data['image'] = $filename;
        }

        // Documento nuevo
        if ($request->hasFile('document')) {
            if ($publication->document && file_exists(public_path('docs/publications/' . $publication->document))) {
                unlink(public_path('docs/publications/' . $publication->document));
            }

            $filename = time() . '_' . $request->file('document')->getClientOriginalName();
            $request->file('document')->move(public_path('docs/publications'), $filename);
            $data['document'] = $filename;
        }

        $publication->update($data);

        return redirect()->back()->with('success', 'Publicación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $publication = Publication::findOrFail($id);

        if ($publication->image && file_exists(storage_path("app/public/{$publication->image}"))) {
            unlink(storage_path("app/public/{$publication->image}"));
        }
        if ($publication->document && file_exists(storage_path("app/public/{$publication->document}"))) {
            unlink(storage_path("app/public/{$publication->document}"));
        }

        $publication->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Publicación eliminada correctamente.']);
        }
    }

    public function statisticals(Request $request)
    {
        $search = trim($request->get('q', ''));
        $from   = $request->get('from');
        $to     = $request->get('to');

        $publications = Publication::with('type')
            ->activeOfType(6)
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($from, fn($q) => $q->whereDate('date_start', '>=', $from))
            ->when($to,   fn($q) => $q->whereDate('date_end',   '<=', $to))
            ->orderByDesc('date')
            ->paginate(12)
            ->withQueryString();

        return view('public.transparency.open-data.statistical-information-management', compact('publications', 'search', 'from', 'to'));
    }
}