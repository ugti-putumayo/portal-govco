<?php
namespace App\Http\Controllers\private\consecutives;

use App\Http\Controllers\Controller;
use App\Models\Consecutives\Series;
use App\Http\Requests\StoreSeriesRequest;
use App\Http\Requests\UpdateSeriesRequest;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class SeriesController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('series');
    }

    public function index()
    {
        $series = Series::orderBy('name')->paginate(20);
        return view('dashboard.consecutives.series', compact('series'));
    }

    public function create()
    {
        return view('dashboard.consecutives.series.create');
    }

    public function store(StoreSeriesRequest $request)
    {
        Series::create($request->validated());
        return redirect()->route('dashboard.consecutives.index')
            ->with('success', 'Serie creada exitosamente.');
    }

    public function show(Series $series)
    {
        return view('dashboard.consecutives.series.show', compact('series'));
    }

    public function edit($id)
    {
        $series = Series::findOrFail($id);
        return response()->json($series);
    }

    public function update(UpdateSeriesRequest $request, Series $series)
    {
        $series->update($request->validated());
        return redirect()->route('dashboard.consecutives.index')
            ->with('success', 'Serie actualizada exitosamente.');
    }

    public function destroy(Series $series)
    {
        $series->update(['is_active' => false]);
        return redirect()->route('dashboard.consecutives.index')
            ->with('success', 'Serie desactivada exitosamente.');
    }
}