@extends('public.transparency.shared.sidebar')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container">
    <h1>Publicación de la Ejecución de los Contratos</h1>

    <form method="GET" action="{{ route('execution') }}" class="mb-3">
        <input type="text" name="search" placeholder="Buscar..." value="{{ request('search') }}" class="form-control" />
        <button type="submit" class="btn btn-primary mt-2">Buscar</button>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Contrato</th>
                    <th>Dependencia</th>
                    <th>Contratista</th>
                    <th>NIT</th>
                    <th>Objeto</th>
                    <th>Fecha de Suscripción</th>
                    <th>Valor Total</th>
                    <th>Plazo / Duración</th>
                    <th>Adición en Tiempo</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Terminación</th>
                    <th>% Avance</th>
                    <th>Fecha Corte</th>
                </tr>
            </thead>
            <tbody>
                @foreach($executions as $execution)
                    <tr>
                        <td>{{ $execution->contract_number }}</td>
                        <td>{{ $execution->dependency }}</td>
                        <td>{{ $execution->contractor }}</td>
                        <td>{{ $execution->nit }}</td>
                        <td>{{ $execution->objective }}</td>
                        <td>{{ $execution->subscription_date }}</td>
                        <td>{{ number_format($execution->total_value, 2) }}</td>
                        <td>{{ $execution->duration }}</td>
                        <td>{{ $execution->time_addition }}</td>
                        <td>{{ $execution->start_date }}</td>
                        <td>{{ $execution->end_date }}</td>
                        <td>{{ $execution->progress_percentage }}%</td>
                        <td>{{ $execution->cutoff_date }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div>
        {{ $executions->appends(request()->input())->links('vendor.pagination.custom') }}
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
    
    /* Asegurar que la tabla se mantenga dentro del contenedor */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch; /* Para desplazamiento suave en dispositivos móviles */
    }

    /* Asegurar que la tabla sea completamente visible en pantallas más pequeñas */
    table {
        width: 100%;
    }
</style>
@endpush
