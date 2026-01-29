<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mipg;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class MipgController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('mipg');
    }

    public function publicIndex(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        if ($search) {
            $files = Mipg::when($category, function ($query) use ($category, $search) {
                        return $query->where($category, 'like', "%{$search}%");
                    }, function ($query) use ($search) {
                        return $query->where(function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%");
                        });
                    })
                    ->get();
            $grouped = collect();
            foreach ($files as $file) {
                if ($file->type === 'directory') {
                    $file->children = Mipg::where('parent_id', $file->id)->get();
                    $grouped->push($file);
                } elseif ($file->parent_id) {
                    $parent = $grouped->firstWhere('id', $file->parent_id);
                    if (!$parent) {
                        $parent = Mipg::where('id', $file->parent_id)->first();
                        $parent->children = collect([$file]);
                        $grouped->push($parent);
                    } else {
                        $parent->children->push($file);
                    }
                } else {
                    $grouped->push($file);
                }
            }
            $files = $grouped;
            $paginate = false;
        } else {
            // Vista normal sin búsqueda
            $files = Mipg::whereNull('parent_id')->get();

            foreach ($files as $file) {
                if ($file->type === 'directory') {
                    $file->children = Mipg::where('parent_id', $file->id)->get();
                }
            }
            $paginate = true;
        }
        return view('dashboard.mipg.mipg', compact('files', 'search', 'category', 'paginate'));
    }

    public function index()
    {
        $files = Mipg::whereNull('parent_id')->get();

        foreach ($files as $file) {
            if ($file->type === 'directory') {
                $file->children = $this->getChildren($file->id);
            }
        }

        return view('dashboard.mipg.mipg', compact('files'));
    }

    private function getChildren($parentId)
    {
        $children = Mipg::where('parent_id', $parentId)->get();

        foreach ($children as $child) {
            if ($child->type === 'directory') {
                $child->children = $this->getChildren($child->id);
            }
        }

        return $children;
    }

    public function create()
    {
        return view('dashboard.mipg.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:25'
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('mipg', 'public');
            $data['file'] = $path;
        }

        $file = Mipg::create($data);
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Documento agregado exitosamente.',
                'file' => $file
            ]);
        }

        return redirect()->route('dashboard.mipg.index')->with('success', 'Documento agregado correctamente.');
    }

    public function edit($id)
    {
        $file = Mipg::findOrFail($id);
        return response()->json($file);
    }

    public function update(Request $request, Mipg $file)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:25'
        ]);

        if ($request->hasFile('file')) {
            if ($file->file && Storage::disk('public')->exists($file->file)) {
                Storage::disk('public')->delete($file->file);
            }
            $path = $request->file('file')->store('mipg', 'public');
            $data['file'] = $path;
        }

        $file->update($data);
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Documento agregado exitosamente.',
                'file' => $file
            ]);
        }
        return redirect()->route('dashboard.mipg.index')->with('success', 'Documento actualizado correctamente.');
    }

    public function destroy(Mipg $mipg)
    {
        try {
            if ($mipg->type === 'directory') {
                $this->deleteDirectoryRecursive($mipg);
            } else {
                if ($mipg->file) {
                    $fileExists = Storage::disk('public')->exists($mipg->file);
                    if ($fileExists) {
                        $deletedPhysicalFile = Storage::disk('public')->delete($mipg->file);
                        if (!$deletedPhysicalFile) {
                            Log::error('Failed to delete physical file.', ['path' => $mipg->file]);
                        }
                    } else {
                        Log::warning('Physical file not found for deletion.', ['path' => $mipg->file]);
                    }
                } else {
                    Log::warning('File item has no path in `file` column.', ['id' => $mipg->id]);
                }
                $deletedDbRecord = $mipg->delete();
                if (!$deletedDbRecord) {
                    Log::error('Failed to delete file record from database.', ['id' => $mipg->id]);
                }
            }
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Archivo eliminado.'
                ]);
            }
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['message' => 'Error al eliminar el elemento: ' . $e->getMessage()], 500);
            }
        }
    }

    private function deleteDirectoryRecursive(Mipg $directory)
    {
        $children = Mipg::where('parent_id', $directory->id)->get();
        foreach ($children as $child) {
            if ($child->type === 'directory') {
                $this->deleteDirectoryRecursive($child);
            } else { // It's a file
                if ($child->file) {
                    $childFileExists = Storage::disk('public')->exists($child->file);
                    if ($childFileExists) {
                        $deletedChildPhysicalFile = Storage::disk('public')->delete($child->file);
                        if (!$deletedChildPhysicalFile) {
                            Log::error('Failed to delete child physical file.', ['child_path' => $child->file]);
                        }
                    } else {
                        Log::warning('Child physical file not found for deletion.', ['child_path' => $child->file]);
                    }
                } else {
                    Log::warning('Child file item has no path in `file` column.', ['child_id' => $child->id]);
                }
                $deletedChildDbRecord = $child->delete();
                if (!$deletedChildDbRecord) {
                    Log::error('Failed to delete child file record from database.', ['child_id' => $child->id]);
                }
            }
        }
        $physicalDirectoryPath = 'mipg/' . $directory->path;
        $directoryExists = Storage::disk('public')->exists($physicalDirectoryPath);
        if ($directoryExists) {
            $deletedPhysicalDirectory = Storage::disk('public')->deleteDirectory($physicalDirectoryPath);
            if (!$deletedPhysicalDirectory) {
                Log::error('Failed to delete physical directory.', ['path' => $physicalDirectoryPath]);
            }
        } else {
            Log::warning('Physical directory not found for deletion.', ['path' => $physicalDirectoryPath]);
        }
        $deletedDirectoryDbRecord = $directory->delete();
        if (!$deletedDirectoryDbRecord) {
            Log::error('Failed to delete directory record from database.', ['id' => $directory->id]);
        }
    }

    public function rename(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $file = Mipg::findOrFail($id);
            $oldName = $file->name;
            $newName = $request->input('name');

            if ($file->name === $newName) {
                return response()->json(['message' => 'El nombre no cambió.', 'file' => $file]);
            }

            $file->name = $newName;

            if ($file->type === 'directory') {
                $segments = explode('/', $file->path);
                array_pop($segments);
                $segments[] = $newName;
                $newPath = implode('/', $segments);
                $oldPath = $file->path;
                $file->path = $newPath;
                $file->save();
                $this->updateDescendantPaths($file, $oldPath, $newPath);
            } else {
                $segments = explode('/', $file->path);
                array_pop($segments);
                $segments[] = $newName;
                $file->path = implode('/', $segments);
                $file->save();
            }

            return response()->json(['message' => 'Nombre actualizado correctamente.', 'file' => $file]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Elemento no encontrado.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el nombre.'], 500);
        }
    }

    public function storeFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:mipg,id',
            'path' => 'required|string'
        ]);
        try {
            $folderName = $request->input('name');
            $parentId = $request->input('parent_id');
            $basePath = trim($request->input('path'), '/');
            $storageDisk = 'public';
            $directoryPathSuffix = $basePath ? $basePath . '/' . $folderName : $folderName;
            $fullDirectoryPath = 'mipg/' . $directoryPathSuffix;
            if (Storage::disk($storageDisk)->exists($fullDirectoryPath)) {
                return response()->json(['success' => false, 'message' => 'Una carpeta con este nombre ya existe en esta ubicación.'], 400);
            }
            Storage::disk($storageDisk)->makeDirectory($fullDirectoryPath);
            $newFolder = new Mipg();
            $newFolder->name = $folderName;
            $newFolder->type = 'directory';
            $newFolder->path = $directoryPathSuffix;
            $newFolder->parent_id = $parentId;
            $newFolder->save();
            return response()->json(['success' => true, 'message' => 'Carpeta creada exitosamente.', 'folder' => $newFolder]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear la carpeta: ' . $e->getMessage()], 500);
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'parent_id' => 'nullable|integer|exists:mipg,id'
        ]);

        $parent = Mipg::find($request->parent_id);
        $originalName = str_replace(' ', '_', $request->file('file')->getClientOriginalName());
        $extension = strtolower($request->file('file')->getClientOriginalExtension());

        $fullPath = $parent ? 'mipg/' . $parent->path : 'mipg';
        $storedPath = $request->file('file')->store($fullPath, 'public');

        $file = Mipg::create([
            'name' => $originalName,
            'extension' => $extension,
            'type' => 'file',
            'file' => $storedPath,
            'parent_id' => $request->parent_id,
            'path' => $parent ? ($parent->path . '/' . $originalName) : $originalName
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Archivo subido correctamente.',
            'file' => $file
        ]);
    }

    private function updateDescendantPaths(Mipg $folder, $oldBasePath, $newBasePath)
    {
        $children = Mipg::where('parent_id', $folder->id)->get();

        foreach ($children as $child) {
            $relativePath = substr($child->path, strlen($oldBasePath));
            $child->path = $newBasePath . $relativePath;
            $child->save();

            if ($child->type === 'directory') {
                $this->updateDescendantPaths($child, $oldBasePath . '/' . $child->name, $newBasePath . '/' . $child->name);
            }
        }
    }

    public function assignArea(Request $request, $id)
    {
        $request->validate([
            'dependency_id' => 'required|exists:dependencies,id'
        ]);

        $file = Mipg::findOrFail($id);
        $file->dependency_id = $request->dependency_id;
        $file->save();

        return response()->json(['success' => true]);
    }

    public function dependencySummary()
    {
        $dependencies = \App\Models\Dependency::withCount('mipgItems')->get()->map(function ($dep) {
            return [
                'id' => $dep->id,
                'name' => $dep->name,
                'total' => $dep->mipg_items_count
            ];
        });

        $generales = \App\Models\Mipg::where('dependency_id', 0)->count();

        $resumen = collect([
            [
                'id' => 0,
                'name' => 'General',
                'total' => $generales
            ]
        ])->merge($dependencies);

        return response()->json($resumen);
    }

    public function typeSummary()
    {
        $files = Mipg::where('type', 'file')
            ->whereNotNull('parent_id')
            ->where('dependency_id', '!=', 0)
            ->get();

        $folders = Mipg::where('type', 'directory')->pluck('name', 'id');
        $dependencies = \App\Models\Dependency::pluck('name', 'id');

        $agrupado = $files->groupBy('dependency_id')->map(function ($items, $depId) use ($folders, $dependencies) {
            $porTipo = $items->groupBy('parent_id')->map(function ($grupo, $parentId) use ($folders) {
                return [
                    'name' => $folders[$parentId] ?? 'Sin nombre',
                    'total' => count($grupo)
                ];
            })->values();
            return [
                'dependency_id' => (int) $depId,
                'dependency_name' => $dependencies[$depId] ?? 'Desconocida',
                'document_types' => $porTipo
            ];
        })->values();
        return response()->json($agrupado);
    }

    public function toggleVisibility(Request $request, $id)
    {
        $file = Mipg::findOrFail($id);
        if ($file->type === 'file') {
            $file->is_visible = !$file->is_visible;
            $file->save();
            return response()->json([
                'success' => true,
                'is_visible' => $file->is_visible
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Solo se puede cambiar el estado de archivos'], 400);
    }
}