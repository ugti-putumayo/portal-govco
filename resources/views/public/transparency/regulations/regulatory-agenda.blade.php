@extends('public.transparency.shared.sidebar')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container py-4">
    <h1 class="mb-4">Agenda Regulatoria</h1>

    <p>
        La <strong>Agenda Regulatoria</strong> informa los proyectos normativos que la entidad tiene previsto emitir o modificar durante el año. 
        Su publicación responde a lo establecido en la <strong>Ley 1712 de 2014</strong> y permite garantizar transparencia y participación ciudadana.
    </p>

    <form method="GET" action="{{ route('regulatoryagenda') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar por título o descripción" value="{{ request('search') }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    {{-- Tabla con resultados --}}
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Fecha Estimada</th>
                <th>Estado</th>
                <th>Documento</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($regulatories as $item)
                <tr>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->estimated_date)->format('d/m/Y') }}</td>
                    <td>{{ $item->state }}</td>
                    <td>
                        @if ($item->document)
                            <a href="{{ asset('storage/' . $item->document) }}" target="_blank">Ver PDF</a>
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay registros.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Paginación --}}
    {{ $regulatories->withQueryString()->links() }}

    <p class="mt-3">
        Para participar en los procesos de consulta, puede enviar sus observaciones o aportes al correo institucional: 
        <a href="mailto:contacto@putumayo.gov.co">contactenos@putumayo.gov.co</a>
    </p>
</div>
@endsection