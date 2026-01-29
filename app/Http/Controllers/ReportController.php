<?php
namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Models\ReportType;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class ReportController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('report');
    }

    public function index(Request $request)
    {
        $ReportTypeId = $request->report_type_id;
        if ($ReportTypeId) {
            $filtered = Report::activeOfType($ReportTypeId)
                ->latest()
                ->get();

            return response()->json([
                'message' => 'Informes filtrados por tipo',
                'publications' => $filtered
            ]);
        }
        $reports = Report::with('report_type')->orderBy('created_at', 'desc')->paginate(15);
        $report_types = ReportType::all();
        return view('report.index', compact('reports', 'report_types'));
    }

    public function create(Request $request)
    {
        $ReportTypeId = $request->report_type_id;
        $report_types = ReportType::all();

        return view('report.create', compact('report_types', 'ReportTypeId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'report_type_id' => 'required|exists:type_publications,id',
            'year'           => 'required|integer',
            'description' => 'required|text',
            'document'    => 'nullable|file|mimes:pdf,docx|max:5120',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'state'       => 'nullable|boolean'
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['state'] = $request->state ?? 1;

        $dateFolder = now()->format('Y-m');

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store("reports/{$dateFolder}/images", 'public');
            $data['image'] = $imagePath;
        }

        if ($request->hasFile('document')) {
            $docPath = $request->file('document')->store("reports/{$dateFolder}/documents", 'public');
            $data['document'] = $docPath;
        }

        $report = Report::create($data);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Informe creado exitosamente.',
                'publication' => $report
            ]);
        }

        return redirect()->back()->with('success', 'Informe creado exitosamente.');
    }

    public function edit($id)
    {
        $report = Report::findOrFail($id);
        return response()->json($report);
    }

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $request->validate([
            'report_type_id' => 'required|exists:type_publications,id',
            'year'           => 'required|integer',
            'description'    => 'required|string',
            'document'       => 'nullable|file|mimes:pdf,docx|max:5120',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'state'          => 'nullable|boolean'
        ]);

        $data = $request->all();

        $dateFolder = now()->format('Y-m');

        // Reemplazar imagen si se sube una nueva
        if ($request->hasFile('image')) {
            if ($report->image && Storage::disk('public')->exists($report->image)) {
                Storage::disk('public')->delete($report->image);
            }

            $imagePath = $request->file('image')->store("reports/{$dateFolder}/images", 'public');
            $data['image'] = $imagePath;
        }

        // Reemplazar documento si se sube uno nuevo
        if ($request->hasFile('document')) {
            if ($report->document && Storage::disk('public')->exists($report->document)) {
                Storage::disk('public')->delete($report->document);
            }

            $docPath = $request->file('document')->store("reports/{$dateFolder}/documents", 'public');
            $data['document'] = $docPath;
        }

        $report->update($data);

        return redirect()->back()->with('success', 'Informe actualizado correctamente.');
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        if ($report->image && file_exists(storage_path("app/public/{$report->image}"))) {
            unlink(storage_path("app/public/{$report->image}"));
        }
        if ($report->document && file_exists(storage_path("app/public/{$report->document}"))) {
            unlink(storage_path("app/public/{$report->document}"));
        }

        $report->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Informe eliminado correctamente.']);
        }
    }
}