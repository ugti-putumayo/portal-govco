<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class EventController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('events');
    }

    public function index(Request $request)
    {
        $events = Event::all();
        return view('dashboard.publications.events', compact('events'));
    }

    public function show(Request $request, $id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }
        return view('publication.events.show', compact('event'));
    }

    public function create()
    {
        return view('dashboard.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $now = now();
            $path = $request->file('image')->store(
                'public/publications/events/' . $now->year . '/' . $now->format('m')
            );

            $data['image'] = Storage::url($path);
        }

        $event = Event::create($data);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Evento creado con éxito',
                'event' => $event
            ]);
        }

        return redirect()->route('dashboard.events.index')->with('success', 'Evento creado con éxito');
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);
        return response()->json($event);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $event = Event::findOrFail($id);
        $event->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Evento actualizado con éxito',
                'event' => $event
            ]);
        }

        return redirect()->route('dashboard.events.index')->with('success', 'Evento actualizado con éxito');
    }

    public function destroy(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return response()->json(['message' => 'Evento eliminado con éxito'], 200);
    }

    public function fetchEvents(Request $request)
    {
        $events = Event::all();
        return response()->json($events);
    }
}

