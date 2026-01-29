@extends('dashboard.dashboard') 
@section('content')
<div class="container-areas">
    <div class="navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/areas-white.svg') }}" class="submenu-icon-area">
            <h2 class="navbar-title">Áreas</h2>
        </div>

        <div class="navbar-filters">
            <select id="filter-category" class="filter-select">
                <option value="">Filtrar por...</option>
                <option value="name">Nombre</option>
                <option value="shortname">Abreviatura</option>
            </select>

            <input type="text" id="search-input" class="search-box" placeholder="Buscar área...">
            <button class="search-btn" onclick="searchAreas()">🔍</button>
        </div>
    </div>
    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="content-areas">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Celular</th>
                    <th>Ext</th>
                    <th>Email</th>
                    <th>Nombre Corto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dependencies as $dep)
                    <tr>
                        <td>{{ $dep->id }}</td>
                        <td>{{ $dep->name }}</td>
                        <td>{{ $dep->cellphone }}</td>
                        <td>{{ $dep->ext }}</td>
                        <td>{{ $dep->email }}</td>
                        <td>{{ $dep->shortname }}</td>
                        <td class="action-icons">
                            <a href="#" class="btn-icon" onclick="openEditModal({{ $dep->id }})">
                                <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
                            </a>
                            <a href="#" class="btn-icon" onclick="deleteArea({{ $dep->id }})">
                                <img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar">
                            </a>
                            <form id="delete-form-{{ $dep->id }}" action="{{ route('dashboard.dependencies.destroy', $dep->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-container">
            {{ $dependencies->links('vendor.pagination.default') }}
        </div>
     </div>
</div>
@include('components.administration.dependency.modal-create-dependency')
@include('components.administration.dependency.modal-update-dependency')

<!-- Botón flotante para agregar nueva área -->
<a href="#" class="btn-floating" onclick="openModal()">+</a>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let searchInput = document.getElementById('search-input');
    let filterSelect = document.getElementById('filter-category');

    function normalizeText(text) {
        return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    function searchAreas() {
        let searchValue = searchInput.value.toLowerCase().trim();
        let filterValue = filterSelect.value;
        let rows = document.querySelectorAll('.styled-table tbody tr');

        let normalizedSearch = normalizeText(searchValue);

        rows.forEach(row => {
            let areaName = normalizeText(row.cells[1].innerText.toLowerCase());
            let shortName = normalizeText(row.cells[2].innerText.toLowerCase());
            let matchSearch = false;
            if (filterValue === "") {
                matchSearch = areaName.includes(normalizedSearch) || shortName.includes(normalizedSearch);
            } else if (filterValue === "name") {
                matchSearch = areaName.includes(normalizedSearch);
            } else if (filterValue === "shortname") {
                matchSearch = shortName.includes(normalizedSearch);
            }
            row.style.display = matchSearch ? "" : "none";
        });
    }
    searchInput.addEventListener("keyup", function () {
        if (filterSelect.value === "") {
            searchAreas();
        }
    });
    document.querySelector(".search-btn").addEventListener("click", function () {
        searchAreas();
    });
});

/* Delete */
function deleteArea(areaId) {
    if (!confirm('¿Seguro que quieres eliminar este área?')) return;

    fetch(`/dashboard/dependencies/${areaId}`, {
        method: "DELETE",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Error al eliminar el área");
        }
        return response.json();
    })
    .then(data => {
        alert("Área eliminada con éxito");
        location.reload();
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Hubo un problema al eliminar el área.");
    });
}
</script>
@endsection

<style>
/* Ajuste dinámico de la navbar */
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
.container-areas {
    min-width: 100%;
    min-height: 100%;
}

.content-areas {
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

/* Botón flotante */
.btn-floating {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: var(--govco-accent-color);
    color: var(--govco-white-color);
    font-size: 24px;
    width: 50px;
    height: 50px;
    text-align: center;
    line-height: 50px;
    border-radius: 50%;
    text-decoration: none;
    box-shadow: var(--govco-box-shadow);
}

.btn-floating:hover {
    background-color: var(--govco-secondary-color);
    color: white
}
</style>