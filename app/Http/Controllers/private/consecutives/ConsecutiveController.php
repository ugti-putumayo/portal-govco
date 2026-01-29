<?php

namespace App\Http\Controllers\private\consecutives;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use App\Models\Consecutives\Series;
use App\Models\Consecutives\Counter;
use App\Models\Consecutives\Consecutives;
use App\Http\Requests\StoreConsecutiveRequest;
use App\Http\Requests\CancelConsecutiveRequest;
use App\Http\Requests\UpdateConsecutiveRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class ConsecutiveController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('consecutives');
    }

    public function index(Request $request)
    {
        $user    = Auth::user();
        $isAdmin = $user && (int) $user->rol_id === 1;

        $query = Consecutives::with(['series', 'user', 'person'])->latest();

        if (!$isAdmin && $user && $user->dependency_id) {
            $query->whereHas('series', function ($q) use ($user) {
                $q->where('dependency_id', $user->dependency_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function($q) use ($search) {
                $q->where('full_consecutive', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhere('recipient', 'like', "%{$search}%")
                ->orWhereHas('person', function($qp) use ($search) {
                    $qp->where('fullname', 'like', "%{$search}%")
                        ->orWhere('document_number', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('series_id')) {
            $query->where('series_id', $request->input('series_id'));
        }

        $consecutives = $query->paginate(25)->withQueryString();

        $seriesQuery = Series::where('is_active', true);

        if (!$isAdmin && $user && $user->dependency_id) {
            $seriesQuery->where('dependency_id', $user->dependency_id);
        }

        $series = $seriesQuery
            ->orderBy('name')
            ->get(['id', 'name', 'prefix']);

        return view('dashboard.consecutives.consecutives', compact('consecutives', 'series'));
    }

    public function create()
    {
        $user    = Auth::user();
        $isAdmin = $user && (int) $user->rol_id === 1;

        $seriesQuery = Series::where('is_active', true);

        if (!$isAdmin && $user && $user->dependency_id) {
            $seriesQuery->where('dependency_id', $user->dependency_id);
        }

        $series = $seriesQuery
            ->orderBy('name')
            ->get(['id', 'name', 'prefix']);

        return response()->json([
            'series' => $series
        ]);
    }

    public function store(StoreConsecutiveRequest $request)
    {
        $validated = $request->validated();
        $series = Series::findOrFail($validated['series_id']);
        $year = now()->year;
        $newConsecutive = null;
        $user      = Auth::user();

        if ($user->dependency_id && $series->dependency_id !== $user->dependency_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permiso para generar consecutivos en esta serie.',
            ], 403);
        }

        try {
            DB::transaction(function () use ($series, $year, $validated, &$newConsecutive, $request) {
                $counter = Counter::lockForUpdate()->firstOrCreate(
                    ['series_id' => $series->id, 'year' => $year],
                    ['last_number' => 0]
                );

                $counter->increment('last_number');
                $newNumber = $counter->last_number;

                $fullConsecutive = sprintf(
                    '%s-%d-%s',
                    $series->prefix,
                    $year,
                    str_pad($newNumber, 4, '0', STR_PAD_LEFT)
                );

                $attachmentPath = null;
                if ($request->hasFile('attachment_url')) {
                    $attachmentPath = $request->file('attachment_url')->store('consecutives_files', 'public');
                }

                $newConsecutive = Consecutives::create([
                    'series_id' => $series->id,
                    'user_id' => Auth::id(),
                    'person_id' => $validated['person_id'] ?? null,
                    'number' => $newNumber,
                    'year' => $year,
                    'full_consecutive' => $fullConsecutive,
                    'subject' => $validated['subject'],
                    'recipient' => $validated['recipient'],
                    'status' => 'Generated',
                    'document_type' => $validated['document_type'] ?? null,
                    'internal_reference' => $validated['internal_reference'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'attachment_url' => $attachmentPath,
                ]);
            }, 5);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el consecutivo: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Consecutivo generado exitosamente: ' . $newConsecutive->full_consecutive,
            'consecutive' => $newConsecutive->load('series', 'user', 'person')
        ], 201);
    }

    public function show(Consecutives $consecutive)
    {
        $consecutive->load('series', 'user', 'canceledBy', 'person');  
        return response()->json($consecutive);
    }

    public function edit(Consecutives $consecutive)
    {
        if ($consecutive->status === 'Canceled') {
            return response()->json(['message' => 'No se puede editar un consecutivo anulado.'], 403);
        }
        $consecutive->load('person');
        return response()->json($consecutive);
    }

    public function update(UpdateConsecutiveRequest $request, Consecutives $consecutive)
    {
        if ($consecutive->status === 'Canceled') {
            return response()->json([
                'success' => false,
                'message' => 'No es posible editar un documento anulado.'
            ], 422);
        }

        $data = $request->validated();

        if ($request->hasFile('attachment_url')) {
            if ($consecutive->attachment_url) {
                Storage::disk('public')->delete($consecutive->attachment_url);
            }
            $path = $request->file('attachment_url')->store('consecutives_files', 'public');
            $data['attachment_url'] = $path;
        }

        $consecutive->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Consecutivo actualizado correctamente.',
            'consecutive' => $consecutive->load('person')
        ]);
    }

    public function cancel(CancelConsecutiveRequest $request, Consecutives $consecutive)
    {
        $user    = Auth::user();
        $isAdmin = $user && (int) $user->rol_id === 1;
        $consecutive->load('series');

        if (!$isAdmin && $user && $user->dependency_id) {
            if (!$consecutive->series || $consecutive->series->dependency_id !== $user->dependency_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para anular consecutivos de esta serie.',
                ], 403);
            }
        }

        if ($consecutive->status === 'Canceled') {
            return response()->json([
                'success' => false,
                'message' => 'Este consecutivo ya fue anulado.'
            ], 422);
        }

        $data = $request->validated();

        $consecutive->update([
            'status'               => 'Canceled',
            'canceled_at'          => now(),
            'canceled_by_user_id'  => $user?->id,
            'cancellation_reason'  => $data['cancellation_reason'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'El consecutivo ha sido anulado.',
            'consecutive' => $consecutive
        ]);
    }
}