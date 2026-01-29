@extends('dashboard') 
@section('content')
<div class="container-modules">
    <div class="navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/publication-white.svg') }}" class="submenu-icon-area">
            <h2 class="navbar-title">Informes</h2>
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
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $rep)
                    <tr>
                        <td>{{ $rep->id }}</td>
                        <td>{{ $rep->title }}</td>
                        <td>{{ $rep->type->name ?? 'Sin tipo' }}</td>
                        <td>{{ $rep->date }}</td>
                        <td>
                            <span class="{{ $pub->state ? 'text-success' : 'text-danger' }}">
                                {{ $rep->state ? 'Publicado' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="action-icons">
                            <a href="#" class="btn-icon" onclick="openModalEditPublication({{ $rep->id }})">
                                <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
                            </a>
                            <a href="#" class="btn-icon" onclick="deletePublication({{ $rep->id }})">
                                <img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar">
                            </a>
                            <form id="delete-form-{{ $pub->id }}" action="{{ route('dashboard.publication.destroy', $pub->id) }}" method="POST" style="display: none;">
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
@include('components.modals.publication.modal-create-publication')
@include('components.modals.publication.modal-update-publication')
<!-- Botón flotante para agregar nueva área -->
<a href="#" class="btn-floating" onclick="openModalPublication()">+</a>
<script>
function openModalPublication() {
    document.getElementById('modalCreatePublication').style.display = 'flex';
}

function closeModalPublication() {
    document.getElementById('modalCreatePublication').style.display = 'none';
}

function deletePublication(id) {
    if (confirm("¿Seguro que deseas eliminar esta publicación?")) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}

/* Search */
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

/* Update */
function openModalEditPublication(id) {
    fetch(`/dashboard/publication/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("edit_id").value = data.id;
            document.getElementById("edit_title").value = data.title || '';
            document.getElementById("edit_description").value = data.description || '';
            document.getElementById("edit_type_id").value = data.type_id || '';
            document.getElementById("edit_date").value = data.date || '';
            document.getElementById("edit_date_start").value = data.date_start || '';
            document.getElementById("edit_date_end").value = data.date_end || '';

            const preview = document.getElementById("preview_edit_image");
            if (data.image) {
                preview.src = `/storage/${data.image}`;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }

            document.getElementById("modalEditPublication").style.display = "flex";
        })
        .catch(err => {
            console.error("Error al cargar publicación:", err);
            alert("No se pudo cargar la publicación.");
        });
}

function closeModalEditPublication() {
    document.getElementById("modalEditPublication").style.display = "none";
}

/* Delete */
function deletePublication(publicationId) {
    if (!confirm('¿Seguro que deseas eliminar esta publicación?')) return;

    fetch(`/dashboard/publication/${publicationId}`, {
        method: "DELETE",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        if (status === 200) {
            alert(body.message || "Publicación eliminada con éxito.");
            location.reload();
        } else {
            console.error("Error al eliminar:", body);
            alert(body.message || "No se pudo eliminar la publicación.");
        }
    })
    .catch(error => {
        console.error("Error inesperado:", error);
        alert("Hubo un problema al eliminar la publicación.");
    });
}
</script>

@endsection

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