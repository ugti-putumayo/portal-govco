@php
use Illuminate\Support\Str;
@endphp
@extends('dashboard.dashboard')

@section('content')
<div class="container-modules with-app-navbar">
    <div class="navbar app-navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/consecutive-white.svg') }}" class="app-navbar__icon" alt="Consecutivos">
            <h2 class="app-navbar__title">Gestión de Consecutivos</h2>
        </div>

        <form method="GET" action="{{ route('dashboard.consecutives.index') }}" class="app-navbar__right navbar-filters filters-card">
            
            <select name="series_id" id="filter-series" class="filter-select">
                <option value="">Todas las series</option>
                @foreach ($series as $serie)
                    <option value="{{ $serie->id }}" @selected(request('series_id') == $serie->id)>
                        {{ $serie->name }} ({{ $serie->prefix }})
                    </option>
                @endforeach
            </select>

            <select name="status" id="filter-state" class="filter-select">
                <option value="">Todos los estados</option>
                <option value="Generated" @selected(request('status') == 'Generated')>Generado</option>
                <option value="Canceled" @selected(request('status') == 'Canceled')>Anulado</option>
            </select>

            <input type="text" name="search" id="search-input" class="search-box" 
                   placeholder="Buscar por #, asunto, destinatario..." 
                   value="{{ request('search') }}">
            
            <button class="search-btn" type="submit" title="Buscar">🔍</button>
            <a href="{{ route('dashboard.consecutives.index') }}" class="search-btn" title="Limpiar Filtros" style="text-decoration:none; line-height:28px;">❌</a>
        </form>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-danger"> {{-- Asumiendo que tienes una clase para error --}}
            {{ session('error') }}
        </div>
    @endif

    <div class="content-modules">
        <div class="data-table" style="--cols: 70px 1.2fr 1fr 1.6fr 1.1fr 1.1fr 1fr 0.8fr 110px;">
            <div class="data-table-header">
                <div class="data-table-cell">ID</div>
                <div class="data-table-cell">Consecutivo</div>
                <div class="data-table-cell">Serie</div>
                <div class="data-table-cell">Asunto</div>
                <div class="data-table-cell">Destinatario</div>
                <div class="data-table-cell">Usuario</div>
                <div class="data-table-cell">Fecha</div>
                <div class="data-table-cell">Estado</div>
                <div class="data-table-cell">Acciones</div>
            </div>

            @forelse ($consecutives as $consec)
                <div class="data-table-row {{ $consec->status == 'Canceled' ? 'row-canceled' : '' }}">
                    <div class="data-table-cell" data-label="ID">{{ $consec->id }}</div>

                    <div class="data-table-cell" data-label="Consecutivo">
                        <span style="font-weight: bold;">{{ $consec->full_consecutive }}</span>
                    </div>

                    <div class="data-table-cell" data-label="Serie">{{ $consec->series->name ?? 'N/A' }}</div>

                    <div class="data-table-cell" data-label="Asunto" title="{{ $consec->subject }}">
                        {{ Str::limit($consec->subject, 50) }}
                    </div>

                    <div class="data-table-cell" data-label="Destinatario">{{ $consec->recipient }}</div>

                    <div class="data-table-cell" data-label="Usuario">{{ $consec->user->name ?? 'N/A' }}</div>
                    
                    <div class="data-table-cell" data-label="Fecha">{{ $consec->created_at->format('Y-m-d H:i') }}</div>

                    <div class="data-table-cell" data-label="Estado">
                        {{ $consec->status == 'Canceled' ? 'Anulado' : 'Generado' }}
                    </div>

                    <div class="data-table-cell" data-label="Acciones">
                        <div class="action-icons">
                            {{-- Botón para modal "Ver Detalle" --}}
                            <a href="#" class="btn-icon" title="Ver Detalle" 
                               data-action="open-show-modal" 
                               data-consecutive-id="{{ $consec->id }}">
                                <img src="{{ asset('icon/eye.svg') }}" alt="Ver">
                            </a>
                            
                            @if ($consec->status != 'Canceled')
                                <a href="#" class="btn-icon" title="Editar"
                                    onclick="openModalEditConsecutive({{ $consec->id }})">
                                    {{-- Asegurate de tener un icono de lapiz, o usa un emoji temporal ✏️ --}}
                                    <img src="{{ asset('icon/edit.svg') }}" alt="Editar"> 
                                </a>

                                <a href="#" class="btn-icon" title="Anular"
                                   onclick="openModalCancelConsecutive({{ $consec->id }})"
                                   data-consecutive-id="{{ $consec->id }}">
                                    <img src="{{ asset('icon/cancel-document.svg') }}" alt="Anular"> {{-- Asegúrate de tener este ícono --}}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="data-table-row">
                    <div class="data-table-cell" style="grid-column:1 / -1; text-align:center;">
                        No se encontraron consecutivos con los filtros aplicados.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="pagination-container" style="margin-top: 1.5rem;">
            {{ $consecutives->links() }}
        </div>
    </div>
</div>

<a href="#" class="btn-floating" onclick="openModalCreateConsecutive()" alt="Agregar Consecutivo">+</a>
@can('series.create')
    <a href="#" title="Agregar serie" class="btn-floating-file" onclick="openModalCreateSeries()">
      <img src="{{ asset('icon/serie-create-white.svg') }}" alt="Agregar serie">
    </a>
@endcan

@endsection

@include('components.dashboard.consecutive.consecutive-create')
@include('components.dashboard.consecutive.consecutive-update')
@include('components.dashboard.consecutive.serie-create')
@include('components.dashboard.consecutive.serie-update')
@include('components.dashboard.consecutive.consecutive-cancel')
@include('components.dashboard.consecutive.consecutive-show')

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector('[data-action="open-create-modal"]')?.addEventListener('click', (e) => {
        e.preventDefault();
        window.dispatchEvent(new CustomEvent('open-modal-create-consecutive'));
    });

    document.querySelectorAll('[data-action="open-show-modal"]').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const consecutiveId = button.dataset.consecutiveId;
            window.dispatchEvent(new CustomEvent('open-modal-show-consecutive', { 
                detail: { id: consecutiveId } 
            }));
        });
    });

    document.querySelectorAll('[data-action="open-cancel-modal"]').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const consecutiveId = button.dataset.consecutiveId;
            window.dispatchEvent(new CustomEvent('open-modal-cancel-consecutive', { 
                detail: { id: consecutiveId } 
            }));
        });
    });
});
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

.submenu-icon-area {
  width: 30px;
  height: 30px;
  color: var(--govco-white-color);
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