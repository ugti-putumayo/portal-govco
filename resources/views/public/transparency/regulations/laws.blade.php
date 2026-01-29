@extends('public.transparency.shared.sidebar')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container">
    <h1>Listado de Leyes</h1>
    <p>Las leyes establecen derechos, deberes, prohibiciones y permisos con el fin de garantizar el orden, la justicia y la convivencia pacífica en un país.</p>
    <form method="GET" action="{{ route('laws') }}" class="mb-3">
        <input type="text" name="search" placeholder="Buscar..." value="{{ request('search') }}" class="form-control" />
        <button type="submit" class="btn btn-primary mt-2">Buscar</button>
    </form>
    <table class="table table-striped table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Fecha de Expedición</th>
                <th>Número</th>
                <th>Nombre</th>
                <th>Tema</th>
                <th>Enlace</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laws as $law)
                <tr>
                    <td>{{ $law->expedition_date }}</td>
                    <td>{{ $law->number }}</td>
                    <td>{{ $law->name }}</td>
                    <td>{{ $law->theme }}</td>
                    <td><a href="{{ $law->link }}" target="_blank">Ver Ley</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div>
        {{ $laws->appends(request()->input())->links('vendor.pagination.custom') }}
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
