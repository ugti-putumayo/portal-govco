<?php
namespace App\Http\Controllers;

use App\Models\CallsJobs;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\AuthorizesByPermissionKey;

class CallsJobsController extends Controller
{
    use AuthorizesByPermissionKey;

    public function __construct()
    {
        parent::__construct();
        $this->authorizeCrud('callsjobs');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $communications = CallsJobs::when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                             ->orWhere('content', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('public.transparency.calls-jobs', compact('communications'));
    }

    public function show($id)
    {
        $communication = CallsJobs::findOrFail($id);
        return view('public.transparency.show-call-job', compact('communication'));
    }

    public function lastNewsIndex(Request $request)
    {
        $search = $request->input('search');
        $communications = CallsJobs::when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                             ->orWhere('content', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('public.news.calls-jobs', compact('communications'));
    }

    public function lastNewsShow($id)
    {
        $communication = CallsJobs::findOrFail($id);
        return view('public.news.show-call-job', compact('communication'));
    }

    public function indexGoverment()
    {
        $convocatorias = CallsJobs::orderBy('publication_date', 'desc')->take(5)->get();
        return view('public.goverment.secretaries-offices.microsite-goverment.goverment-calls', compact('convocatorias'));
    }

    public function recordCallJobs(Request $request)
    {
        $search = $request->input('search');

        $convocatorias = CallsJobs::when($search, function ($query, $search) {
                return $query->where('title', 'like', "%$search%")
                             ->orWhere('content', 'like', "%$search%");
            })
            ->orderBy('publication_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('public.goverment.secretaries-offices.microsite-goverment.record-call-jobs', compact('convocatorias'));
    }
}
