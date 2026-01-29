@extends('dashboard.dashboard')
@push('scripts')
<script>
async function deleteEntity(entityId) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
             || document.querySelector('input[name="_token"]')?.value;

  const ok = await Confirm.open({
    title: 'Eliminar entidad',
    message: 'Esta acción no se puede deshacer. ¿Deseas continuar?',
    confirmText: 'Eliminar',
    cancelText: 'Cancelar',
    danger: true
  });
  if (!ok) return;

  try {
    const resp = await fetch(`/dashboard/entities/${entityId}`, {
      method: "DELETE",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": csrf
      }
    });
    const body = await resp.json().catch(() => ({}));

    if (resp.ok) {
      Toast.success(body.message || 'Entidad eliminada con éxito.');
      setTimeout(() => location.reload(), 900);
    } else {
      Toast.error(body.message || 'No se pudo eliminar la entidad.');
    }
  } catch (e) {
    Toast.error('Hubo un problema al eliminar la entidad.');
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const searchInput  = document.getElementById('search-input');
  const filterSelect = document.getElementById('filter-category');
  const rows         = document.querySelectorAll('.styled-table tbody tr');

  function normalizeText(text) {
    return (text || '').toString().toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  }

  function searchEntity() {
    const searchValue = normalizeText(searchInput.value.trim());
    const filterValue = filterSelect.value;

    rows.forEach(row => {
      const typeEntity  = normalizeText(row.cells[1].innerText);
      const scopeEntity = normalizeText(row.cells[2].innerText);
      const nameEntity  = normalizeText(row.cells[3].innerText);

      let match = false;
      if (filterValue === "")        match = nameEntity.includes(searchValue);
      else if (filterValue === "type")  match = typeEntity.includes(searchValue);
      else if (filterValue === "scope") match = scopeEntity.includes(searchValue);

      row.style.display = match ? "" : "none";
    });
  }

  searchInput.addEventListener("keyup", () => {
    if (filterSelect.value === "") searchEntity();
  });
  document.querySelector(".search-btn")?.addEventListener("click", searchEntity);
});
</script>
@endpush

@section('content')
<div class="container-modules">
    <div class="navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/entity-white.svg') }}" class="submenu-icon-area">
            <h2 class="navbar-title">Entidades públicas</h2>
        </div>

        <div class="navbar-filters">
            <select id="filter-category" class="filter-select">
                <option value="">Filtrar por...</option>
                <option value="type">Tipo</option>
                <option value="scope">Alcance</option>
            </select>

            <input type="text" id="search-input" class="search-box" placeholder="Buscar entidad...">
            <button class="search-btn" onclick="searchEntity()">🔍</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="content-modules">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Alcance</th>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Email</th>
                    <th>Dirección</th>
                    <th>Url</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entities as $e)
                    <tr>
                        <td>{{ $e->id }}</td>
                        <td>{{ $e->type }}</td>
                        <td>{{ $e->scope }}</td>
                        <td>{{ $e->name }}</td>
                        <td>{{ $e->phone }}</td>
                        <td>{{ $e->mail }}</td>
                        <td>{{ $e->address }}</td>
                        <td>
                            <a href="{{ $e->link }}" target="_blank" rel="noopener noreferrer">
                                Ver enlace
                            </a>
                        </td>
                        <td>
                            <div class="action-icons">
                                <a href="#" class="btn-icon" onclick="openModalEditEntity({{ $e->id }})">
                                    <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
                                </a>
                                <a href="#" class="btn-icon" onclick="deleteEntity({{ $e->id }})">
                                    <img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar">
                                </a>
                            </div>

                            <form id="delete-form-{{ $e->id }}" action="{{ route('dashboard.entities.destroy', $e->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
     </div>
</div>
<a href="#" class="btn-floating" onclick="openModalEntity()">+</a>
@endsection
@include('components.administration.entity.modal-create-entity')
@include('components.administration.entity.modal-update-entity')

<style>
.navbar {
    position: fixed;
    top: 0;
    left: 0; /* Ajustamos a la izquierda para que no se desborde */
    min-width: 100%; /* Ocupará todo el ancho */
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
    align-items: center; /* Centrar verticalmente */
    gap: 10px; /* Espacio entre el icono y el texto */
}

.submenu-icon-area {
    width: 30px; /* Ajusta el tamaño del icono */
    height: 30px;
    color: white;
}


/* Ajustar el contenedor para que no se solape con la navbar */
.container-modules {
    min-width: 100%;
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

/* Contenedor de filtros */
.navbar-filters {
    display: flex;
    gap: 10px;
    align-items: center;
}

/* Estilo del Select */
.filter-select {
    padding: 8px;
    border-radius: var(--govco-border-radius);
    border: 1px solid var(--govco-border-color);
    font-family: var(--govco-font-primary);
}

/* Estilo del Input de Búsqueda */
.search-box {
    padding: 8px;
    border-radius: var(--govco-border-radius);
    border: 1px solid var(--govco-border-color);
    font-family: var(--govco-font-primary);
}

/* Estilo del Botón de Búsqueda */
.search-btn {
    padding: 8px 12px;
    border: none;
    background-color: var(--govco-accent-color);
    color: var(--govco-white-color);
    border-radius: var(--govco-border-radius);
    cursor: pointer;
}

.search-btn:hover {
    background-color: var(--govco-primary-color);
}

.title {
    color: var(--govco-primary-color);
    font-family: var(--govco-font-primary);
    margin-bottom: 20px;
}

/* Alertas */
.alert-success {
    background-color: var(--govco-success-color);
    color: var(--govco-white-color);
    padding: 10px;
    border-radius: var(--govco-border-radius);
    margin-bottom: 15px;
}

/* Tabla */
.styled-table {
    width: 100%;
    border-collapse: collapse;
    background: var(--govco-white-color);
    border: 1px solid var(--govco-border-color);
}

.styled-table thead tr {
    text-align: center;
}

.styled-table thead th {
    text-align: center;
    vertical-align: middle;
}

.styled-table th, .styled-table td {
    border: 1px solid var(--govco-border-color);
    padding: 10px;
    text-align: left;
}

.styled-table th {
    background: var(--govco-secondary-color);
    color: var(--govco-white-color);
    font-family: var(--govco-font-primary);
}

.styled-table tr:nth-child(even) {
    background: var(--govco-gray-color);
}

.action-icons {
    display: flex;
    align-items: center;
    gap: 10px; /* Espacio entre iconos */
}

.btn-icon img {
    width: 24px;
    height: 24px;
    cursor: pointer;
    transition: transform 0.2s ease-in-out;
}

.btn-icon img:hover {
    transform: scale(1.1);
}
</style>