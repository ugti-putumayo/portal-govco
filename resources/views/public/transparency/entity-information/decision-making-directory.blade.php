@extends('public.transparency.shared.sidebar')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container">
    <h1>Procedimientos para la Toma de Decisiones</h1>

    <!-- Buscador -->
    <form method="GET" action="{{ route('decision_making_directory') }}" class="mb-3">
        <div class="search-container">
            <label for="show">Mostrar</label>
            <select id="show" class="form-select form-select-sm" style="width: auto;">
                <option>10</option>
                <option>20</option>
                <option>50</option>
            </select>
            <div class="input-group">
                <input type="text" class="form-control form-control-sm" placeholder="Buscar..." value="{{ request('search') }}" aria-label="Buscar" name="search">
                <button class="btn btn-primary btn-sm" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Tabla -->
    <table class="table table-striped table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Fecha de Ingreso</th>
                <th>Nombre</th>
                <th>Archivo</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach($decisions as $decision)
                <tr>
                    <td>{{ $decision->entry_date }}</td>
                    <td>{{ $decision->name }}</td>
                    <td>{{ $decision->archive }}</td>
                    
                    <td>
                        <a href="{{ $decision->link }}" target="_blank">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Paginación -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $decisions->appends(request()->input())->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Estilo para el encabezado de la tabla */
    .thead-light th {
        background-color: #f0f8ff;
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
