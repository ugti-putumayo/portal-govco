@extends('dashboard.dashboard') 
@push('scripts')
<script>
function deletePlantOfficial(pofficialId) {
    if (!confirm('¿Seguro que deseas eliminar este funcionario?')) return;

    fetch(`/dashboard/plantofficials/${pofficialId}`, {
        method: "DELETE",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        if (status === 200) {
            alert(body.message || "Funcionario eliminado con éxito.");
            location.reload();
        } else {
            console.error("Error al eliminar:", body);
            alert(body.message || "No se pudo eliminar el funcionario.");
        }
    })
    .catch(error => {
        console.error("Error inesperado:", error);
        alert("Hubo un problema al eliminar el funcionario.");
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('search-input');
    const filterSelect = document.getElementById('filter-category');
    const searchBtn = document.querySelector('.search-btn');

    function searchPlantOfficial() {
        const category = filterSelect.value;
        const search = searchInput.value;
        const url = new URL(window.location.href.split('?')[0]);
        if (search) url.searchParams.set('search', search);
        if (category) url.searchParams.set('category', category);
        window.location.href = url.toString();
    }

    searchInput?.addEventListener("keyup", function () {
        if (filterSelect.value === "") {
            searchPlantOfficial();
        }
    });

    searchBtn?.addEventListener("click", function () {
        searchPlantOfficial();
    });
});
</script>
@endpush

@section('content')
<div class="container-modules">
    <div class="navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/plantofficial-white.svg') }}" class="submenu-icon-area">
            <h2 class="navbar-title">Nomina</h2>
        </div>

        <form method="GET" class="navbar-filters">
            <select id="filter-category" name="category" class="filter-select">
                <option value="">Filtrar por...</option>
                <option value="fullname" {{ request('category') == 'fullname' ? 'selected' : '' }}>Nombre</option>
                <option value="document_number" {{ request('category') == 'document_number' ? 'selected' : '' }}># Documento</option>
                <option value="dependency" {{ request('category') == 'dependency' ? 'selected' : '' }}>Dependencia</option>
            </select>

            <input type="text" id="search-input" name="search" class="search-box"
                placeholder="Buscar funcionario..." value="{{ request('search') }}">
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
                    <th>Tipo doc.</th>
                    <th># Documento</th>
                    <th>Nombres y apellidos</th>
                    <th>Cargo</th>
                    <th>Dependencia</th>
                    <th>Subdepencencia</th>
                    <th>Código</th>
                    <th>Grado</th>
                    <th>Nivel</th>
                    <th>Denominación</th>
                    <th>Vr. Salario</th>
                    <th>Gastos de rep.</th>
                    <th>Fec. Ini</th>
                    <th>Fec. Vac</th>
                    <th>Fec. Bon.</th>
                    <th>Correo</th>
                    <th>Fec. Nac</th>
                    <th>EPS</th>
                    <th>Celular</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($plantofficial as $poff)
                    <tr>
                        <td>{{ $poff->id }}</td>
                        <td>{{ $poff->document_type }}</td>
                        <td>{{ $poff->document_number }}</td>
                        <td>{{ $poff->fullname }}</td>
                        <td>{{ $poff->charge }}</td>
                        <td>{{ $poff->dependency }}</td>
                        <td>{{ $poff->subdependencie }}</td>
                        <td>{{ $poff->code }}</td>
                        <td>{{ $poff->grade }}</td>
                        <td>{{ $poff->level }}</td>
                        <td>{{ $poff->denomination }}</td>
                        <td>${{ number_format($poff->total_value, 2, ',', '.') }}</td>
                        <td>${{ number_format($poff->representation_expenses, 2, ',', '.') }}</td>
                        <td>{{ $poff->init_date }}</td>
                        <td>{{ $poff->vacation_date }}</td>
                        <td>{{ $poff->bonus_date }}</td>
                        <td>{{ $poff->email }}</td>
                        <td>{{ $poff->birthdate }}</td>
                        <td>{{ $poff->eps }}</td>
                        <td>{{ $poff->cellphone }}</td>    
                        <td>{{ $poff->is_active ? 'Activo' : 'Inactivo' }}</td>                    
                        <td>
                            <div class="action-icons">
                                <a href="#" class="btn-icon" onclick="openEditPlantOfficialModal({{ $poff->id }})">
                                    <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
                                </a>
                                <a href="#" class="btn-icon" onclick="deleteContractor({{ $poff->id }})">
                                    <img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar">
                                </a>
                            </div>

                            <form id="delete-form-{{ $poff->id }}" action="{{ route('dashboard.plantofficials.destroy', $poff->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-container">
            {{ $plantofficial->links('vendor.pagination.default') }}
        </div>
     </div>
</div>
<a href="#" title="Agregar nuevo registro" class="btn-floating" onclick="openModalCreatePlantOfficial()">+</a>
<a href="#" title="Exportar archivo" class="btn-floating-download-po" onclick="openModalExportPlantOfficial()">
  <img src="{{ asset('icon/download-white.svg') }}" alt="Exportar archivo">
</a>
@endsection
@include('components.modals.publication.modal-create-plant-official')
@include('components.modals.publication.modal-update-plant-official')
@include('components.modals.publication.modal-export-plant-official')
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

.btn-floating-download-po {
  position: fixed;
  bottom: 80px;
  right: 20px;
  background-color: var(--govco-accent-color);
  color: var(--govco-white-color);
  font-size: 24px;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  text-decoration: none;
  box-shadow: var(--govco-box-shadow);
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-floating-download-po:hover {
  background-color: var(--govco-secondary-color);
  color: white;
}

.btn-floating-download-po img {
  width: 24px;
  height: 24px;
  object-fit: contain;
}
</style>