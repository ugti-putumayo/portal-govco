<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Location;

class ContactController extends Controller
{
    public function indexPublicContact(Request $request)
    {
        $search = $request->input('search');

        $contacts = Contact::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
        })->paginate(10);

        return view('public.contacts.contacts', compact('contacts'));
    }

    public function indexPublicLocation()
    {
        $locations = Location::all();
        return view('public.contacts.map', compact('locations'));
    }
}
