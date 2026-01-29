@extends('public.transparency.shared.sidebar')
@section('title', 'Estados Financieros')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container-ms">
    <div class="container my-5">
        <h1 class="titulo-personalizado">Informe Financieros</h1>
        <p>En esta sección encontrará los Estados Financieros de nuestra entidad, una herramienta fundamental para la transparencia y la rendición de cuentas.</p>
    </div>

    <div class="container my-5">
        {{-- Muestra la lista de años --}}
        @if (isset($years))
            <h1 class="titulo-personalizado">Estados Financieros por Año</h1>
            <div class="list-group">
                @forelse ($years as $year)
                    <a href="{{ route('estados-financieros.year', $year) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Informes del año {{ $year }}
                        <span class="badge bg-primary rounded-pill">Ver Informes</span>
                    </a>
                @empty
                    <p class="text-center">No hay informes disponibles.</p>
                @endforelse
            </div>
        @endif

        {{-- Muestra la tabla de detalles para un año --}}
        @if (isset($registros))
            <h1 class="titulo-personalizado">Estados Financieros del Año {{ $year }}</h1>
            <div class="my-4">
                <a href="{{ route('estados-financieros.index') }}" class="btn btn-secondary">&larr; Volver a la lista de años</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Fecha de Expedición</th>
                            <th scope="col">Concepto / Descripción</th>
                            <th scope="col">Documento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($registros as $mes => $itemsDelMes)
                            <tr class="table-info">
                                <th colspan="3" class="text-center text-uppercase">{{ $mes }}</th>
                            </tr>
                            @foreach ($itemsDelMes as $registro)
                                <tr>
                                    <td>{{ $registro->expedition_date->format('Y-m-d') }}</td>
                                    <td>{{ $registro->description ?? 'No hay descripción' }}</td>
                                    <td>
                                        <a href="{{ asset('storage/app/public/' . $registro->file_path) }}" target="_blank" class="btn btn-primary btn-sm">
                                            Descargar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No hay documentos disponibles para este año.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection