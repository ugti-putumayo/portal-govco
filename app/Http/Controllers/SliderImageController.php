<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SliderImage;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class SliderImageController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        $this->authorizeCrud('sliderimages');
    }

    public function active()
    {
        $slider = SliderImage::where('status', 1)
            ->orderByDesc('order')
            ->get();

        return view('home', ['images' => $slider]);
    }

    public function manage()
    {
        $slider = SliderImage::orderByDesc('order')->paginate(20);
        return view('dashboard.slider.manage', compact('slider'));
    }

    public function toggleStatus($id)
    {
        $slider = SliderImage::findOrFail($id);
        $slider->status = !$slider->status;
        $slider->save();

        return response()->json(['message' => 'Estado actualizado', 'status' => $slider->status]);
    }

    public function updateOrder(Request $request, $id)
    {
        $request->validate([
            'order' => 'required|integer'
        ]);

        $slider = SliderImage::findOrFail($id);
        $slider->order = $request->order;
        $slider->save();

        return response()->json(['message' => 'Orden actualizado', 'order' => $slider->order]);
    }

    public function index()
    {
        $slider = SliderImage::paginate(15);
        return view('dashboard.administration.slider-images', compact('slider'));
    }

    public function show(Request $request, $id)
    {
        $slider = SliderImage::find($id);
        if (!$slider) {
            return response()->json(['message' => 'Imagen no encontrada'], 404);
        }
        return view('slider.show', compact('slider'));
    }

    public function create()
    {
        return view('dashboard.slider.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:250',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $relativePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('img/sliders');
            
            File::ensureDirectoryExists($destinationPath);
            $file->move($destinationPath, $filename);
            $relativePath = 'img/sliders/' . $filename;
        }

        $slider = SliderImage::create([
            'title' => $request->title,
            'route' => $relativePath,
            'link' => $request->link,
            'order' => (SliderImage::max('order') ?? 0) + 1,
            'status' => 1,
            'site_id' => $request->site_id,
            'user_register_id' => $request->user_register_id,
        ]);

        return response()->json([
            'message' => 'Imagen creada con éxito',
            'slider' => $slider
        ]);
    }

    public function edit($id)
    {
        $slider = SliderImage::findOrFail($id);
        return response()->json($slider);
    }

    public function update(Request $request, $id)
    {
        $slider = SliderImage::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:250',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($slider->route && File::exists(public_path($slider->route))) {
                File::delete(public_path($slider->route));
            }

            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('img/sliders');
            File::ensureDirectoryExists($destinationPath);
            $file->move($destinationPath, $filename);
            
            $slider->route = 'img/sliders/' . $filename;
        }

        $slider->title = $request->title;
        $slider->link = $request->link;
        $slider->order = $request->order ?? $slider->order;
        $slider->status = $request->status ?? $slider->status;
        $slider->site_id = $request->site_id ?? $slider->site_id;
        $slider->save();

        return response()->json([
            'message' => 'Imagen actualizada con éxito',
            'slider' => $slider
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $slider = SliderImage::findOrFail($id);

        $imagePath = public_path('img/sliders/' . $slider->route);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $slider->delete();

        return response()->json(['message' => 'Imagen eliminada con éxito'], 200);
    }
}