@extends('dashboard.dashboard')

@section('content')
<div class="container-modules with-app-navbar">
    <div class="navbar app-navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/series-white.svg') }}" class="app-navbar__icon" alt="Series">
            <h2 class="app-navbar__title">Gestión de Series</h2>
        </div>

        <form method="GET" action="{{ route('dashboard.series.index') }}" class="app-navbar__right navbar-filters filters-card">
            
            <select name="is_active" id="filter-status" class="filter-select">
                <option value="">Todos los estados</option>
                <option value="1" @selected(request('is_active') === '1')>Activa</option>
                <option value="0" @selected(request('is_active') === '0')>Inactiva</option>
            </select>

            <select name="dependency_id" id="filter-dependency" class="filter-select">
                <option value="">Todas las dependencias</option>
                @isset($dependencies)
                    @foreach ($dependencies as $dependency)
                        <option value="{{ $dependency->id }}" @selected(request('dependency_id') == $dependency->id)>
                            {{ $dependency->name }}
                        </option>
                    @endforeach
                @endisset
            </select>
            
            <input type="text" name="search" id="search-input" class="search-box" 
                   placeholder="Buscar por nombre o prefijo..." 
                   value="{{ request('search') }}">
            
            <button class="search-btn" type="submit" title="Buscar">🔍</button>
            <a href="{{ route('dashboard.series.index') }}" class="search-btn" title="Limpiar Filtros" style="text-decoration:none; line-height:28px;">❌</a>
        </form>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="content-modules">
        <div class="data-table" style="--cols: 70px 1.8fr 0.9fr 1.6fr 0.9fr 110px;">
            <div class="data-table-header">
                <div class="data-table-cell">ID</div>
                <div class="data-table-cell">Nombre</div>
                <div class="data-table-cell">Prefijo</div>
                <div class="data-table-cell">Dependencia</div>
                <div class="data-table-cell">Estado</div>
                <div class="data-table-cell">Acciones</div>
            </div>

            @forelse ($series as $serie)
                <div class="data-table-row {{ !$serie->is_active ? 'row-canceled' : '' }}">
                    <div class="data-table-cell" data-label="ID">
                        {{ $serie->id }}
                    </div>

                    <div class="data-table-cell" data-label="Nombre">
                        {{ $serie->name }}
                    </div>

                    <div class="data-table-cell" data-label="Prefijo">
                        {{ $serie->prefix }}
                    </div>

                    <div class="data-table-cell" data-label="Dependencia">
                        {{ optional($serie->dependency)->name ?? 'Sin asignar' }}
                    </div>

                    <div class="data-table-cell" data-label="Estado">
                        {{ $serie->is_active ? 'Activa' : 'Inactiva' }}
                    </div>

                    <div class="data-table-cell" data-label="Acciones">
                        <div class="action-icons">
                            @can('series.update')
                                <a href="#"
                                   class="btn-icon"
                                   title="Editar serie"
                                   onclick="openModalEditSeries({{ $serie->id }})">
                                    <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
                                </a>
                            @endcan

                            @can('series.delete')
                                <a href="#"
                                   class="btn-icon delete-btn"
                                   title="Desactivar serie"
                                   onclick="confirmAndDeleteSeries({{ $serie->id }})">
                                    <img src="{{ asset('icon/delete.svg') }}" alt="Desactivar">
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="data-table-row">
                    <div class="data-table-cell" style="grid-column:1 / -1; text-align:center;">
                        No se encontraron series registradas con los filtros aplicados.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="pagination-container" style="margin-top: 1.5rem;">
            {{ $series->links() }}
        </div>
    </div>
</div>

@can('series.create')
    <a href="#" title="Agregar serie" class="btn-floating" onclick="openModalCreateSeries()">
      <img src="{{ asset('icon/serie-create-white.svg') }}" alt="Agregar serie">
    </a>
@endcan
@endsection

@include('components.dashboard.consecutive.serie-create')
@include('components.dashboard.consecutive.serie-update')

@push('scripts')
<script>
function getCsrf() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
           || document.querySelector('input[name="_token"]')?.value;
}

async function confirmAndDeleteSeries(serieId) {
    const ok = await Confirm.open({
        title: 'Desactivar Serie',
        message: 'Esta acción desactivará la serie. ¿Deseas continuar?',
        confirmText: 'Desactivar',
        cancelText: 'Cancelar',
        danger: true
    });
    
    if (!ok) return;

    deleteSeries(serieId);
}

async function deleteSeries(serieId) {
    try {
        const r = await fetch(`{{ url('dashboard/series') }}/${serieId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': getCsrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const body = await r.json().catch(() => ({}));

        if (r.ok) {
            Toast.success(body.message || 'Serie desactivada con éxito.');
            setTimeout(() => location.reload(), 900);
        } else {
            Toast.error(body.message || 'No se pudo desactivar la serie.');
        }
    } catch (err) {
        Toast.error('Hubo un problema de red al desactivar la serie.');
    }
}

</script>
@endpush

<style>
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: var(--govco-secondary-color);
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
    transition: all 0.3s ease-in-out;
    box-shadow: var(--govco-box-shadow);
}

.navbar-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.container-modules {
    width: 100%;
    min-height: 100%;
}

.content-modules {
    margin: 1.5rem;
}

.navbar-title {
    color: var(--govco-white-color);
    font-family: var(--govco-font-primary);
    font-size: 20px;
    font-weight: bold;
}

.row-canceled {
    background-color: #ffebeb !important;
    border-left: 4px solid #dc3545;
}

.row-canceled .data-table-cell {
    color: #787878;
}
</style>