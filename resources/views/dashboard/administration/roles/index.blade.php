@extends('dashboard.dashboard')
@section('content')
<div class="container-users">
    <div class="navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/roles-white.svg') }}" class="submenu-icon-area">
            <h2 class="navbar-title">Roles</h2>
        </div>

        <div class="navbar-filters">
            <select id="filter-category" class="filter-select">
                <option value="">Filtrar por...</option>
                <option value="name">Nombre</option>
            </select>

            <input type="text" id="search-input" class="search-box" placeholder="Buscar rol...">
            
            <button class="search-btn" onclick="searchAreas()">🔍</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="content-users">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $rol)
                    <tr>
                        <td>{{ $rol->id }}</td>
                        <td>{{ $rol->name }}</td>
                        <td class="action-icons">
                            <a href="#" class="btn-icon" onclick="openEditModal({{ $rol->id }})">
                                <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
                            </a>
                            <a href="#" class="btn-icon" onclick="deleteArea({{ $rol->id }})">
                                <img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar">
                            </a>
                            <form id="delete-form-{{ $rol->id }}" action="{{ route('dashboard.roles.destroy', $rol->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-container">
            {{ $roles->links('vendor.pagination.default') }}
        </div>
     </div>
</div>

<!-- Modal -->
<div id="modalCreateRol" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h2>Agregar Nuevo Rol</h2>
        <form id="createAreaForm">
            @csrf
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" required>
            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>

<!-- Modal de Edición -->
<div id="modalEditRol" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeEditModal()">&times;</span>
        <h2>Editar Rol</h2>
        
        <form id="editAreaForm" class="modal-form">
            @csrf
            @method('PUT')
            
            <input type="hidden" id="edit_id" name="id"> 
            <div class="modal-field">
                <label for="edit_name">Nombre:</label>
                <input type="text" id="edit_name" name="name" required>
            </div>
            <button type="submit" class="btn-submit">Guardar Cambios</button>
        </form>
    </div>
</div>

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
            let matchSearch = false;

            if (filterValue === "") {
                matchSearch = areaName.includes(normalizedSearch);
            } else if (filterValue === "name") {
                matchSearch = areaName.includes(normalizedSearch);
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

function openModal() {
    document.getElementById('modalCreateRol').style.display = 'flex';
}

/* Modal */
function closeModal() {
    document.getElementById('modalCreateRol').style.display = 'none';
}

document.getElementById("createAreaForm").addEventListener("submit", function (event) {
    event.preventDefault();

    let formData = new FormData(this);

    fetch("{{ route('dashboard.roles.store') }}", {
        method: "POST",
        body: formData,
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Error en la respuesta del servidor");
        }
        return response.json();
    })
    .then(data => {
        if (data.message) {
            alert(data.message);
            closeModal();
            location.reload();
        } else {
            alert("Error al crear el rol");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Hubo un problema al crear el rol.");
    });
});

/* Update */
function openEditModal(areaId) {
    fetch(`/dashboard/roles/${areaId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('modalEditRol').style.display = 'flex';
        })
        .catch(error => console.error("Error al cargar datos:", error));
}

function closeEditModal() {
    document.getElementById('modalEditRol').style.display = 'none';
}

document.getElementById("editAreaForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Evita la recarga de la página

    let formData = new FormData(this);
    let areaId = document.getElementById('edit_id').value;

    fetch(`/dashboard/roles/${areaId}`, {
        method: "POST",
        body: formData,
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Error en la respuesta del servidor");
        }
        return response.json();
    })
    .then(data => {
        alert("Rol actualizado con éxito");
        closeEditModal();
        location.reload();
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Hubo un problema al actualizar el rol.");
    });
});

/* Delete */
function deleteArea(areaId) {
    if (!confirm('¿Seguro que quieres eliminar este rol?')) return;

    fetch(`/dashboard/roles/${areaId}`, {
        method: "DELETE",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Error al eliminar el rol");
        }
        return response.json();
    })
    .then(data => {
        alert("Rol eliminado con éxito");
        location.reload();
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Hubo un problema al eliminar el rol.");
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
.container-users {
    min-width: 100%;
    min-height: 100%;
}

.content-users {
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