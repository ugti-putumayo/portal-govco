@extends('dashboard.dashboard')
@section('content')
<div class="container-modules">
    <div class="navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/content-white.svg') }}" class="submenu-icon-area">
            <h2 class="navbar-title">Contenido Institucional</h2>
        </div>

        <form method="GET" class="navbar-filters">
            <select id="filter-category" name="category" class="filter-select">
                <option value="">Filtrar por...</option>
                <option value="section" {{ request('category') == 'section' ? 'selected' : '' }}>Sección</option>
                <option value="content" {{ request('category') == 'content' ? 'selected' : '' }}>Contenido</option>
            </select>

            <input type="text" id="search-input" name="search" class="search-box"
                placeholder="Buscar contenido..." value="{{ request('search') }}">
            <button type="submit" class="search-btn">🔍</button>
        </form>
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
                    <th>Sección</th>
                    <th>Estado</th>
                    <th>Orden</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ic as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->section }}</td>
                        <td>
                            <span class="{{ $item->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $item->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>{{ $item->order_index ?? 0 }}</td>
                        <td>
                            <div class="action-icons">
                                <a href="#" class="btn-icon" onclick="openModalEditInstitutionalContent({{ $item->id }})">
                                    <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
                                </a>
                                <a href="#" class="btn-icon" onclick="deleteInstitutional({{ $item->id }})">
                                    <img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar">
                                </a>
                            </div>

                            <form id="delete-form-{{ $item->id }}" action="{{ route('dashboard.institutionalcontent.destroy', $item->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $ic->links() }}
        </div>
    </div>
</div>
<a href="#" class="btn-floating" onclick="openModalCreateInstitutionalContent()">+</a>
@include('components.administration.institutional-content.modal-create-institutional-content')
@include('components.administration.institutional-content.modal-update-institutional-content')
@endsection
@push('scripts')
<script>
function deleteInstitutional(id) {
    if (!confirm('¿Seguro que deseas eliminar este contenido institucional?')) return;

    fetch(`/dashboard/institutionalcontent/${id}`, {
        method: "DELETE",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        if (status === 200) {
            alert(body.message || "Contenido eliminado correctamente.");
            location.reload();
        } else {
            console.error("Error:", body);
            alert(body.message || "No se pudo eliminar el contenido.");
        }
    })
    .catch(error => {
        console.error("Error inesperado:", error);
        alert("Hubo un problema al eliminar el contenido.");
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('search-input');
    const filterSelect = document.getElementById('filter-category');
    const searchBtn = document.querySelector('.search-btn');

    function searchContractor() {
        const category = filterSelect.value;
        const search = searchInput.value;
        const url = new URL(window.location.href.split('?')[0]);
        if (search) url.searchParams.set('search', search);
        if (category) url.searchParams.set('category', category);
        window.location.href = url.toString();
    }

    searchInput?.addEventListener("keyup", function () {
        if (filterSelect.value === "") {
            searchContractor();
        }
    });

    searchBtn?.addEventListener("click", function () {
        searchContractor();
    });
});
</script>
@endpush
@push('styles')
<style>
.navbar {
    position: sticky; /* o fixed si prefieres */
    top: 0;
    left: 0;
    width: 100%;
    background-color: var(--govco-secondary-color);
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
    box-sizing: border-box;
}

.navbar-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.submenu-icon-area {
    width: 30px;
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
    gap: 10px;
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
@endpush