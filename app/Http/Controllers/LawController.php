<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Law;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class LawController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('laws');
    }

    public function index(Request $request)
    {
        $query = Law::query();
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $field = $request->category ?? 'number';

            $allowedFields = ['number', 'name', 'topic'];
            if (in_array($field, $allowedFields)) {
                $query->where($field, 'LIKE', "%$search%");
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('number', 'LIKE', "%$search%")
                    ->orWhere('name', 'LIKE', "%$search%")
                    ->orWhere('topic', 'LIKE', "%$search%");
                });
            }
        }

        $laws = $query->paginate(15)->withQueryString();
        return view('publication.laws', compact('laws'));
    }

    public function show(Request $request, $id)
    {
        $law = Law::find($id);
        if (!$law) {
            return response()->json(['message' => 'Ley no encontrada.'], 404);
        }
        return view('laws.show', compact('law'));
    }

    public function create()
    {
        return view('dashboard.laws.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|string|max:50',
            'name'   => 'required|string|max:250'
        ]);
        $law = Law::create($request->all());
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Ley creada con éxito',
                'law' => $law
            ]);
        }

        return redirect()->route('dashboard.laws.index')->with('success', 'Ley creada con éxito');
    }

    public function edit($id)
    {
        $law = Law::findOrFail($id);
        return response()->json($law);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'number' => 'required|string|max:50',
            'name'   => 'required|string|max:250'
        ]);
        $law = Law::findOrFail($id);
        $law->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Ley actualizada con éxito',
                'law' => $law
            ]);
        }

        return redirect()->route('dashboard.laws.index')->with('success', 'Ley actualizada con éxito');
    }

    public function destroy(Request $request, $id)
    {
        $law = Law::findOrFail($id);
        $law->delete();
        return response()->json(['message' => 'Ley eliminada con éxito'], 200);
    }
}