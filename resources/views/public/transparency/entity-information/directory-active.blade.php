@extends('public.transparency.shared.sidebar')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container">
    <h1 class="mb-4">Directorio de Servidores Públicos, Empleados o Contratistas</h1>
    <ul class="nav nav-tabs" id="directoryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="funcionarios-tab" data-bs-toggle="tab" href="#funcionarios" role="tab" aria-controls="funcionarios" aria-selected="true">Funcionarios</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="contratistas-tab" data-bs-toggle="tab" href="#contratistas" role="tab" aria-controls="contratistas" aria-selected="false">Directorio Contratistas</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="sigep-tab" data-bs-toggle="tab" href="#sigep" role="tab" aria-controls="sigep" aria-selected="false">Directorio de Servidores-Contratistas (SIGEP)</a>
        </li>
    </ul>
    
    <div class="tab-content mt-3" id="directoryTabsContent">
        <div class="tab-pane fade show active" id="funcionarios" role="tabpanel" aria-labelledby="funcionarios-tab">
            @include('public.transparency.partials.plantofficials_table', ['data' => $plantofficials])
        </div>
        
        <div class="tab-pane fade" id="contratistas" role="tabpanel" aria-labelledby="contratistas-tab">
            @include('public.transparency.partials.contractors_table', ['data' => $contractors])
        </div>
        
        <div class="tab-pane fade" id="sigep" role="tabpanel" aria-labelledby="sigep-tab">
            <div class="p-4 border rounded bg-light">
                <p>{!! $sigepInfo !!}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Estilo para el encabezado de la tabla */
    .table thead th {
        background-color: #e9f4fb;
        color: #004085;
        font-weight: bold;
    }

    /* Estilo para los ítems de la tabla */
    .table td, .table th {
        vertical-align: middle;
    }

    /* Borde y espaciado adicional */
    .table-bordered {
        border: 1px solid #dee2e6;
    }

    /* Personalización del buscador */
    .search-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .search-container input {
        max-width: 200px;
    }

    .nav-tabs .nav-link.active {
        background-color: #007bff;
        color: #fff;
    }

    .accordion-button {
        background-color: #f7faff;
        color: #007bff;
        font-weight: bold;
    }
</style>
@endpush
