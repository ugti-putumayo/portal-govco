<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Module;

class SidebarMenu extends Component
{
    public $modules = [];

    public function mount()
    {
        if (!Auth::check()) { $this->modules = collect(); return; }

        $user = Auth::user();

        if ($user->isAdmin()) {
            $this->modules = Module::roots()
                ->ordered()
                ->with('childrenRecursive')
                ->get();
            return;
        }

        $visibleIds = $user->visibleModuleIds();
        $visibleIds = collect($visibleIds)->map(fn($v) => (int) $v)->all();

        $roots = Module::roots()
            ->ordered()
            ->with('childrenRecursive')
            ->get();

        $prune = function ($nodes) use (&$prune, $visibleIds) {
            $nodes->each(function ($m) use (&$prune) {
                if ($m->relationLoaded('childrenRecursive')) {
                    $kids = $m->getRelation('childrenRecursive');
                    $m->setRelation('childrenRecursive', $prune($kids));
                }
            });

            return $nodes->filter(function ($m) use ($visibleIds) {
                $kids = $m->relationLoaded('childrenRecursive')
                    ? $m->getRelation('childrenRecursive')
                    : collect();
                return in_array($m->id, $visibleIds, true) || $kids->isNotEmpty();
            })->values();
        };

        $this->modules = $prune($roots);
    }

    public function render()
    {
        return view('livewire.sidebar-menu', ['modules' => $this->modules]);
    }
}