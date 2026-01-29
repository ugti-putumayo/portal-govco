@extends('layouts.sidebar')

@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container">
    <h1>Decreto Único Reglamentario</h1>
    <p>Los decretos únicos reglamentarios son normas que consolidan la regulación en diversas áreas, y dependiendo del tema, pueden establecer reglas que las Gobernaciones deben cumplir.</p>

    <!-- Formulario de Búsqueda -->
    <form method="GET" action="{{ route('decretoreglamentario') }}" class="mb-3">
        <input type="text" name="search" placeholder="Buscar..." value="{{ request('search') }}" class="form-control" />
        <button type="submit" class="btn btn-primary mt-2">Buscar</button>
    </form>

    <!-- Tabla de Resultados -->
    <table class="table table-striped table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Decreto Aplicable</th>
                <th>Objetivo</th>
                <th>Ámbitos Regulados</th>
                <th>Obligaciones</th>
                <th>Cumplimiento y Evaluación</th>
                <th>Documentos Políticas Relacionadas</th>
                <th>Actualizaciones</th>
                <th>Consultas Ciudadanas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($decretos as $decreto)
                <tr>
                    <td>{{ $decreto->decreto_aplicable }}</td>
                    <td>{{ $decreto->objetivo }}</td>
                    <td>{{ $decreto->ambitos_regulados }}</td>
                    <td>{{ $decreto->obligaciones }}</td>
                    <td>{{ $decreto->cumplimiento_evaluacion }}</td>
                    <td>{{ $decreto->documentos_politicas_relacionadas }}</td>
                    <td>{{ $decreto->actualizaciones }}</td>
                    <td>{{ $decreto->consultas_ciudadanas }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Paginación -->
    <div>
        {{ $decretos->appends(request()->input())->links('vendor.pagination.custom') }}
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
