@extends('transparencu.shared.sidebar')
@section('title', 'Presupuestal')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container-ms">
    <div class="container my-5">
        <h1 class="titulo-personalizado">Informe de Presupuestales</h1>
        <p>En esta selección encontrará la Ejecución Presupuestal de nuestra entidad, una herramienta fundamental para la transparencia y la rendición de cuentas.</p>
    </div>

    <div class="container my-5">
        @if (isset($years))
            <h1 class="titulo-personalizado">Ejecución Presupuestal por Año</h1>
            <div class="list-group">
                @forelse ($years as $year)
                    <a href="{{ route('presupuestal.year', $year) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Ejecuciones del año {{ $year }}
                        <span class="badge bg-primary rounded-pill">Ver Ejecuciones</span>
                    </a>
                @empty
                    <p class="text-center">No hay ejecuciones presupuestales disponibles.</p>
                @endforelse
            </div>
        @endif

        @if (isset($ejecuciones))
            <h1 class="titulo-personalizado">Ejecución Presupuestal del Año {{ $year }}</h1>
            <div class="my-4">
                <a href="{{ route('presupuestal.index') }}" class="btn btn-secondary">&larr; Volver a la lista de años</a>
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
                        @forelse ($ejecuciones as $mes => $itemsDelMes)
                            {{-- Fila que actúa como cabecera del mes --}}
                            <tr class="table-info">
                                <th colspan="3" class="text-center text-uppercase">{{ $mes }}</th>
                            </tr>
                            {{-- Bucle para los documentos de ese mes --}}
                            @foreach ($itemsDelMes as $ejecucion)
                                <tr>
                                    <td>{{ $ejecucion->expedition_date->format('Y-m-d') }}</td>
                                    <td>{{ $ejecucion->description ?? 'No hay descripción' }}</td>
                                    <td>
                                        <a href="{{ asset('storage/app/public/' . $ejecucion->file_path) }}" target="_blank" class="btn btn-primary btn-sm">
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