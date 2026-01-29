<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\City;

class LocateController extends Controller
{
    public function index()
    {
        return response()->json(Department::all());
    }

    public function getCities($department_id)
    {
        $cities = City::where('department_id', $department_id)->get();
        return response()->json($cities);
    }
}