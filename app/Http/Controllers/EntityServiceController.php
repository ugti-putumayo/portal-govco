<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntityService;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class EntityServiceController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        $this->authorizeCrud('entityservice');
    }

    public function index(Request $request)
    {
        $query = EntityService::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $field = $request->category ?? 'section';

            $allowedFields = ['title', 'description'];
            if (in_array($field, $allowedFields)) {
                $query->where($field, 'LIKE', "%$search%");
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
                });
            }
        }

        $service = $query->paginate(15)->withQueryString();

        $icons = collect(glob(public_path('icon/*.svg')))
            ->map(fn($path) => basename($path));

        return view('dashboard.administration.entityservice.entity-service', compact('service', 'icons'));
    }

    public function create()
    {
        return view('dashboard.entityservice.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_id' => 'nullable',
            'url' => 'nullable|max:255',
            'icon' => 'nullable|file|mimes:svg|max:2048',
            'existing_icon' => 'nullable|string',
        ]);

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = uniqid() . '.svg';
            $file->move(public_path('icon'), $filename);
            $data['icon'] = $filename;
        } elseif (!empty($data['existing_icon'])) {
            $data['icon'] = $data['existing_icon'];
        }

        $data['status'] = 1;
        $data['order_index'] = EntityService::max('order_index') + 1;

        $service = EntityService::create($data);

        return $request->ajax()
            ? response()->json(['message' => 'Servicio creado exitosamente.', 'content' => $service])
            : redirect()->route('dashboard.entityservice.index')->with('success', 'Servicio creado correctamente.');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/img/services', $filename);

            return response()->json([
                'location' => asset('storage/uploads/services/' . $filename)
            ]);
        }

        return response()->json(['error' => 'No se subió imagen'], 400);
    }

    public function edit($id)
    {
        $service = EntityService::findOrFail($id);
        return response()->json($service);
    }

    public function update(Request $request, $id)
    {
        $service = EntityService::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_id' => 'nullable',
            'url' => 'nullable|max:255',
            'icon' => 'nullable|file|mimes:svg|max:2048',
            'existing_icon' => 'nullable|string',
        ]);

        // Si se sube un nuevo ícono
        if ($request->hasFile('icon')) {
            // Eliminar el ícono anterior si existe
            if ($service->icon && file_exists(public_path($service->icon))) {
                unlink(public_path($service->icon));
            }

            $file = $request->file('icon');
            $filename = uniqid() . '.svg';
            $file->move(public_path('icon'), $filename);
            $data['icon'] = $filename;
        }

        $service->update($data);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Servicio actualizado exitosamente.',
                'content' => $service
            ]);
        }

        return redirect()->route('dashboard.entityservice.index')->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy($id)
    {
        $service = EntityService::findOrFail($id);

        if ($service->icon && file_exists(public_path($service->icon))) {
            unlink(public_path($service->icon));
        }

        $service->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Servicio eliminado correctamente.']);
        }

        return redirect()->route('dashboard.entityservice.index')->with('success', 'Servicio eliminado correctamente.');
    }
}