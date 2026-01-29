<?php
namespace App\Http\Controllers;

use App\Models\Dependency;
use App\Models\User;
use App\Models\Contrac;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class DashboardController extends Controller
{
    use AuthorizesByPermissionKey;
    public function __construct()
    {
        $this->middleware('perm:dashboard.view')->only('index');
    }

    public function index()
    {
        $totalDependencies = Dependency::count();
        $totalUsers = User::count();
        $totalContracts = Contrac::count();

        $completedContracts = Contrac::where('cutoff_date', '<=', now())->count();
        $contractsInExecution = Contrac::where('cutoff_date', '>', now())->count();

        return view('dashboard.dashboard', compact(
            'totalDependencies',
            'totalUsers',
            'totalContracts',
            'completedContracts',
            'contractsInExecution'
        ));
    }
}