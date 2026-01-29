@extends('dashboard.dashboard')
@push('scripts')
<script>
function deleteContractor(contractorId) {
    if (!confirm('¿Seguro que deseas eliminar este contratista?')) return;

    fetch(`/dashboard/contractors/${contractorId}`, {
        method: "DELETE",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        if (status === 200) {
            alert(body.message || "Contratista eliminado con éxito.");
            location.reload();
        } else {
            console.error("Error al eliminar:", body);
            alert(body.message || "No se pudo eliminar el contratista.");
        }
    })
    .catch(error => {
        console.error("Error inesperado:", error);
        alert("Hubo un problema al eliminar el contratista.");
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

function toggleVerMas(el) {
    el.classList.toggle('expanded');
    const btn = el.querySelector('.ver-mas');
    btn.textContent = el.classList.contains('expanded') ? '▴' : '▾';
    }
</script>
@endpush

@section('content')
<div class="container-modules">
    <div class="navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/contractor-white.svg') }}" class="submenu-icon-area">
            <h2 class="navbar-title">Contratistas</h2>
        </div>

        <form method="GET" class="navbar-filters">
            <select id="filter-category" name="category" class="filter-select">
                <option value="">Filtrar por...</option>
                <option value="contract_number" {{ request('category') == 'contract_number' ? 'selected' : '' }}># Contrato</option>
                <option value="contractor" {{ request('category') == 'contractor' ? 'selected' : '' }}>Contratista</option>
                <option value="object" {{ request('category') == 'object' ? 'selected' : '' }}>Objeto</option>
                <option value="supervision" {{ request('category') == 'supervision' ? 'selected' : '' }}>Supervisor</option>
                <option value="dependency" {{ request('category') == 'dependency' ? 'selected' : '' }}>Dependencia</option>
            </select>

            <input type="text" id="search-input" name="search" class="search-box"
                placeholder="Buscar contratista..." value="{{ request('search') }}">
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
                    <th>Año</th>
                    <th>Mes</th>
                    <th># Contrato</th>
                    <th>Fec. Cont</th>
                    <th>Cód. Secop</th>
                    <th>Clase cont.</th>
                    <th>Contratista</th>
                    <th>Firma cont.</th>
                    <th>Modalidad</th>
                    <th>Objeto</th>
                    <th>Plazo</th>
                    <th>Fec. Ini</th>
                    <th>Fec. Fin</th>
                    <th>Valor</th>
                    <th>Dependencia</th>
                    <th>Supervisión</th>
                    <th>Clase de gasto</th>
                    <th>Secop</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contractors as $con)
                    <tr>
                        <td>{{ $con->id }}</td>
                        <td>{{ $con->year_contract }}</td>
                        <td>{{ $con->month_contract }}</td>
                        <td>{{ $con->contract_number }}</td>
                        <td>{{ $con->date_contract }}</td>
                        <td>{{ $con->code_secop }}</td>
                        <td>{{ $con->class_contract }}</td>
                        <td>{{ $con->contractor }}</td>
                        <td>{{ $con->firm_contractor }}</td>
                        <td>{{ $con->process_modality }}</td>
                        <td>
                            <div class="objeto-texto" onclick="toggleVerMas(this)">
                                <div class="contenido">{{ $con->object }}</div>
                                <span class="ver-mas">▾</span>
                            </div>
                        </td>
                        <td>{{ $con->contract_term }}</td>
                        <td>{{ $con->start_date }}</td>
                        <td>{{ $con->cutoff_date }}</td>
                        <td>${{ number_format($con->total_value, 2, ',', '.') }}</td>
                        <td>{{ $con->dependency }}</td>
                        <td>{{ $con->supervision }}</td>
                        <td>{{ $con->expense_class }}</td>
                        <td>
                            <a href="{{ $con->link_secop }}" target="_blank" rel="noopener noreferrer">
                                Ver enlace
                            </a>
                        </td>
                        <td>
                            <div class="action-icons">
                                <a href="#" class="btn-icon" onclick="openModalEditContractor({{ $con->id }})">
                                    <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
                                </a>
                                <a href="#" class="btn-icon" onclick="deleteContractor({{ $con->id }})">
                                    <img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar">
                                </a>
                            </div>

                            <form id="delete-form-{{ $con->id }}" action="{{ route('dashboard.contractors.destroy', $con->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-container">
            {{ $contractors->links('vendor.pagination.default') }}
        </div>
     </div>
</div>
<a href="#" title="Agregar nuevo registro" class="btn-floating" onclick="openModalCreateContractor()">+</a>
<a href="#" title="Cargar archivo" class="btn-floating-file" onclick="openModalUploadContractor()">
  <img src="{{ asset('icon/upload-white.svg') }}" alt="Cargar archivo">
</a>
<a href="#" title="Exportar archivo" class="btn-floating-download" onclick="openModalExportContractor()">
  <img src="{{ asset('icon/download-white.svg') }}" alt="Exportar archivo">
</a>
@endsection
@include('components.modals.publication.modal-create-contractor')
@include('components.modals.publication.modal-update-contractor')
@include('components.modals.publication.modal-upload-contractors')
@include('components.modals.publication.modal-export-contractors')
<!-- Estilos -->
<style>
.navbar {
    position: sticky;
    top: 0;
    width: 100%;
    background-color: var(--govco-secondary-color);
    padding: 15px 20px;
    display: flex;
    flex-wrap: wrap; /* si quieres que el buscador baje en pantallas pequeñas */
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
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


.container-modules {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100vh;
}

.content-modules {
    width: 100%;
    min-height: 92%;
    overflow-x: auto;
    padding: 10px
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
    width: max-content;
    min-width: 100%;
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

.objeto-texto {
    position: relative;
    max-height: 4.5em;
    width: 9rem;
    overflow: hidden;
    padding-right: 40px;
    line-height: 1.5em;
    display: flex;
    flex-direction: column;
    justify-content: start;
    cursor: pointer;
}

.objeto-texto.expanded {
    max-height: none;
}

.objeto-texto .contenido {
    white-space: pre-wrap;
    word-break: break-word;
}

.ver-mas {
    position: absolute;
    bottom: 2px;
    right: 4px;
    font-size: 12px;
    color: #007bff;
    background-color: white;
    padding: 0 4px;
    pointer-events: none;
}
.objeto-texto.expanded .ver-mas {
    content: "▴";
}
</style>