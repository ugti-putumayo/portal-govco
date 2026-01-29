@extends('dashboard.dashboard')
@push('scripts')
<script>
async function deleteEntitySetting(settingId) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
             || document.querySelector('input[name="_token"]')?.value;

  const ok = await Confirm.open({
    title: 'Eliminar configuración',
    message: 'Esta acción no se puede deshacer. ¿Deseas continuar?',
    confirmText: 'Eliminar',
    cancelText: 'Cancelar',
    danger: true
  });
  if (!ok) return;

  try {
    const resp = await fetch(`/dashboard/settings/${settingId}`, {
      method: "DELETE",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": csrf
      }
    });
    const body = await resp.json().catch(() => ({}));

    if (resp.ok) {
      Toast.success(body.message || 'Configuración eliminada con éxito.');
      setTimeout(() => location.reload(), 900);
    } else {
      Toast.error(body.message || 'No se pudo eliminar la configuración.');
    }
  } catch (e) {
    Toast.error('Hubo un problema al eliminar la configuración.');
  }
}
</script>
@endpush

@section('content')
<div class="container-modules">
    <div class="navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/setting-white.svg') }}" class="submenu-icon-area">
            <h2 class="navbar-title">Configuración</h2>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="config-layout">
        @foreach ($settings as $a)
        <div class="entity-card-horizontal">
            <div class="entity-logo-section">
                @if($a->logo_path)
                    <img src="{{ asset('img/settings/gobernacion.png') }}" alt="Logo" class="entity-logo-xl">
                @else
                    <div class="no-logo">Sin imagen</div>
                @endif
            </div>
            <div class="entity-info-section">
                <h2 class="entity-title">{{ $a->entity_name }}</h2>
                <ul class="entity-info-list">
                    <li><strong>NIT:</strong> {{ $a->document_number }}</li>
                    <li><strong>Representante Legal:</strong> {{ $a->legal_representative }}</li>    
                    <li><strong>Acrónimo:</strong> {{ $a->entity_acronym }}</li>                
                    <li><strong>Dirección:</strong> {{ $a->address }}</li>
                    <li><strong>Teléfono:</strong> {{ $a->phone }}</li>
                    <li><strong>Email:</strong> {{ $a->email }}</li>
                    <li><strong>Departamento:</strong> {{ $a->department }}</li>
                    <li><strong>Ciudad:</strong> {{ $a->city }}</li>
                    <li><strong>Sitio Web:</strong> <a href="{{ $a->website }}" target="_blank">{{ $a->website }}</a></li>                    
                </ul>
                <div class="entity-actions-horizontal">
                    <a href="#" onclick="openModalEditEntitySetting({{ $a->id }})"><img src="{{ asset('icon/edit.svg') }}" alt="Editar"></a>
                    <a href="#" onclick="deleteEntitySetting({{ $a->id }})"><img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar"></a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@if($settings->isEmpty())
    <a href="#" class="btn-floating" onclick="openModalEntitySetting()">+</a>
@endif
@endsection

@include('components.administration.entity-setting.modal-create-entity-setting')
@include('components.administration.entity-setting.modal-update-entity-setting')

<style>
/* Navbar fija */
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
}

.navbar-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.navbar-title {
    color: var(--govco-white-color);
    font-family: var(--govco-font-primary);
    font-size: 20px;
    font-weight: bold;
}

.submenu-icon-area {
    width: 30px;
    height: 30px;
    color: white;
}

/* Contenedor principal alineado a la izquierda */
.config-layout {
    padding: 2rem;
    display: flex;
    justify-content: flex-start;
}

/* Tarjeta horizontal institucional */
.entity-card-horizontal {
    display: flex;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 12px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    padding: 20px;
    max-width: 700px;
    width: 100%;
    gap: 30px;
}

/* Sección del logo */
.entity-logo-section {
    flex: 0 0 180px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.entity-logo-xl {
    max-width: 160px;
    max-height: 160px;
    object-fit: contain;
    border-radius: 8px;
}

.no-logo {
    font-size: 0.8rem;
    color: #aaa;
    text-align: center;
}

/* Sección de datos */
.entity-info-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.entity-title {
    font-size: 1.5rem;
    color: var(--govco-primary-color);
    font-weight: 700;
    margin-bottom: 1rem;
}

.entity-info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.entity-info-list li {
    margin-bottom: 6px;
    font-size: 0.95rem;
}

.entity-info-list a {
    color: #007bff;
    text-decoration: none;
}

/* Botones */
.entity-actions-horizontal {
    display: flex;
    gap: 15px;
    margin-top: 1rem;
}
</style>