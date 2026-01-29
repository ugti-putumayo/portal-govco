<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contrac;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class ContracController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('contracs');
    }

    public function index()
    {
        $contracs = Contrac::paginate(15);
        return view('dashboard.publications.contracs', compact('contracs'));
    }

    public function show(Request $request, $id)
    {
        $contrac = Contrac::find($id);
        if (!$contrac) {
            return response()->json(['message' => 'Contrato no encontrado.'], 404);
        }
        return view('contracs.show', compact('contrac'));
    }

    public function create()
    {
        return view('dashboard.contracs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'contract_number' => 'required|string|max:255',
            'contractor'      => 'required|string|max:255'
        ]);
        $contrac = Contrac::create($request->all());
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Contrato creado con éxito',
                'area' => $contrac
            ]);
        }

        return redirect()->route('dashboard.contracs.index')->with('success', 'Contrato creado con éxito');
    }

    public function edit($id)
    {
        $contrac = Contrac::findOrFail($id);
        return response()->json($contrac);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'contract_number' => 'required|string|max:255',
            'contractor'      => 'required|string|max:255'
        ]);

        $contrac = Contrac::findOrFail($id);
        $contrac->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Contrato actualizado con éxito',
                'area' => $contrac
            ]);
        }

        return redirect()->route('dashboard.contracs.index')->with('success', 'Contrato actualizado con éxito');
    }

    public function destroy(Request $request, $id)
    {
        $contrac = Contrac::findOrFail($id);
        $contrac->delete();
        return response()->json(['message' => 'Contrato eliminado con éxito'], 200);
    }
}