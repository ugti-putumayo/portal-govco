@extends('dashboard.dashboard')
@push('scripts')
<script>
function deleteItem(itemId) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
             || document.querySelector('input[name="_token"]')?.value;

  if (!confirm('¿Seguro que deseas eliminar este item?')) return;

  fetch(`/dashboard/contentitems/${itemId}`, {
    method: "DELETE",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      "X-CSRF-TOKEN": csrf
    }
  })
  .then(r => r.json().then(b => ({ status: r.status, body: b })))
  .then(({ status, body }) => {
    if (status === 200) {
      alert(body.message || "Item eliminado con éxito.");
      location.reload();
    } else {
      console.error("Error al eliminar:", body);
      alert(body.message || "No se pudo eliminar el item.");
    }
  })
  .catch(err => {
    console.error("Error inesperado:", err);
    alert("Hubo un problema al eliminar el item.");
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const searchInput  = document.getElementById('search-input');
  const filterSelect = document.getElementById('filter-category');
  const rows = Array.from(document.querySelectorAll('.data-table-row'));

  function normalizeText(text) {
    return (text || '')
      .toString()
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "");
  }

  function applyFilters() {
    const term  = normalizeText(searchInput.value.trim());
    const field = filterSelect.value; // title|page|url|all

    rows.forEach(row => {
      const title    = normalizeText(row.dataset.title);
      const page     = normalizeText(row.dataset.page); // título y/o slug de la página
      const urlfield = normalizeText(row.dataset.url);

      let matchSearch = true;
      if (term !== '') {
        if (field === 'title')      matchSearch = title.includes(term);
        else if (field === 'page')  matchSearch = page.includes(term);
        else if (field === 'url')   matchSearch = urlfield.includes(term);
        else                        matchSearch = (title.includes(term) || page.includes(term) || urlfield.includes(term));
      }

      row.style.display = matchSearch ? "" : "none";
    });
  }

  searchInput.addEventListener("keyup", applyFilters);
  document.querySelector(".search-btn").addEventListener("click", applyFilters);
  filterSelect.addEventListener("change", applyFilters);
});
</script>
@endpush

@section('content')
<div class="container-modules with-app-navbar">
  <div class="navbar app-navbar">
    <div class="app-navbar__left">
      <img src="{{ asset('icon/users-white.svg') }}" class="app-navbar__icon" alt="Items">
      <h2 class="app-navbar__title">Content Items</h2>
    </div>

    <div class="app-navbar__right navbar-filters filters-card">
      <select id="filter-category" class="filter-select">
        <option value="all">Buscar en...</option>
        <option value="title">Título</option>
        <option value="page">Página</option>
        <option value="url">URL</option>
      </select>

      <input type="text" id="search-input" class="search-box" placeholder="Buscar item...">
      <button class="search-btn" type="button">🔍</button>

      <a href="{{ route('dashboard.contentitems.create') }}" class="btn btn-primary">+ Nuevo</a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert-success">
      {{ session('success') }}
    </div>
  @endif

  @php
    // La colección puede venir como $items o $contentitems (fallback)
    $items = $items ?? ($contentitems ?? collect());
  @endphp

  <div class="content-modules">
    <div class="data-table">
      <div class="data-table-header">
        <div class="data-table-cell">ID</div>
        <div class="data-table-cell">Título</div>
        <div class="data-table-cell">Página</div>
        <div class="data-table-cell">Orden</div>
        <div class="data-table-cell">URL</div>
        <div class="data-table-cell">Documento</div>
        <div class="data-table-cell">Imagen</div>
        <div class="data-table-cell">Acciones</div>
      </div>

      @forelse ($items as $i)
        @php
          $pageTitle = $i->page->title ?? '';
          $pageSlug  = $i->page->slug  ?? '';
          $pageMix   = trim($pageTitle.' '.$pageSlug);

          // URL externa o interna
          $urlHref = null;
          if (!empty($i->url)) {
            $urlHref = $i->url;
          }

          // Documento: si no es http/https ni empieza por '/', construimos con asset('app/public/...').
          $docHref = null;
          if (!empty($i->document)) {
            $docHref = \Illuminate\Support\Str::startsWith($i->document, ['http://','https://','/'])
                       ? $i->document
                       : asset($i->document);
          }

          // Imagen
          $imgSrc = null;
          if (!empty($i->image)) {
            $imgSrc = \Illuminate\Support\Str::startsWith($i->image, ['http://','https://','/'])
                     ? $i->image
                     : asset($i->image);
          }
        @endphp

        <div class="data-table-row"
             data-title="{{ $i->title }}"
             data-page="{{ $pageMix }}"
             data-url="{{ $i->url ?? '' }}">
          <div class="data-table-cell" data-label="ID">{{ $i->id }}</div>
          <div class="data-table-cell" data-label="Título">{{ $i->title }}</div>
          <div class="data-table-cell" data-label="Página">
            {{ $pageTitle ?: '—' }}
            @if($pageSlug) <small style="opacity:.7;">({{ $pageSlug }})</small> @endif
          </div>
          <div class="data-table-cell" data-label="Orden">{{ $i->ordering }}</div>
          <div class="data-table-cell" data-label="URL">
            @if($urlHref)
              <a href="{{ $urlHref }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($urlHref, 40) }}</a>
            @else
              <span>—</span>
            @endif
          </div>
          <div class="data-table-cell" data-label="Documento">
            @if($docHref)
              <a href="{{ $docHref }}" target="_blank" rel="noopener">Descargar</a>
            @else
              <span>—</span>
            @endif
          </div>
          <div class="data-table-cell" data-label="Imagen">
            @if($imgSrc)
              <img src="{{ $imgSrc }}" alt="imagen" class="table-img">
            @else
              <span>Sin imagen</span>
            @endif
          </div>

          <div class="data-table-cell" data-label="Acciones">
            <div class="action-icons">
              <a href="{{ route('dashboard.contentitems.edit', $i->id) }}" class="btn-icon" title="Editar">
                <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
              </a>
              <a href="#" class="btn-icon" onclick="deleteItem({{ $i->id }})" title="Eliminar">
                <img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar">
              </a>
            </div>

            <form id="delete-form-{{ $i->id }}" action="{{ route('dashboard.contentitems.destroy', $i->id) }}" method="POST" style="display:none;">
              @csrf
              @method('DELETE')
            </form>
          </div>
        </div>
      @empty
        <div class="data-table-row">
          <div class="data-table-cell" style="grid-column:1 / -1; text-align:center;">
            No hay items registrados.
          </div>
        </div>
      @endforelse
    </div>
  </div>
</div>

<a href="{{ route('dashboard.contentitems.create') }}" class="btn-floating">+</a>
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