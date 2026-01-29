@extends('public.transparency.shared.sidebar')
@section('sidebar')
  @include('partials.sidebar')
@endsection

@section('title', $page->title ?? 'Marco Fiscal de Mediano Plazo')

@section('main-content')

{{-- LÓGICA PHP: Agrupar items por AÑO de creación --}}
@php
    $groupedItems = $page->items->sortByDesc('created_at')->groupBy(function($item) {
        return $item->created_at->format('Y'); 
    });
    // Obtener el año más reciente para la pestaña activa
    $latestYear = $groupedItems->keys()->first();
@endphp

<div class="container my-5 govco-fiscal-container">
    
    {{-- TÍTULO / HERO --}}
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="govco-page-title">{{ $page->title }}</h1>
            @if($page->body || $page->content)
                <div class="mx-auto govco-page-desc">
                    {!! $page->body ?? $page->content !!}
                </div>
            @endif
        </div>
    </div>

    @if($groupedItems->isNotEmpty())
        <div class="row g-4">
            
            {{-- COLUMNA IZQUIERDA: MENÚ TABS (AÑOS) --}}
            <div class="col-md-3 col-lg-2">
                <div class="govco-sticky-menu">
                    <div class="nav flex-column nav-pills govco-vertical-tabs" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        @foreach($groupedItems as $year => $items)
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                    id="v-pills-{{ $year }}-tab" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#v-pills-{{ $year }}" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="v-pills-{{ $year }}" 
                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                <span class="tab-year">{{ $year }}</span>
                                <span class="tab-badge">{{ $items->count() }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: CONTENIDO --}}
            <div class="col-md-9 col-lg-10">
                <div class="tab-content" id="v-pills-tabContent">
                    @foreach($groupedItems as $year => $items)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                             id="v-pills-{{ $year }}" 
                             role="tabpanel" 
                             aria-labelledby="v-pills-{{ $year }}-tab">
                            
                            <h3 class="govco-section-title">Vigencia {{ $year }}</h3>

                            <div class="govco-doc-list">
                                @foreach($items as $item)
                                    <div class="govco-doc-card">
                                        <div class="govco-doc-icon">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="govco-doc-info">
                                            <h5 class="govco-doc-title">{{ $item->title }}</h5>
                                            <div class="govco-doc-meta">
                                                <span><i class="far fa-calendar-alt"></i> Publicado: {{ $item->created_at->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="govco-doc-action">
                                            <a href="{{ asset('storage/'.$item->document) }}" target="_blank" class="btn govco-btn-download">
                                                Descargar <i class="fas fa-download ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center py-5">
            <p class="mb-0">No hay información registrada para mostrar.</p>
        </div>
    @endif

</div>

<style>
    /* Tipografía General */
    .govco-fiscal-container {
        font-family: var(--govco-font-primary);
        color: var(--govco-tertiary-color);
    }

    .govco-page-title {
        color: var(--govco-secondary-color);
        font-family: var(--govco-font-primary);
        font-weight: 800;
        margin-bottom: 1rem;
    }

    .govco-page-desc {
        max-width: 900px;
        color: var(--govco-tertiary-color);
        font-family: var(--govco-font-secondary);
    }

    .govco-section-title {
        color: var(--govco-secondary-color);
        border-bottom: 2px solid var(--govco-gray-color);
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }

    /* Menu Lateral Sticky */
    .govco-sticky-menu {
        position: sticky;
        top: 2rem;
        z-index: 10;
    }

    /* Estilos de los Tabs Verticales */
    .govco-vertical-tabs .nav-link {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: var(--govco-tertiary-color);
        font-family: var(--govco-font-secondary);
        font-weight: 600;
        background-color: transparent;
        border: 1px solid transparent;
        border-radius: var(--govco-border-radius);
        padding: 0.8rem 1rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .govco-vertical-tabs .nav-link:hover {
        background-color: var(--govco-gray-menu);
        color: var(--govco-secondary-color);
    }

    .govco-vertical-tabs .nav-link.active {
        background-color: var(--govco-secondary-color);
        color: var(--govco-white-color) !important;
        box-shadow: var(--govco-box-shadow);
    }

    .govco-vertical-tabs .nav-link.active .tab-year {
        color: #ffffff !important;
    }

    /* Badge del contador en el tab */
    .tab-badge {
        background-color: var(--govco-gray-color);
        color: var(--govco-tertiary-color);
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
        font-size: 0.75rem;
    }

    .govco-vertical-tabs .nav-link.active .tab-badge {
        background-color: var(--govco-white-color);
        color: var(--govco-secondary-color);
        font-weight: bold;
    }

    /* Cards de Documentos */
    .govco-doc-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .govco-doc-card {
        display: flex;
        align-items: center;
        background-color: var(--govco-white-color);
        border: 1px solid var(--govco-gray-color);
        border-radius: var(--govco-border-radius);
        padding: 1.2rem;
        transition: box-shadow 0.3s ease;
        gap: 1.5rem;
    }

    .govco-doc-card:hover {
        box-shadow: var(--govco-box-shadow);
        border-color: var(--govco-border-color);
    }

    /* Icono PDF */
    .govco-doc-icon {
        font-size: 2.2rem;
        color: var(--govco-error-color); /* Rojo GovCo */
        min-width: 50px;
        text-align: center;
    }

    .govco-doc-info {
        flex-grow: 1;
    }

    .govco-doc-title {
        color: var(--govco-black-color);
        font-family: var(--govco-font-primary);
        font-weight: 700;
        margin-bottom: 0.4rem;
        font-size: 1.1rem;
    }

    .govco-doc-meta {
        font-size: 0.85rem;
        color: var(--govco-tertiary-color);
        font-family: var(--govco-font-secondary);
    }

    /* Botón de descarga */
    .govco-btn-download {
        background-color: transparent;
        color: var(--govco-secondary-color);
        border: 1px solid var(--govco-secondary-color);
        border-radius: 50px;
        padding: 0.4rem 1.2rem;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s;
    }

    .govco-btn-download:hover {
        background-color: var(--govco-secondary-color);
        color: var(--govco-white-color);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .govco-vertical-tabs {
            flex-direction: row !important;
            overflow-x: auto;
            padding-bottom: 10px;
            margin-bottom: 1rem;
        }
        .govco-vertical-tabs .nav-link {
            white-space: nowrap;
            margin-bottom: 0;
            margin-right: 0.5rem;
        }
        .govco-doc-card {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        .govco-doc-action {
            width: 100%;
        }
        .govco-btn-download {
            display: block;
            width: 100%;
        }
    }
</style>
@endsection