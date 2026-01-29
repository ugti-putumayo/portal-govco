@extends('public.transparency.shared.sidebar')

@section('sidebar')
  @include('partials.sidebar')
@endsection

@section('title', $page->title ?? 'Plan Institucional')

@section('main-content')
@php
    $groupedItems = $page->items->groupBy(function($item) {
        return \Carbon\Carbon::parse($item->created_at)->format('Y');
    })->sortKeysDesc();
@endphp

<div class="container py-4 plan-wrapper">
    
    {{-- Título y Descripción --}}
    <section class="plan-hero mb-4">
        <h1 class="plan-title-action">{{ $page->title }}</h1>
        @if($page->body || $page->description)
            <div class="plan-description">
                {!! $page->body ?? $page->description !!}
            </div>
        @endif
    </section>

    @if($groupedItems->count() > 0)
        
        <div class="row">
            {{-- COLUMNA IZQUIERDA: TABS VERTICALES (AÑOS) --}}
            <div class="col-md-3 mb-4">
                <div class="nav flex-column nav-pills custom-vertical-tabs" 
                     id="v-pills-tab" 
                     role="tablist" 
                     aria-orientation="vertical">
                    @foreach($groupedItems as $year => $items)
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                id="v-pills-{{ $year }}-tab" 
                                data-toggle="pill" 
                                data-target="#v-pills-{{ $year }}" 
                                type="button" 
                                role="tab" 
                                aria-controls="v-pills-{{ $year }}" 
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ $year }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- COLUMNA DERECHA: CONTENIDO (TABLAS) --}}
            <div class="col-md-9">
                <div class="tab-content" id="v-pills-tabContent">
                    @foreach($groupedItems as $year => $items)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                             id="v-pills-{{ $year }}" 
                             role="tabpanel" 
                             aria-labelledby="v-pills-{{ $year }}-tab">
                            
                            <div class="table-responsive rounded-table-container">
                                <table class="table plan-table-govco">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th class="text-center" style="width: 150px;">Documento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <td class="align-middle title-cell">
                                                    {{ $item->title ?? $item->name }}
                                                </td>
                                                <td class="text-center align-middle">
                                                    @if($item->document)
                                                        <a href="{{ asset('storage/'.$item->document) }}" 
                                                           target="_blank" 
                                                           class="btn-ver-pdf">
                                                           Ver PDF
                                                        </a>
                                                    @else
                                                        <span>-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    @else
        <div class="alert alert-info">
            No hay documentos publicados.
        </div>
    @endif

</div>
@endsection

<style>
/* --- ESTRUCTURA GENERAL --- */
.plan-wrapper {
    font-family: var(--govco-font-primary, sans-serif) !important;
}

.plan-title-action {
    color: var(--govco-secondary-color) !important;
    font-weight: 700 !important;
    font-size: 2.2rem !important;
}

.plan-description, 
.plan-description p {
    color: var(--govco-secondary-color) !important;
    font-weight: 400 !important;
}

/* --- TABS VERTICALES --- */
.custom-vertical-tabs .nav-link {
    color: var(--govco-secondary-color) !important;
    font-weight: 600 !important;
    background-color: #fff !important;
    border: 1px solid #ddd !important;
    margin-bottom: 8px !important;
    border-radius: 8px !important;
    text-align: center !important;
    transition: all 0.3s ease !important;
}

.custom-vertical-tabs .nav-link:hover {
    background-color: #f0f4f8 !important;
}

.custom-vertical-tabs .nav-link.active {
    background-color: var(--govco-secondary-color) !important;
    color: #fff !important;
    border-color: var(--govco-secondary-color) !important;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
}

/* --- TABLA --- */
.rounded-table-container {
    border-radius: 10px !important;
    overflow: hidden !important;
    border: 1px solid #ddd !important;
}

.plan-table-govco {
    width: 100% !important;
    margin-bottom: 0 !important;
}

.plan-table-govco thead th {
    background-color: var(--govco-secondary-color) !important;
    color: #fff !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    border: none !important;
    padding: 15px !important;
}

.plan-table-govco tbody td {
    border-bottom: 1px solid #e5e5e5 !important;
    color: var(--govco-secondary-color) !important;
    padding: 15px !important;
    background-color: #fff !important;
}

.plan-table-govco tbody tr:last-child td {
    border-bottom: none !important;
}

.title-cell {
    font-weight: 500 !important;
    font-size: 1rem !important;
    color: var(--govco-secondary-color) !important;
}

.btn-ver-pdf {
    display: inline-block !important;
    padding: 6px 20px !important;
    border: 1px solid var(--govco-secondary-color) !important;
    color: var(--govco-secondary-color) !important;
    background: #fff !important;
    border-radius: 20px !important;
    text-decoration: none !important;
    font-weight: 600 !important;
    font-size: 0.9rem !important;
    transition: all 0.3s ease !important;
}

.btn-ver-pdf:hover {
    background-color: var(--govco-secondary-color) !important;
    color: #fff !important;
    text-decoration: none !important;
}
</style>