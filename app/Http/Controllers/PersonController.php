<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return response()->json([]);
        }

        $persons = Person::where('fullname', 'like', "%{$query}%")
            ->orWhere('document_number', 'like', "%{$query}%")
            ->orWhere('company_name', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($persons);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:20|unique:persons,document_number',
            'email' => 'nullable|email',
            'type_person' => 'required|in:Natural,Juridica',
            'department_id' => 'nullable|exists:departments,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        $person = Person::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Persona creada correctamente.',
            'person' => $person
        ]);
    }
}