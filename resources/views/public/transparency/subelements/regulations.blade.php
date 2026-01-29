@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Normativa Aplicable</h1>
    <p>Se refiere al conjunto de leyes, reglamentos, directrices y estándares que son relevantes y deben cumplirse en un contexto específico.</p>

    <!-- Formulario de Búsqueda -->
    <form method="GET" action="{{ route('regulations') }}" class="mb-3">
        <input type="text" name="search" placeholder="Buscar..." value="{{ request('search') }}" class="form-control" />
        <button type="submit" class="btn btn-primary mt-2">Buscar</button>
    </form>

    <!-- Tabla de Resultados -->
    <table class="table table-striped table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Fecha de Expedición</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Tema</th>
                <th>Enlace</th>
            </tr>
        </thead>
        <tbody>
            @foreach($regulations as $regulation)
                <tr>
                    <td>{{ $regulation->expedition_date }}</td>
                    <td>{{ $regulation->name }}</td>
                    <td>{{ $regulation->tipo }}</td>
                    <td>{{ $regulation->theme }}</td>
                    <td><a href="{{ $regulation->link }}" target="_blank">Ver Documento</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Paginación -->
    <div>
        {{ $regulations->appends(request()->input())->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .thead-light th {
        background-color: #f0f8ff;
        font-weight: bold;
    }

    .table-bordered th, .table-bordered td {
        border: 1px solid #dee2e6;
    }

    .table tbody tr td {
        vertical-align: middle;
    }

    .pagination .page-link {
        font-size: 14px;
        padding: 0.5rem 0.75rem;
        color: #007bff;
        background-color: #ffffff;
        border: 1px solid #dee2e6;
    }

    .pagination .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        padding: 0;
    }

    input.form-control {
        max-width: 300px;
    }
</style>
@endpush
