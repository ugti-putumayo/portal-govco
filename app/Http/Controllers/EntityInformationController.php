<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlantOfficial;
use App\Models\Contractor;
use App\Models\Entity;
use App\Models\Dependency;
use App\Models\Association;
use App\Models\DecisionMaking;
use App\Models\Event;
use App\Models\ControlEntity;

class EntityInformationController extends Controller
{
    public function indexPublicMision()
    {
        return view('public.transparency.entity-information.mision');
    }

    public function directory(Request $request)
    {
        $search = $request->input('search');
        $plantofficials = PlantOfficial::when($search, function ($query, $search) {
                            $query->where('fullname', 'like', "%{$search}%")
                                  ->orWhere('charge', 'like', "%{$search}%")
                                  ->orWhere('dependency', 'like', "%{$search}%");
                        })
                        ->orderBy('fullname')
                        ->paginate(10);
    
        $contractors = Contractor::when($search, function ($query, $search) {
                            $query->where('contractor', 'like', "%{$search}%")
                                  ->orWhere('contract_number', 'like', "%{$search}%")
                                  ->orWhere('object', 'like', "%{$search}%");
                        })
                        ->orderBy('contractor')
                        ->paginate(10);
    
        $sigepInfo = "
                Art. 9 Ley 1712 de 2014 en concordancia con Decreto 1081 de 2015 Art. 2.1.1.2.1.5.
                Directorio de Información de servidores públicos, empleados y contratistas.
                PARÁGRAFO 1. Para las entidades u organismos públicos, el requisito se entenderá cumplido con 
                publicación de la información que contiene el directorio en el Sistema de Gestión del Empleo Público (SIGEP),
                de que trata el artículo 18 de la Ley 909 de 2004 y las normas que la reglamentan.
                
                Ingrese al siguiente <a href='https://www.funcionpublica.gov.co/dafpIndexerBHV/hvSigep/index?find=FindNext&query=putumayo&dptoSeleccionado=&entidadSeleccionado=4415&munSeleccionado=&tipoAltaSeleccionado=&bloquearFiltroDptoSeleccionado=&bloquearFiltroEntidadSeleccionado=&bloquearFiltroMunSeleccionado=&bloquearFiltroTipoAltaSeleccionado='>Enlace</a> para consultar la información de cada uno de los servidores públicos y contratistas.";                   
    
        return view('public.transparency.entity-information.directory-active', compact('plantofficials', 'contractors', 'sigepInfo', 'search'));
    }

    public function indexPublicDirectoryInstitutional(Request $request)
    {
        $search = $request->input('search');
        $directory = Dependency::select('name', 'description', 'cellphone', 'email')
                        ->when($search, function ($query, $search) {
                            return $query->where('name', 'like', "%{$search}%");
                        })
                        ->orderBy('name')
                        ->paginate(10);
        return view('public.transparency.entity-information.directory-institutional', compact('directory', 'search'));
    }

    public function indexPublicEntitiesDirectory(Request $request)
    {
        $search = $request->input('search');
        $entities = Entity::when($search, function ($query, $search) {
                            return $query->where('name', 'like', "%{$search}%")
                                         ->orWhere('description', 'like', "%{$search}%")
                                         ->orWhere('contact_email', 'like', "%{$search}%")
                                         ->orWhere('phone', 'like', "%{$search}%");
                        })
                        ->orderBy('name')
                        ->paginate(5);
        return view('public.transparency.entity-information.entities-directory', compact('entities', 'search'));
    }

    public function indexPublicAssociationDirectory(Request $request)
    {
        $search = $request->input('search');
        $associations = Association::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('classification', 'like', "%{$search}%")
                             ->orWhere('activity', 'like', "%{$search}%")
                             ->orWhere('cellphone', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15);

        return view('public.transparency.entity-information.associations-directory', compact('associations', 'search'));
    }

    public function indexPublicDecisionMakingDirectory(Request $request)
    {
        $search = $request->input('search');
        $decisions = DecisionMaking::when($search, function ($query, $search) {
                            return $query->where('entry_date', 'like', "%{$search}%")
                                         ->orWhere('name', 'like', "%{$search}%")
                                         ->orWhere('archive', 'like', "%{$search}%");
                                         
                        })
                        ->orderBy('entry_date')
                        ->paginate(10);

        return view('public.transparency.entity-information.decision-making-directory', compact('decisions', 'search'));
    }

    public function indexPublicCalendar()
    {
        $events = Event::all();
        return view('public.transparency.entity-information.events', compact('events'));
    }

    public function indexPublicControlEntities(Request $request)
    {
        $search = $request->input('search');
        $entities = ControlEntity::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })->paginate(12);

        return view('public.transparency.entity-information.control-entities', compact('entities', 'search'));
    }

    public function indexPublicResume()
    {
        return view('public.transparency.entity-information.resume');
    }
}
