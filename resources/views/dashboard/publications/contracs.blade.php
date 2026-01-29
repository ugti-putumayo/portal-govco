@extends('dashboard.dashboard') 
@section('content')
<div class="container-areas">
    <div class="navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/contracs-white.svg') }}" class="submenu-icon-area">
            <h2 class="navbar-title">Contratos</h2>
        </div>

        <div class="navbar-filters">
            <select id="filter-category" class="filter-select">
                <option value="">Filtrar por...</option>
                <option value="contract_number">Número contrato</option>
                <option value="dependency">Dependencia</option>
                <option value="contractor">Contratista</option>
                <option value="nit">Nit</option>
                <option value="objective">Objeto</option>
            </select>

            <input type="text" id="search-input" class="search-box" placeholder="Buscar contrato...">
            <button class="search-btn" onclick="searchContrac()">🔍</button>
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
                    <th>Fecha</th>
                    <th>#Contrato</th>
                    <th>Área</th>
                    <th>Contratista</th>
                    <th>Nit</th>
                    <th>Objeto</th>
                    <th>Valor</th>
                    <th>Duración</th>
                    <th>Fec.Ini</th>
                    <th>Fec.Ter</th>
                    <th>Fec.Lim</th>
                    <th>%</th>
                    <th>Secop</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contracs as $cons)
                    <tr>
                        <td>{{ $cons->id }}</td>
                        <td>{{ $cons->subscription_date }}</td>
                        <td>{{ $cons->contract_number }}</td>
                        <td>{{ $cons->dependency }}</td>
                        <td>{{ $cons->contractor }}</td>
                        <td>{{ $cons->nit }}</td>
                        <td>{{ $cons->objective }}</td>
                        <td>${{ number_format($cons->total_value, 2, ',', '.') }}</td>
                        <td>{{ $cons->duration }}</td>
                        <td>{{ $cons->start_date }}</td>
                        <td>{{ $cons->end_date }}</td>
                        <td>{{ $cons->cutoff_date }}</td>
                        <td>{{ $cons->contract_progress_percentage }}%</td>
                        <td>
                            <a href="{{ $cons->link_secop }}" target="_blank" rel="noopener noreferrer">
                                Ver enlace
                            </a>
                        </td>
                        <td>
                            <div class="action-icons">
                                <a href="#" class="btn-icon" onclick="openEditContracModal({{ $cons->id }})">
                                    <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
                                </a>
                                <a href="#" class="btn-icon" onclick="deleteContrac({{ $cons->id }})">
                                    <img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar">
                                </a>
                                <form id="delete-form-{{ $cons->id }}" action="{{ route('dashboard.contracs.destroy', $cons->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-container">
            {{ $contracs->links('vendor.pagination.default') }}
        </div>
     </div>
</div>
<!-- Botón flotante para agregar nueva área -->
<a href="#" class="btn-floating" onclick="openModalCreateContrac()">+</a>

@include('components.modals.publication.modal-create-contrac')
@include('components.modals.publication.modal-update-contrac')

<script>
document.addEventListener("DOMContentLoaded", function () {
    let searchInput = document.getElementById('search-input');
    let filterSelect = document.getElementById('filter-category');

    function normalizeText(text) {
        return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    function searchContrac() {
        let searchValue = searchInput.value.toLowerCase().trim();
        let filterValue = filterSelect.value;
        let rows = document.querySelectorAll('.styled-table tbody tr');

        let normalizedSearch = normalizeText(searchValue);

        rows.forEach(row => {
            let numberContrac = normalizeText(row.cells[2].innerText.toLowerCase());
            let dependency = normalizeText(row.cells[3].innerText.toLowerCase());
            let contractor = normalizeText(row.cells[4].innerText.toLowerCase());
            let nit = normalizeText(row.cells[5].innerText.toLowerCase());
            let objective = normalizeText(row.cells[6].innerText.toLowerCase());

            let matchSearch = false;
            if (filterValue === "") {
                matchSearch =
                    numberContrac.includes(normalizedSearch) ||
                    dependency.includes(normalizedSearch) ||
                    contractor.includes(normalizedSearch) ||
                    nit.includes(normalizedSearch) ||
                    objective.includes(normalizedSearch);
            } else if (filterValue === "contract_number") {
                matchSearch = numberContrac.includes(normalizedSearch);
            } else if (filterValue === "dependency") {
                matchSearch = dependency.includes(normalizedSearch);
            } else if (filterValue === "contractor") {
                matchSearch = contractor.includes(normalizedSearch);
            } else if (filterValue === "nit") {
                matchSearch = nit.includes(normalizedSearch);
            } else if (filterValue === "objective") {
                matchSearch = objective.includes(normalizedSearch);
            }

            row.style.display = matchSearch ? "" : "none";
        });
    }

    searchInput.addEventListener("keyup", function () {
        if (filterSelect.value === "") {
            searchContrac();
        }
    });
    document.querySelector(".search-btn").addEventListener("click", function () {
        searchContrac();
    });
});

/* Delete */
function deleteContrac(conId) {
    if (!confirm('¿Seguro que quieres eliminar este contrato?')) return;

    fetch(`/dashboard/contracs/${conId}`, {
        method: "DELETE",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Error al eliminar el contrato");
        }
        return response.json();
    })
    .then(data => {
        alert("Contrato eliminado con éxito");
        location.reload();
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Hubo un problema al eliminar el contrato.");
    });
}
</script>
@endsection

<style>
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    min-width: 100%;
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

.submenu-icon-area {
    width: 30px;
    height: 30px;
    color: white;
}


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