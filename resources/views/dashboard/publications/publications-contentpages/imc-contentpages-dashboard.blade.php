@extends('dashboard.dashboard')
@section('content')
<div class="container-modules with-app-navbar">
  <div class="navbar app-navbar">
    <div class="navbar-header-title">
      <img src="{{ asset('icon/users-white.svg') }}" class="app-navbar__icon" alt="Páginas">
      <h2 class="app-navbar__title">Content Pages</h2>
    </div>

    <div class="app-navbar__right navbar-filters filters-card">
      <select id="filter-category" class="filter-select">
        <option value="all">Buscar en...</option>
        <option value="title">Título</option>
        <option value="slug">Slug</option>
        <option value="module">Módulo</option>
      </select>

      <select id="filter-state" class="filter-select">
        <option value="">Todos</option>
        <option value="1">Activos</option>
        <option value="0">Inactivos</option>
      </select>

      <input type="text" id="search-input" class="search-box" placeholder="Buscar página...">
      <button class="search-btn" type="button">🔍</button>
    </div>
  </div>

  @if(session('success'))
    <div class="alert-success">
      {{ session('success') }}
    </div>
  @endif

  @php
    $pages = $pages ?? ($contentpages ?? collect());
  @endphp

  <div class="content-modules">
    <div class="data-table" style="--cols: 70px 1.6fr 1.1fr 1.1fr 1fr 110px 120px 120px;">
      <div class="data-table-header">
        <div class="data-table-cell">ID</div>
        <div class="data-table-cell">Título</div>
        <div class="data-table-cell">Slug</div>
        <div class="data-table-cell">Módulo</div>
        <div class="data-table-cell">Orden</div>
        <div class="data-table-cell">Estado</div>
        <div class="data-table-cell">Imagen</div>
        <div class="data-table-cell">Acciones</div>
      </div>

      @forelse ($pages as $p)
        <div class="data-table-row"
            data-title="{{ $p->title }}"
            data-slug="{{ $p->slug }}"
            data-module="{{ $p->module }}"
            data-state="{{ $p->state ? '1' : '0' }}">

          {{-- 1) ID --}}
          <div class="data-table-cell" data-label="ID">{{ $p->id }}</div>

          {{-- 2) Título --}}
          <div class="data-table-cell" data-label="Título">{{ $p->title }}</div>

          {{-- 3) Slug --}}
          <div class="data-table-cell" data-label="Slug">{{ $p->slug }}</div>

          {{-- 4) Módulo --}}
          <div class="data-table-cell" data-label="Módulo">{{ $p->module }}</div>

          {{-- 5) Orden --}}
          <div class="data-table-cell" data-label="Orden">{{ $p->ordering }}</div>

          {{-- 6) Estado (texto plano para que se vea con tu CSS global) --}}
          <div class="data-table-cell" data-label="Estado">
            {{ $p->state ? 'Activo' : 'Inactivo' }}
          </div>

          {{-- 7) Imagen --}}
          <div class="data-table-cell" data-label="Imagen">
            @if($p->image)
              <img src="{{ asset($p->image) }}" alt="imagen" class="table-img">
            @else
              <span>Sin imagen</span>
            @endif
          </div>

          {{-- 8) Acciones --}}
          <div class="data-table-cell" data-label="Acciones">
            <div class="action-icons">
              <a href="{{ route('dashboard.contentpages.edit', $p->id) }}" class="btn-icon" title="Editar">
                <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
              </a>
              <a href="#" class="btn-icon" onclick="deletePage({{ $p->id }})" title="Eliminar">
                <img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar">
              </a>
            </div>
            <form id="delete-form-{{ $p->id }}" action="{{ route('dashboard.contentpages.destroy', $p->id) }}" method="POST" style="display:none;">
              @csrf
              @method('DELETE')
            </form>
          </div>
        </div>
      @empty
        <div class="data-table-row">
          <div class="data-table-cell" style="grid-column:1 / -1; text-align:center;">
            No hay páginas registradas.
          </div>
        </div>
      @endforelse
    </div>
  </div>
</div>

<a href="#" class="btn-floating" onclick="openModalContentPage()">+</a>
@endsection

@include('components.administration.contentpages.modal-create-contentpages')
@include('components.administration.contentpages.modal-update-contentpages')

@push('scripts')
<script>
function deletePage(pageId) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
             || document.querySelector('input[name="_token"]')?.value;

  if (!confirm('¿Seguro que deseas eliminar esta página?')) return;

  fetch(`/dashboard/contentpages/${pageId}`, {
    method: "DELETE",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      "X-CSRF-TOKEN": csrf
    }
  })
  .then(r => r.json().then(b => ({ status: r.status, body: b })))
  .then(({ status, body }) => {
    if (status === 200) {
      alert(body.message || "Página eliminada con éxito.");
      location.reload();
    } else {
      console.error("Error al eliminar:", body);
      alert(body.message || "No se pudo eliminar la página.");
    }
  })
  .catch(err => {
    console.error("Error inesperado:", err);
    alert("Hubo un problema al eliminar la página.");
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const searchInput   = document.getElementById('search-input');
  const filterSelect  = document.getElementById('filter-category');
  const stateSelect   = document.getElementById('filter-state');
  const rows = Array.from(document.querySelectorAll('.data-table-row'));

  function normalizeText(text) {
    return (text || '')
      .toString()
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "");
  }

  function applyFilters() {
    const term   = normalizeText(searchInput.value.trim());
    const field  = filterSelect.value;
    const stateF = stateSelect.value;

    rows.forEach(row => {
      const title  = normalizeText(row.dataset.title);
      const slug   = normalizeText(row.dataset.slug);
      const module = normalizeText(row.dataset.module);
      const state  = row.dataset.state;

      let matchSearch = true;
      if (term !== '') {
        if (field === 'title')       matchSearch = title.includes(term);
        else if (field === 'slug')   matchSearch = slug.includes(term);
        else if (field === 'module') matchSearch = module.includes(term);
        else                         matchSearch = (title.includes(term) || slug.includes(term) || module.includes(term));
      }

      let matchState = true;
      if (stateF !== '') matchState = (state === stateF);

      row.style.display = (matchSearch && matchState) ? "" : "none";
    });
  }

  searchInput.addEventListener("keyup", applyFilters);
  document.querySelector(".search-btn").addEventListener("click", applyFilters);
  filterSelect.addEventListener("change", applyFilters);
  stateSelect.addEventListener("change", applyFilters);
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
  color: var(--govco-white-color) !important;
  font-family: var(--govco-font-primary);
  font-size: 20px;
  font-weight: bold;
}
</style>