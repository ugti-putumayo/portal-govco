<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contractor;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ContractorsImport;
use App\Exports\ContractorsExport;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class ContractorController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('contractors');
    }

    public function index(Request $request)
    {
        $query = Contractor::query();
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $field = $request->category ?? 'contractor';

            $allowedFields = ['contractor', 'contract_number', 'object', 'supervision', 'dependency'];
            if (in_array($field, $allowedFields)) {
                $query->where($field, 'LIKE', "%$search%");
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('contractor', 'LIKE', "%$search%")
                    ->orWhere('contract_number', 'LIKE', "%$search%")
                    ->orWhere('object', 'LIKE', "%$search%")
                    ->orWhere('supervision', 'LIKE', "%$search%")
                    ->orWhere('dependency', 'LIKE', "%$search%");
                });
            }
        }

        $contractors = $query->paginate(15)->withQueryString();
        return view('dashboard.publications.contractors', compact('contractors'));
    }

    public function show(Request $request, $id)
    {
        $contractor = Contractor::find($id);
        if (!$contractor) {
            return response()->json(['message' => 'Contratista no encontrado.'], 404);
        }
        return view('dashboard.publications.contractors', compact('contractor'));
    }

    public function create()
    {
        return view('dashboard.publications.contractors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'contract_number' => 'required|string|max:255',
            'contractor'      => 'required|string|max:255'
        ]);
        $contractor = Contractor::create($request->all());
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Contratista creado con éxito',
                'area' => $contractor
            ]);
        }

        return redirect()->route('dashboard.publications.contractors.index')->with('success', 'Contratista creado con éxito');
    }

    public function edit($id)
    {
        $contractor = Contractor::findOrFail($id);
        return response()->json($contractor);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'contract_number' => 'required|string|max:255',
            'contractor'      => 'required|string|max:255'
        ]);

        $contractor = Contractor::findOrFail($id);
        $contractor->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Contratista actualizado con éxito',
                'contrator' => $contractor
            ]);
        }

        return redirect()->route('dashboard.publications.contractors.index')->with('success', 'Contratista actualizado con éxito');
    }

    public function destroy(Request $request, $id)
    {
        $contractor = Contractor::findOrFail($id);
        $contractor->delete();
        return response()->json(['message' => 'Contratista eliminado con éxito'], 200);
    }

    public function import(Request $request)
    {
        if (!$request->hasFile('excel_file')) {
            return response()->json(['message' => 'Archivo no cargado.'], 400);
        }

        $file = $request->file('excel_file');

        try {
            Excel::import(new ContractorsImport, $file);
            return response()->json(['message' => 'Archivo importado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al procesar el archivo: ' . $e->getMessage()], 500);
        }
    }

    public function exportContractors(Request $request)
    {
        $year = $request->input('year_contract');
        $month = $request->input('month_contract');

        if (!$year || !$month) {
            return redirect()->back()->with('error', 'Debe seleccionar año y mes.');
        }

        return Excel::download(new ContractorsExport($year, $month), "Contratistas_{$month}_{$year}.xlsx");
    }
}