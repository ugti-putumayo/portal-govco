<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class EntityController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('entities');
    }

    public function index()
    {
        $entities = Entity::paginate(15);
        return view('dashboard.administration.entities', compact('entities'));
    }

    public function show($id)
    {
        $entity = Entity::find($id);
        if (!$entity) {
            return response()->json(['message' => 'Entidad no encontrada'], 404);
        }
        return response()->json($entity);
    }

    public function create()
    {
        return view('dashboard.entities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250'
        ]);

        $entity = Entity::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Entidad creada con éxito',
                'entity' => $entity
            ]);
        }

        return redirect()->route('dashboard.entities.index')->with('success', 'Entidad creada con éxito');
    }

    public function edit($id)
    {
        $entity = Entity::findOrFail($id);
        return response()->json($entity);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:250'
        ]);

        $entity = Entity::findOrFail($id);
        $entity->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Entidad actualizada con éxito',
                'area' => $entity
            ]);
        }

        return redirect()->route('dashboard.entities.index')->with('success', 'Entidad actualizada con éxito');
    }

    public function destroy(Request $request, $id)
    {
        $entity = Entity::findOrFail($id);
        $entity->delete();
        return response()->json(['message' => 'Entidad eliminada con éxito'], 200);
    }
}
