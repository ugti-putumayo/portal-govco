@extends('dashboard.dashboard')
@push('scripts')
<script>
async function deleteAssociation(associationId) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
             || document.querySelector('input[name="_token"]')?.value;

  const ok = await Confirm.open({
    title: 'Eliminar asociación',
    message: 'Esta acción no se puede deshacer. ¿Deseas continuar?',
    confirmText: 'Eliminar',
    cancelText: 'Cancelar',
    danger: true
  });
  if (!ok) return;

  try {
    const r = await fetch(`/dashboard/association/${associationId}`, {
      method: "DELETE",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": csrf
      }
    });
    const body = await r.json().catch(() => ({}));

    if (r.ok) {
      Toast.success(body.message || "Asociación eliminada con éxito.");
      setTimeout(() => location.reload(), 900);
    } else {
      Toast.error(body.message || "No se pudo eliminar la asociación.");
    }
  } catch (err) {
    Toast.error("Hubo un problema al eliminar la asociación.");
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const searchInput  = document.getElementById('search-input');
  const filterSelect = document.getElementById('filter-category');
  const rows         = Array.from(document.querySelectorAll('.data-table-row'));

  function normalizeText(text) {
    return (text || '')
      .toString()
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "");
  }

  function searchAssociation() {
    const searchValue = normalizeText(searchInput.value.trim());
    const filterValue = filterSelect.value;

    rows.forEach(row => {
      const name = normalizeText(row.dataset.name);
      const clas = normalizeText(row.dataset.classification);
      const act  = normalizeText(row.dataset.activity);
      const city = normalizeText(row.dataset.city);

      let match = false;
      if (filterValue === "")                 match = name.includes(searchValue);
      else if (filterValue === "classification") match = clas.includes(searchValue);
      else if (filterValue === "activity")       match = act.includes(searchValue);
      else if (filterValue === "city")           match = city.includes(searchValue);

      row.style.display = match ? "" : "none";
    });
  }

  searchInput.addEventListener("keyup", () => {
    if (filterSelect.value === "") searchAssociation();
  });

  document.querySelector(".search-btn")?.addEventListener("click", searchAssociation);
  filterSelect.addEventListener("change", searchAssociation);
});
</script>
@endpush

@section('content')
<div class="container-modules with-app-navbar">
  <div class="navbar app-navbar">
    <div class="app-navbar__left">
      <img src="{{ asset('icon/users-white.svg') }}" class="app-navbar__icon" alt="Usuarios">
      <h2 class="app-navbar__title">Asociaciones</h2>
    </div>

    <div class="app-navbar__right navbar-filters filters-card">
      <select id="filter-category" class="filter-select">
        <option value="">Filtrar por...</option>
        <option value="classification">Clasificación</option>
        <option value="activity">Actividad</option>
        <option value="city">Municipio</option>
      </select>

      <input type="text" id="search-input" class="search-box" placeholder="Buscar asociación...">
      <button class="search-btn" type="button">🔍</button>
    </div>
  </div>

  @if(session('success'))
    <div class="alert-success">
      {{ session('success') }}
    </div>
  @endif

  <div class="content-modules">
    <div class="data-table">
      <div class="data-table-header">
        <div class="data-table-cell">ID</div>
        <div class="data-table-cell">Nombre</div>
        <div class="data-table-cell">Clasificación</div>
        <div class="data-table-cell">Actividad</div>
        <div class="data-table-cell">Ciudad</div>
        <div class="data-table-cell">Imagen</div>
        <div class="data-table-cell">Acciones</div>
      </div>

      @foreach ($associations as $a)
        <div class="data-table-row"
             data-name="{{ $a->name }}"
             data-classification="{{ $a->classification }}"
             data-activity="{{ $a->activity }}"
             data-city="{{ $a->city }}">
          <div class="data-table-cell" data-label="ID">{{ $a->id }}</div>
          <div class="data-table-cell" data-label="Nombre">{{ $a->name }}</div>
          <div class="data-table-cell" data-label="Clasificación">{{ $a->classification }}</div>
          <div class="data-table-cell" data-label="Actividad">{{ $a->activity }}</div>
          <div class="data-table-cell" data-label="Ciudad">{{ $a->city }}</div>

          <div class="data-table-cell" data-label="Imagen">
            @if($a->image)
              <img src="{{ asset('storage/' . $a->image) }}" alt="imagen" class="table-img">
            @else
              <span style="opacity:.6;">Sin imagen</span>
            @endif
          </div>

          <div class="data-table-cell" data-label="Acciones">
            <div class="action-icons">
              <a href="#" class="btn-icon" onclick="openModalEditAssociation({{ $a->id }})" title="Editar">
                <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
              </a>
              <a href="#" class="btn-icon" onclick="deleteAssociation({{ $a->id }})" title="Eliminar">
                <img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar">
              </a>
            </div>

            <form id="delete-form-{{ $a->id }}" action="{{ route('dashboard.association.destroy', $a->id) }}" method="POST" style="display:none;">
              @csrf
              @method('DELETE')
            </form>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<a href="#" class="btn-floating" onclick="openModalAssociation()">+</a>
@endsection

@include('components.administration.association.modal-create-association')
@include('components.administration.association.modal-update-association')

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
</style>