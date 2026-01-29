<?php
namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\Module;
use App\View\Components\Navbar;
use App\View\Composers\BreadcrumbComposer;
use App\Models\EntitySetting;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Blade::component('navbar', Navbar::class);
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }

        View::composer('*', function ($view) {
            $menus = Menu::with('submenus.subsubmenus')->orderBy('order')->get();
            $view->with('menus', $menus);
        });

        View::composer('*', BreadcrumbComposer::class);

        View::composer(['livewire.sidebar-menu', 'partials.sidebar', 'partials.sidebar-node'], function ($view) {
            if (!Auth::check()) { $view->with('modules', collect()); return; }

            $user = Auth::user();

            $roots = Module::roots()
                ->ordered()
                ->with('childrenRecursive')
                ->get();

            if ($user->isAdmin()) {
                $view->with('modules', $roots);
                return;
            }

            $visibleIds = $user->visibleModuleIds();
            $visibleIds = collect($visibleIds)->map(fn($v) => (int) $v)->all();

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

            $view->with('modules', $prune($roots));
        });

        View::composer('*', function ($view) {
            $settings = EntitySetting::first();    
            $view->with('entityName', $settings?->entity_name ?? 'Entidad por defecto');
            $view->with('entityLogo', $settings?->logo_path ?? 'default.png');
        });

        View::composer('partials.sidebar', function ($view) {
            $secciones = DB::table('transparencia')
                            ->where('tipo', 'seccion')
                            ->orderBy('orden')
                            ->get();
            
            $view->with('secciones', $secciones);
        });
    }
}