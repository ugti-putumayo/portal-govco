@extends('dashboard.dashboard')

@section('content')
<div class="container-modules with-app-navbar">
    <div class="navbar app-navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/users-white.svg') }}" class="app-navbar__icon" alt="Usuarios">
            <h2 class="app-navbar__title">Gestión de Usuarios</h2>
        </div>

        <div class="app-navbar__right navbar-filters filters-card">
            <select id="filter-category" class="filter-select">
                <option value="">Filtrar por...</option>
                <option value="name">Nombre</option>
                <option value="email">Email</option>
            </select>

            <input type="text"
                   id="search-input"
                   class="search-box"
                   placeholder="Buscar usuario...">

            <button class="search-btn" type="button" onclick="searchUsers()" title="Buscar">🔍</button>
            <button class="search-btn" type="button" onclick="clearUserFilters()" title="Limpiar filtros" style="line-height:28px;">
                ❌
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="content-modules">
        <div class="data-table" style="--cols: 70px 1.6fr 1.6fr 140px;">
            <div class="data-table-header">
                <div class="data-table-cell">ID</div>
                <div class="data-table-cell">Nombre</div>
                <div class="data-table-cell">Email</div>
                <div class="data-table-cell">Acciones</div>
            </div>

            @forelse ($users as $user)
                <div class="data-table-row" id="user-row-{{ $user->id }}">
                    <div class="data-table-cell" data-label="ID">
                        {{ $user->id }}
                    </div>

                    <div class="data-table-cell" data-label="Nombre">
                        {{ $user->name }}
                    </div>

                    <div class="data-table-cell" data-label="Email">
                        {{ $user->email }}
                    </div>

                    <div class="data-table-cell" data-label="Acciones">
                        <div class="action-icons">
                            <a href="#"
                            class="btn-icon"
                            title="Editar usuario"
                            onclick="openEditModal({{ $user->id }})">
                                <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
                            </a>

                            <a href="#"
                            class="btn-icon"
                            title="Cambiar contraseña"
                            onclick="openChangePasswordModal({{ $user->id }})">
                                <img src="{{ asset('icon/password.svg') }}" alt="Cambiar contraseña">
                            </a>

                            <a href="#"
                            class="btn-icon delete-btn"
                            title="Eliminar usuario"
                            onclick="confirmAndDeleteUser({{ $user->id }})">
                                <img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar">
                            </a>

                            <a href="#"
                            class="btn-icon"
                            title="Accesos"
                            onclick="openAssignAccess({{ $user->id }}, {{ $user->rol_id ?? 'null' }})">
                                <img src="{{ asset('icon/permissions.svg') }}" alt="Accesos">
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="data-table-row">
                    <div class="data-table-cell" style="grid-column:1 / -1; text-align:center;">
                        No se encontraron usuarios registrados.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="pagination-container" style="margin-top: 1.5rem;">
            {{ $users->links('vendor.pagination.default') }}
        </div>
    </div>
</div>

<a href="#"
   title="Agregar usuario"
   class="btn-floating"
   onclick="openModal()">
    <span>+</span>
</a>

@include('components.dashboard.administration.users.users-create')
@include('components.dashboard.administration.users.users-update')
@include('components.dashboard.administration.users.users-password')
@include('components.modals.Users.assign-module', ['roles' => $roles, 'modules' => $modules])
@include('components.modals.Users.assign-permissions')
@endsection
@push('scripts')
<script>
function normalizeText(text) {
    return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

function searchUsers() {
    const searchInput = document.getElementById('search-input');
    const filterSelect = document.getElementById('filter-category');
    const rows = document.querySelectorAll('.data-table-row');

    const searchValue = normalizeText((searchInput.value || '').toLowerCase().trim());
    const filterValue = filterSelect.value;

    rows.forEach(row => {
        // saltar la fila de header si por algún motivo cae en el selector
        if (row.classList.contains('data-table-header')) return;

        const cells = row.querySelectorAll('.data-table-cell');
        const colName  = normalizeText((cells[1]?.innerText || '').toLowerCase());
        const colEmail = normalizeText((cells[2]?.innerText || '').toLowerCase());

        let match = false;

        if (filterValue === "") {
            match = colName.includes(searchValue) || colEmail.includes(searchValue);
        } else if (filterValue === "name") {
            match = colName.includes(searchValue);
        } else if (filterValue === "email") {
            match = colEmail.includes(searchValue);
        }

        row.style.display = match ? "" : "none";
    });
}

function clearUserFilters() {
    document.getElementById('filter-category').value = "";
    document.getElementById('search-input').value = "";
    // Mostrar todas las filas
    document.querySelectorAll('.data-table-row').forEach(row => {
        row.style.display = "";
    });
}

async function confirmAndDeleteUser(userId) {
    if (typeof Confirm === 'undefined' || typeof Confirm.open !== 'function') {
        // Fallback simple si por alguna razón no está disponible el modal
        if (!confirm('¿Seguro que quieres eliminar este usuario?')) return;
        return deleteUser(userId);
    }

    const ok = await Confirm.open({
        title: 'Eliminar usuario',
        message: 'Esta acción eliminará el usuario. ¿Deseas continuar?',
        confirmText: 'Eliminar',
        cancelText: 'Cancelar',
        danger: true
    });

    if (!ok) return;

    deleteUser(userId);
}

async function deleteUser(userId) {
    try {
        const r = await fetch(`{{ url('dashboard/users') }}/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': getCsrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const body = await r.json().catch(() => ({}));

        if (r.ok) {
            if (typeof Toast !== 'undefined' && Toast.success) {
                Toast.success(body.message || 'Usuario eliminado con éxito.');
            }
            // puedes quitar solo la fila o recargar, igual que en series
            setTimeout(() => location.reload(), 900);
        } else {
            if (typeof Toast !== 'undefined' && Toast.error) {
                Toast.error(body.message || 'No se pudo eliminar el usuario.');
            }
        }
    } catch (err) {
        if (typeof Toast !== 'undefined' && Toast.error) {
            Toast.error('Hubo un problema de red al eliminar el usuario.');
        }
    }
}
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
  color: var(--govco-white-color);
  font-family: var(--govco-font-primary);
  font-size: 20px;
  font-weight: bold;
}
</style>
