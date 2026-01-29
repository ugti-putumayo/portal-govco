@extends('layouts.sidebar')

@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container">
    <h1>Plan Anual de Adquisiciones (PAA)</h1>

    <!-- Formulario de Búsqueda -->
    <form method="GET" action="{{ route('paa') }}" class="mb-3">
        <input type="text" name="search" placeholder="Buscar..." value="{{ request('search') }}" class="form-control" />
        <button type="submit" class="btn btn-primary mt-2">Buscar</button>
    </form>

    <!-- Tabla de Resultados -->
    <table class="table table-striped table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Período</th>
                <th>Nombre</th>
                <th>Archivo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paas as $paa)
                <tr>
                    <td>{{ $paa->period }}</td>
                    <td>{{ $paa->name }}</td>
                    <td><a href="{{ $paa->archive }}" target="_blank">Ver Archivo</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Paginación -->
    <div>
        {{ $paas->appends(request()->input())->links('vendor.pagination.custom') }}
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
