<?php
namespace App\Http\Controllers;

use App\Models\ContentPage;
use App\Models\ContentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class ContentItemController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('callsjobs');
    }

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
     |  ContentItem (CRUD + filtro)
     |=======================*/
    public function itemsIndex(Request $request)
    {
        $q = ContentItem::query()->with('page:id,title,slug');

        if ($request->filled('content_page_id')) {
            $q->where('content_page_id', (int)$request->input('content_page_id'));
        }
        if ($request->filled('search')) {
            $term = '%'.$request->input('search').'%';
            $q->where(function ($w) use ($term) {
                $w->where('title', 'like', $term)
                  ->orWhere('description', 'like', $term)
                  ->orWhere('url', 'like', $term);
            });
        }
        if ($request->filled('ordering_from')) {
            $q->where('ordering', '>=', (int)$request->input('ordering_from'));
        }
        if ($request->filled('ordering_to')) {
            $q->where('ordering', '<=', (int)$request->input('ordering_to'));
        }

        $q->orderBy('ordering')->orderBy('id');

        $items = $request->boolean('paginate', true)
            ? $q->paginate($request->integer('per_page', 20))
            : $q->get();

        return response()->json(
            ['data' => $items],
            200,
            [],
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }

    public function itemsStore(Request $request)
    {
        $data = $request->validate([
            'content_page_id' => 'required|exists:content_pages,id',
            'title'           => 'required|string|max:255',
            'url'             => 'nullable|url|max:2048',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'document'        => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'description'     => 'nullable|string',
            'ordering'        => 'nullable|integer|min:0',
            'extra'           => 'nullable|array',
        ]);

        $data['ordering'] = $data['ordering'] ?? 0;

        $dateFolder = now()->format('Y-m');

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeWithAppPublicPrefix(
                $request->file('image'),
                "content/items/{$dateFolder}/images"
            );
        }

        if ($request->hasFile('document')) {
            $data['document'] = $this->storeWithAppPublicPrefix(
                $request->file('document'),
                "content/items/{$dateFolder}/documents"
            );
        }

        $item = ContentItem::create($data);

        return response()->json(
            ['message' => 'Item creado correctamente.', 'item' => $item],
            201,
            [],
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }

    public function itemsUpdate(Request $request, int $id)
    {
        $item = ContentItem::findOrFail($id);

        $data = $request->validate([
            'content_page_id' => 'required|exists:content_pages,id',
            'title'           => 'required|string|max:255',
            'url'             => 'nullable|url|max:2048',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'document'        => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'description'     => 'nullable|string',
            'ordering'        => 'nullable|integer|min:0',
            'extra'           => 'nullable|array',
        ]);

        $data['ordering'] = $data['ordering'] ?? $item->ordering;

        $dateFolder = now()->format('Y-m');

        if ($request->hasFile('image')) {
            $this->safeDelete($item->image);

            $data['image'] = $this->storeWithAppPublicPrefix(
                $request->file('image'),
                "content/items/{$dateFolder}/images"
            );
        }

        if ($request->hasFile('document')) {
            $this->safeDelete($item->document);

            $data['document'] = $this->storeWithAppPublicPrefix(
                $request->file('document'),
                "content/items/{$dateFolder}/documents"
            );
        }

        $item->update($data);

        return response()->json(
            ['message' => 'Item actualizado correctamente.', 'item' => $item->fresh()],
            200,
            [],
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }

    public function itemsDestroy(int $id)
    {
        $item = ContentItem::findOrFail($id);

        $this->safeDelete($item->image);
        $this->safeDelete($item->document);

        $item->delete();

        return response()->json(
            ['message' => 'Item eliminado correctamente.'],
            200,
            [],
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }
}