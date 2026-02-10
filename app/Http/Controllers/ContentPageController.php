<?php
namespace App\Http\Controllers;

use App\Models\ContentPage;
use App\Models\ContentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class ContentPageController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('contentpages');
    }

    public function index(Request $request)
    {
        $q = ContentPage::query();

        if ($request->filled('module')) {
            $q->where('module', (string) $request->input('module'));
        }

        if ($request->filled('state')) {
            $state = filter_var($request->input('state'), FILTER_VALIDATE_BOOLEAN);
            $q->where('state', $state ? 1 : 0);
        }

        if ($request->filled('search')) {
            $term = '%'.(string) $request->input('search').'%';
            $q->where(function ($w) use ($term) {
                $w->where('title', 'like', $term)
                ->orWhere('slug', 'like', $term);
            });
        }

        $q->orderBy('ordering')->orderBy('id');

        $paginate = $request->has('paginate')
            ? filter_var($request->input('paginate'), FILTER_VALIDATE_BOOLEAN)
            : true;

        $perPage = (int) ($request->input('per_page', 20));

        $pages = $paginate
            ? $q->paginate($perPage)->withQueryString()
            : $q->get();

        return view('dashboard.publications.publications-contentpages.imc-contentpages-dashboard', compact('pages'));
    }

    public function create()
    {
        return view('components.administration.contentpages.modal-create-contentpages');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'module'   => 'required|string|max:100',
            'title'    => 'required|string|max:255',
            'slug'     => 'required|string|max:255|unique:content_pages,slug',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'state'    => 'nullable|boolean',
            'ordering' => 'nullable|integer|min:0',
            'meta'     => 'nullable|array',
        ]);

        $data['state']    = $request->boolean('state', true);
        $data['ordering'] = $data['ordering'] ?? 0;

        if ($request->hasFile('image')) {
            $dateFolder    = now()->format('Y-m');
            $data['image'] = $this->storeWithAppPublicPrefix(
                $request->file('image'),
                "content/pages/{$dateFolder}/images"
            );
        }

        $page = ContentPage::create($data);

        // Si viene por AJAX para tus modals:
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(
                ['message' => 'Página creada correctamente.', 'page' => $page],
                201,
                [],
                JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
            );
        }

        return redirect()
            ->route('dashboard.contentpages.index')
            ->with('success', 'Página creada correctamente.');
    }

    public function show(ContentPage $contentpage)
    {
        // Vista/admin para ver una sola página (si lo usas)
        return view('dashboard.publications-contentpages.imc-contentpages-show', [
            'page' => $contentpage->load('items'),
        ]);
    }

    /* =======================
     |  EDIT (resource:edit) y UPDATE (resource:update)
     |=======================*/
    public function edit(ContentPage $contentpage, Request $request)
    {
        // Si tu modal hace fetch a /contentpages/{id}/edit esperando JSON:
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(
                $contentpage->toArray(),
                200,
                [],
                JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
            );
        }

        // Si navegas a una vista de edición tradicional:
        return view('components.administration.contentpages.modal-update-contentpages', [
            'page' => $contentpage
        ]);
    }

    public function update(Request $request, ContentPage $contentpage)
    {
        $data = $request->validate([
            'module'   => 'required|string|max:100',
            'title'    => 'required|string|max:255',
            'slug'     => "required|string|max:255|unique:content_pages,slug,{$contentpage->id}",
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'state'    => 'nullable|boolean',
            'ordering' => 'nullable|integer|min:0',
            'meta'     => 'nullable|array',
        ]);

        $data['state']    = $request->boolean('state', $contentpage->state);
        $data['ordering'] = $data['ordering'] ?? $contentpage->ordering;

        if ($request->hasFile('image')) {
            $this->safeDelete($contentpage->image);

            $dateFolder     = now()->format('Y-m');
            $data['image']  = $this->storeWithAppPublicPrefix(
                $request->file('image'),
                "content/pages/{$dateFolder}/images"
            );
        }

        $contentpage->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(
                ['message' => 'Página actualizada correctamente.', 'page' => $contentpage->fresh()],
                200,
                [],
                JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
            );
        }

        return redirect()
            ->route('dashboard.contentpages.index')
            ->with('success', 'Página actualizada correctamente.');
    }

    /* =======================
     |  DESTROY (resource:destroy)
     |=======================*/
    public function destroy(ContentPage $contentpage, Request $request)
    {
        $contentpage->load('items');

        foreach ($contentpage->items as $it) {
            $this->safeDelete($it->image);
            $this->safeDelete($it->document);
        }
        $this->safeDelete($contentpage->image);

        ContentItem::where('content_page_id', $contentpage->id)->delete();
        $contentpage->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(
                ['message' => 'Página e items asociados eliminados correctamente.'],
                200,
                [],
                JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
            );
        }

        return redirect()
            ->route('dashboard.contentpages.index')
            ->with('success', 'Página e items asociados eliminados correctamente.');
    }

    /* =======================
     |  PÚBLICO POR SLUG (separado del resource)
     |=======================*/
    public function internalManagementControlContent(?string $slug = 'internal-management-control')
    {
        $prefix = 'internal-management-control';

        $intro = ContentPage::with(['items' => fn($q) => $q->orderBy('ordering')])
            ->active()
            ->where('slug', $prefix)
            ->first();

        $labels = [
            "{$prefix}-audits"                        => 'Auditorías',
            "{$prefix}-monitoring-reports"            => 'Informes de Gestión',
            "{$prefix}-promoting-culture-selfcontrol" => 'Fomento de la Cultura de Autocontrol',
            "{$prefix}-improvement-plans"             => 'Planes de Mejoramiento',
            "{$prefix}-bulletin"                      => 'Boletines',
            "{$prefix}-independent-evaluation-report" => 'Informe de Evaluación Independiente',
        ];

        $tabs = ContentPage::with(['items' => fn($q) => $q->orderBy('ordering')])
            ->active()
            ->where('slug', 'like', "{$prefix}-%")
            ->get()
            ->keyBy('slug');

        $pages = collect($labels)->mapWithKeys(function ($label, $slug) use ($tabs) {
            return $tabs->has($slug) ? [$slug => $tabs->get($slug)] : [];
        });

        $labels = array_intersect_key($labels, $pages->all());

        $order = array_keys($pages->all());
        $activeSlug = ($slug === $prefix || !in_array($slug, $order, true))
            ? ($order[0] ?? null)
            : $slug;

        return view('public.transparency.internal-management-control.internal-management-control', [
            'intro'      => $intro,
            'pages'      => $pages,
            'labels'     => $labels,
            'order'      => $order,
            'activeSlug' => $activeSlug,
            'prefix'     => $prefix,
        ]);
    }

    public function internalManagementControlTab(Request $request, string $key)
    {
        $query = ContentPage::active()
            ->with(['items' => fn($q) => $q->orderBy('ordering')]);

        $page = ctype_digit($key)
            ? $query->where('id', (int)$key)->first()
            : $query->where('slug', $key)->first();

        if (!$page) {
            Log::warning('ICG Tab: clave no encontrada', ['key' => $key]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'html' => '<div class="doclist-empty">No se encontró contenido para esta pestaña.</div>'
                ], 200);
            }

            return response('<div class="doclist-empty">No se encontró contenido para esta pestaña.</div>', 200)
                ->header('Content-Type', 'text/html; charset=UTF-8');
        }

        $html = view('components.public.transparency.internal-management-control.doc-list', [
            'title'   => $page->title,
            'records' => $page->items,
        ])->render();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['html' => $html]);
        }

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /* =======================
     |  Helpers de archivos
     |=======================*/
    private function toDiskRelative(?string $path): ?string
    {
        if (!$path || !is_string($path)) return null;
        $p = trim(str_replace(["\r", "\n"], '', $path));
        if (preg_match('#^https?://#i', $p)) {
            $p = parse_url($p, PHP_URL_PATH) ?? '';
        }
        $p = ltrim($p, '/');
        $p = preg_replace('#^(app/public/|public/|storage/)#i', '', $p);
        $p = ltrim(preg_replace('#/+#', '/', $p), '/');
        return $p !== '' ? $p : null;
    }

    private function storeWithAppPublicPrefix(\Illuminate\Http\UploadedFile $file, string $dir): string
    {
        $rel = $file->store($dir, 'public');
        return 'app/public/'.$rel;
    }

    private function safeDelete(?string $dbPath): void
    {
        $rel = $this->toDiskRelative($dbPath);
        if ($rel && Storage::disk('public')->exists($rel)) {
            Storage::disk('public')->delete($rel);
            return;
        }
        if ($rel) {
            $abs = public_path($rel);
            if (File::exists($abs)) File::delete($abs);
        }
    }

    /* =======================
     |  Endpoints utilitarios
     |=======================*/
    public function pagesForSelect(Request $request)
    {
        $q = ContentPage::query()->select('id', 'title', 'slug', 'module', 'state')->orderBy('ordering');
        if ($request->boolean('only_active', true)) {
            $q->where('state', true);
        }
        if ($request->filled('module')) {
            $q->where('module', $request->string('module'));
        }
        if ($request->filled('search')) {
            $term = '%'.$request->input('search').'%';
            $q->where(function ($w) use ($term) {
                $w->where('title', 'like', $term)
                  ->orWhere('slug', 'like', $term);
            });
        }
        $pages = $q->get()->map(fn($p) => [
            'id'     => $p->id,
            'text'   => $p->title,
            'slug'   => $p->slug,
            'module' => $p->module,
            'active' => (bool)$p->state,
        ]);

        return response()->json(
            ['pages' => $pages],
            200,
            [],
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }
}