<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Association;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class AssociationController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('association');
    }

    // Vista administrativa
    public function index()
    {
        $associations = Association::orderBy('created_at', 'desc')->paginate(20);
        return view('dashboard.administration.association', compact('associations'));
    }

    public function create()
    {
        return view('dashboard.associations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'classification' => 'required|string|max:100',
            'activity' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sccope' => 'nullable|string|max:100',
            'cellphone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('data/associations', 'public');
            $data['image'] = $path;
        }

        $association = Association::create($data);
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Asociación creada exitosamente.',
                'publication' => $association
            ]);
        }

        return redirect()->route('dashboard.association.index')->with('success', 'Asociación creada correctamente.');
    }

    public function edit($id)
    {
        $association = Association::findOrFail($id);
        return response()->json($association);
    }

    public function update(Request $request, Association $association)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'classification' => 'required|string|max:100',
            'activity' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sccope' => 'nullable|string|max:100',
            'cellphone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            if ($association->image && Storage::disk('public')->exists($association->image)) {
                Storage::disk('public')->delete($association->image);
            }
            $path = $request->file('image')->store('data/associations', 'public');
            $data['image'] = $path;
        }

        $association->update($data);
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Asociación creada exitosamente.',
                'publication' => $association
            ]);
        }
        return redirect()->route('dashboard.association.index')->with('success', 'Asociación actualizada correctamente.');
    }

    public function destroy(Association $association)
    {
        if ($association->image && Storage::disk('public')->exists($association->image)) {
            Storage::disk('public')->delete($association->image);
        }

        $association->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Informe eliminado correctamente.']);
        }
    }
}