<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlantOfficial;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PlantOfficialExport;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class PlantOfficialController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        $this->authorizeCrud('plantofficials');
    }

    public function index(Request $request)
    {
        $query = PlantOfficial::query();
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $field = $request->category ?? 'fullname';

            $allowedFields = ['document_number', 'fullname', 'dependency'];
            if (in_array($field, $allowedFields)) {
                $query->where($field, 'LIKE', "%$search%");
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('fullname', 'LIKE', "%$search%")
                    ->orWhere('document_number', 'LIKE', "%$search%")
                    ->orWhere('dependency', 'LIKE', "%$search%");
                });
            }
        }

        $plantofficial = $query->paginate(15)->withQueryString();
        return view('dashboard.publications.plantofficials', compact('plantofficial'));
    }

    public function show(Request $request, $id)
    {
        $plantofficial = PlantOfficial::find($id);
        if (!$plantofficial) {
            return response()->json(['message' => 'Funcionario no encontrado.'], 404);
        }
        return view('plantofficials.show', compact('plantofficial'));
    }

    public function create()
    {
        return view('dashboard.plantofficials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname'     => 'required|string|max:100'
        ]);
        $plantofficial = PlantOfficial::create($request->all());
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Funcionario creado con éxito',
                'plantofficial' => $plantofficial
            ]);
        }

        return redirect()->route('dashboard.plantofficials.index')->with('success', 'Funcionario creado con éxito');
    }

    public function edit($id)
    {
        $plantofficial = PlantOfficial::findOrFail($id);
        return response()->json($plantofficial);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fullname'     => 'required|string|max:100'
        ]);

        $plantofficial = PlantOfficial::findOrFail($id);
        $plantofficial->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Funcionario actualizado con éxito',
                'plantofficial' => $plantofficial
            ]);
        }

        return redirect()->route('dashboard.plantofficials.index')->with('success', 'Funcionario actualizado con éxito');
    }

    public function destroy(Request $request, $id)
    {
        $plantofficial = PlantOfficial::findOrFail($id);
        $plantofficial->delete();
        return response()->json(['message' => 'Funcionario eliminado con éxito'], 200);
    }

    public function exportPlantOfficial(Request $request)
    {
        $year = $request->input('year_plantofficial');
        $month = $request->input('month_plantofficial');

        if (!$year || !$month) {
            return redirect()->back()->with('error', 'Debe seleccionar año y mes.');
        }

        return Excel::download(new PlantOfficialExport($year, $month), "Plant_Officials_{$month}_{$year}.xlsx");
    }
}