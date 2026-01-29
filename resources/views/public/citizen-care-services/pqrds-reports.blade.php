@extends('layouts.app')

@section('content')
<div class="container mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
            Informe Consolidado de PQRDS por Periodo y Vigencia
        </h2>
    </div>

    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="{{ route('pqrds.external') }}" class="mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="year" class="form-label">Año</label>
                <input type="number" name="year" value="{{ $year }}" class="form-control" min="2000" max="{{ date('Y') }}">
            </div>
            <div class="col-md-6">
                <label for="trimester" class="form-label">Trimestre</label>
                <select name="trimester" class="form-control">
                    <option value="Q1" {{ $trimester == 'Q1' ? 'selected' : '' }}>Primer Trimestre</option>
                    <option value="Q2" {{ $trimester == 'Q2' ? 'selected' : '' }}>Segundo Trimestre</option>
                    <option value="Q3" {{ $trimester == 'Q3' ? 'selected' : '' }}>Tercer Trimestre</option>
                    <option value="Q4" {{ $trimester == 'Q4' ? 'selected' : '' }}>Cuarto Trimestre</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Filtrar</button>
    </form>

    @if(isset($relatedDocument) && $relatedDocument)
        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <div>
                <strong>Documento Relacionado:</strong> {{ $relatedDocument->title }}
            </div>
            
            <a href="{{ asset('storage/app/public/' . $relatedDocument->document) }}" target="_blank" class="btn btn-sm btn-primary">
                <i class="fas fa-download"></i> Descargar Informe
            </a>
        </div>
    @endif

    @if ($isExternal)
        
        {{-- 2. GRID DE GRÁFICAS 2x2 --}}
        <div class="row mt-5">
            <div class="col-md-6 mb-4">
                <h5>Radicadas vs. Tramitadas por Departamento</h5>
                <canvas id="chartDepartamentos"></canvas>
            </div>
            <div class="col-md-6 mb-4">
                <h5>Radicadas vs. Tramitadas por Tipo Documento</h5>
                <canvas id="chartDocTipos"></canvas>
            </div>
            <div class="col-md-6 mb-4">
                <h5>Total por Medio de Recepción</h5>
                <canvas id="chartMedios"></canvas>
            </div>
            <div class="col-md-6 mb-4">
                <h5>Total de Radicados por Mes</h5>
                <canvas id="chartMes"></canvas>
            </div>
        </div>

        <h3 class="mt-5">Matriz de PQRDS por Departamento y Tipo</h3>
        <div class="table-responsive mt-3">
            <table class="table table-bordered compact-table">
                <thead>
                    <tr>
                        <th style="min-width: 250px;">Departamento Responsable</th>
                        
                        @foreach($pivotDocTypes as $tipo)
                            <th>{{ $tipo }}</th>
                        @endforeach

                        <th class="table-info">Total por Depto.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pivotTable as $row)
                        <tr>
                            <td>{{ $row['departamento'] }}</td>
                            
                            @foreach($pivotDocTypes as $tipo)
                                <td>{{ $row['counts'][$tipo] ?? 0 }}</td>
                            @endforeach

                            <td class="table-info"><strong>{{ $row['total_fila'] }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $pivotDocTypes->count() + 2 }}">No se encontraron datos para los filtros seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <td><strong>Total por Tipo</strong></td>
                        
                        @foreach($pivotDocTypes as $tipo)
                            <td><strong>{{ $pivotColumnTotals[$tipo] ?? 0 }}</strong></td>
                        @endforeach

                        <td><strong>{{ $pivotGrandTotal }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    @endif
</div>

<style>
    .compact-table { font-size: 13px; border-collapse: collapse; }
    .compact-table th { background-color: #0033A0; color: #fff; text-align: center; padding: 8px; }
    .compact-table td { padding: 6px; text-align: center; }
    .compact-table tbody tr:nth-child(even) { background-color: #f2f2f2; }
    .compact-table tbody tr:hover { background-color: #e6e6e6; }
    .compact-table th, .compact-table td { border: 1px solid #ddd; }
    
    .table-info, .table-info > th, .table-info > td {
        background-color: #cfe2ff !important;
        font-weight: bold;
    }
    .compact-table tfoot .table-info td {
        border-color: #9ec5fe;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if ($isExternal)
<script>
    const ctxDept = document.getElementById('chartDepartamentos').getContext('2d');
    new Chart(ctxDept, {
        type: 'bar',
        data: {
            labels: @json($deptLabels),
            datasets: [{
                label: 'Radicadas',
                data: @json($deptRadicadas),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
            }, {
                label: 'Tramitadas',
                data: @json($deptTramitadas),
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
            }]
        }
    });

    const ctxTipo = document.getElementById('chartDocTipos').getContext('2d');
    new Chart(ctxTipo, {
        type: 'bar',
        data: {
            labels: @json($tipoLabels),
            datasets: [{
                label: 'Radicadas',
                data: @json($tipoRadicadas),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
            }, {
                label: 'Tramitadas',
                data: @json($tipoTramitadas),
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
            }]
        }
    });

    const ctxMed = document.getElementById('chartMedios').getContext('2d');
    new Chart(ctxMed, {
        type: 'bar',
        data: {
            labels: @json($mediosLabels),
            datasets: [{
                label: 'Medios de Recepción',
                data: @json($mediosData),
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
            }]
        },
        options: {
            indexAxis: 'y',
        }
    });

    const ctxMes = document.getElementById('chartMes').getContext('2d');
    new Chart(ctxMes, {
        type: 'line',
        data: {
            labels: @json($mesLabels),
            datasets: [{
                label: 'Total Radicados por Mes',
                data: @json($mesData),
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                fill: true,
            }]
        }
    });
</script>
@endif

@endsection