<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntitySetting;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class EntitySettingController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('settings');
    }

    public function index()
    {
        $settings = EntitySetting::all();
        return view('dashboard.administration.entity-settings', compact('settings'));
    }

    public function create()
    {
        return view('dashboard.settings.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'entity_name'          => 'required|string|max:200',
            'entity_acronym'       => 'nullable|string|max:10',
            'document_number'      => 'required|string|max:20',
            'address'              => 'required|string|max:200',
            'phone'                => 'nullable|string|max:20',
            'email'                => 'nullable|email|max:150',
            'image'                => 'nullable|image|mimes:jpg,jpeg,png|max:255',
            'department'           => 'nullable|string|max:50',
            'city'                 => 'nullable|string|max:50',
            'website'              => 'nullable|string|max:250',
            'legal_representative' => 'nullable|url|max:100',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('data/associations', 'public');
            $data['image'] = $path;
        }

        $settings = EntitySetting::create($data);
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Configuración creada exitosamente.',
                'settings' => $settings
            ]);
        }

        return redirect()->route('dashboard.association.index')->with('success', 'Asociación creada correctamente.');
    }

    public function edit($id)
    {
        $setting = EntitySetting::findOrFail($id);
        return response()->json($setting);
    }

    public function update(Request $request, EntitySetting $entitysetting)
    {
        $data = $request->validate([
            'entity_name'          => 'required|string|max:200',
            'entity_acronym'       => 'nullable|string|max:10',
            'document_number'      => 'required|string|max:20',
            'address'              => 'required|string|max:200',
            'phone'                => 'nullable|string|max:20',
            'email'                => 'nullable|email|max:150',
            'image'                => 'nullable|image|mimes:jpg,jpeg,png|max:255',
            'department'           => 'nullable|string|max:50',
            'city'                 => 'nullable|string|max:50',
            'website'              => 'nullable|string|max:250',
            'legal_representative' => 'nullable|url|max:100',
        ]);

        if ($request->hasFile('image')) {
            if ($entitysetting->image && Storage::disk('public')->exists($entitysetting->image)) {
                Storage::disk('public')->delete($entitysetting->image);
            }
            $path = $request->file('image')->store('data/settings', 'public');
            $data['image'] = $path;
        }

        $entitysetting->update($data);
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Configuración creada exitosamente.',
                'settings' => $entitysetting
            ]);
        }
        return redirect()->route('dashboard.settings.index')->with('success', 'Configuración actualizada correctamente.');
    }

    public function destroy(EntitySetting $entitysetting)
    {
        if ($entitysetting->image && Storage::disk('public')->exists($entitysetting->image)) {
            Storage::disk('public')->delete($entitysetting->image);
        }

        $entitysetting->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Configuración eliminada correctamente.']);
        }
    }

    public function entitysettings()
    {
        $settings = EntitySetting::first();
        return response()->json($settings);
    }
}