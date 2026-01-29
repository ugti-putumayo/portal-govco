<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InstitutionalContent;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class InstitutionalContentController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('institutionalcontent');
    }

    public function publicIndex(Request $request)
    {
        $contents = InstitutionalContent::where('is_active', 1)
                ->orderBy('order_index', 'asc')
                ->get();
        return view('public.governorate.entity', compact('contents'));
    }

    public function index(Request $request)
    {
        $query = InstitutionalContent::query();
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $field = $request->category ?? 'section';

            $allowedFields = ['section', 'content'];
            if (in_array($field, $allowedFields)) {
                $query->where($field, 'LIKE', "%$search%");
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('section', 'LIKE', "%$search%")
                    ->orWhere('content', 'LIKE', "%$search%");
                });
            }
        }

        $ic = $query->paginate(15)->withQueryString();
        return view('dashboard.administration.institutionalcontent.institutional-content', compact('ic'));
    }

    public function create()
    {
        return view('dashboard.institutionalcontent.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'section'     => 'required|string|max:100',
            'content'     => 'nullable|string'
        ]);

        $data['is_active'] = 1;
        $data['order_index'] = InstitutionalContent::max('order_index') + 1;
        $ic = InstitutionalContent::create($data);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Contenido creado exitosamente.',
                'content' => $ic
            ]);
        }

        return redirect()
            ->route('dashboard.institutionalcontent.index')
            ->with('success', 'Contenido creado correctamente.');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/uploads/tinymce', $filename);

            return response()->json([
                'location' => asset('storage/uploads/tinymce/' . $filename)
            ]);
        }

        return response()->json(['error' => 'No se subió imagen'], 400);
    }

    public function edit($id)
    {
        $ic = InstitutionalContent::findOrFail($id);
        return response()->json($ic);
    }

    public function update(Request $request, $id)
    {
        $ic = InstitutionalContent::findOrFail($id);
        $data = $request->validate([
            'section'     => 'required|string|max:100',
            'content'     => 'nullable|string',
            'order_index' => 'nullable|integer'
        ]);
        preg_match_all('/<img[^>]+src="[^"]*\/([^\/"]+\.(jpg|jpeg|png|gif))"/i', $ic->content ?? '', $oldMatches);
        $oldImages = $oldMatches[1] ?? [];
        preg_match_all('/<img[^>]+src="[^"]*\/([^\/"]+\.(jpg|jpeg|png|gif))"/i', $data['content'] ?? '', $newMatches);
        $newImages = $newMatches[1] ?? [];
        $unusedImages = array_diff($oldImages, $newImages);
        foreach ($unusedImages as $img) {
            $path = 'public/uploads/tinymce/' . $img;
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }
        $ic->update($data);
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Contenido actualizado exitosamente.',
                'content' => $ic
            ]);
        }
        return redirect()->route('dashboard.institutionalcontent.index')->with('success', 'Contenido actualizado correctamente.');
    }

    public function destroy($id)
    {
        $ic = InstitutionalContent::findOrFail($id);
        preg_match_all('/<img[^>]+src="[^"]*\/([^\/"]+\.(jpg|jpeg|png|gif))"/i', $ic->content ?? '', $matches);
        $images = $matches[1] ?? [];

        foreach ($images as $img) {
            $path = 'public/uploads/tinymce/' . $img;
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        $ic->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Contenido eliminado correctamente.']);
        }

        return redirect()->route('dashboard.institutionalcontent.index')->with('success', 'Contenido eliminado correctamente.');
    }
}