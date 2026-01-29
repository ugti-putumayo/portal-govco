@extends('dashboard.dashboard')

@section('content')
<div class="container-modules with-app-navbar">
    <div class="navbar app-navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/permission-white.svg') }}" class="app-navbar__icon" alt="Permisos">
            <h2 class="app-navbar__title">Gestión de Permisos</h2>
        </div>

        <div class="app-navbar__right navbar-filters filters-card">
            <select id="filter-category" class="filter-select">
                <option value="">Filtrar por...</option>
                <option value="name">Nombre</option>
                <option value="key">Clave</option>
                <option value="module">Módulo</option>
            </select>

            <input type="text"
                   id="search-input"
                   class="search-box"
                   placeholder="Buscar permisos...">

            <button class="search-btn" type="button" onclick="searchPermissions()" title="Buscar">🔍</button>
            <button class="search-btn" type="button" onclick="clearPermissionFilters()" title="Limpiar filtros" style="line-height:28px;">
                ❌
            </button>
        </div>
    </div>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof Toast !== 'undefined' && Toast.success) {
                    Toast.success(@json(session('success')));
                }
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof Toast !== 'undefined' && Toast.error) {
                    Toast.error(@json(session('error')));
                }
            });
        </script>
    @endif

    <div class="content-modules">
        <div class="data-table" style="--cols: 60px 1.3fr 1.4fr 1.2fr 110px;">
            <div class="data-table-header">
                <div class="data-table-cell">ID</div>
                <div class="data-table-cell">Módulo</div>
                <div class="data-table-cell">Clave</div>
                <div class="data-table-cell">Nombre</div>
                <div class="data-table-cell">Acciones</div>
            </div>

            @forelse ($permissions as $permission)
                <div class="data-table-row" id="permission-row-{{ $permission->id }}">
                    <div class="data-table-cell" data-label="ID">
                        {{ $permission->id }}
                    </div>

                    <div class="data-table-cell" data-label="Módulo">
                        {{ optional($permission->module)->name ?? 'Sin módulo' }}
                    </div>

                    <div class="data-table-cell" data-label="Clave">
                        {{ $permission->key }}
                    </div>

                    <div class="data-table-cell" data-label="Nombre">
                        {{ $permission->name }}
                    </div>

                    <div class="data-table-cell" data-label="Acciones">
                        <div class="action-icons">
                            <a href="#"
                               class="btn-icon"
                               title="Editar permiso"
                               onclick="openEditPermissionModal({{ $permission->id }})">
                                <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
                            </a>

                            <a href="#"
                               class="btn-icon delete-btn"
                               title="Eliminar permiso"
                               onclick="confirmAndDeletePermission({{ $permission->id }})">
                                <img src="{{ asset('icon/destroy.svg') }}" alt="Eliminar">
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="data-table-row">
                    <div class="data-table-cell" style="grid-column: 1 / -1; text-align: center;">
                        No se encontraron permisos registrados.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="pagination-container" style="margin-top: 1.5rem;">
            {{ $permissions->links('vendor.pagination.default') }}
        </div>
    </div>
</div>

<a href="#"
   title="Agregar permiso"
   class="btn-floating"
   onclick="openCreatePermissionModal()">
    <span>+</span>
</a>

@include('components.dashboard.administration.permissions.permission-create', ['modules' => $modules])
@include('components.dashboard.administration.permissions.permission-update', ['modules' => $modules])

@endsection

@push('scripts')
<script>
function normalizeText(text) {
    return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

function searchPermissions() {
    const searchInput = document.getElementById('search-input');
    const filterSelect = document.getElementById('filter-category');
    const rows = document.querySelectorAll('.data-table-row');

    const searchValue = normalizeText((searchInput.value || '').toLowerCase().trim());
    const filterValue = filterSelect.value;

    rows.forEach(row => {
        const cells = row.querySelectorAll('.data-table-cell');
        const moduleName = normalizeText((cells[1]?.innerText || '').toLowerCase());
        const key        = normalizeText((cells[2]?.innerText || '').toLowerCase());
        const name       = normalizeText((cells[3]?.innerText || '').toLowerCase());

        let match = false;

        if (filterValue === '') {
            match = moduleName.includes(searchValue) || key.includes(searchValue) || name.includes(searchValue);
        } else if (filterValue === 'module') {
            match = moduleName.includes(searchValue);
        } else if (filterValue === 'key') {
            match = key.includes(searchValue);
        } else if (filterValue === 'name') {
            match = name.includes(searchValue);
        }

        row.style.display = match ? '' : 'none';
    });
}

function clearPermissionFilters() {
    document.getElementById('filter-category').value = '';
    document.getElementById('search-input').value = '';
    document.querySelectorAll('.data-table-row').forEach(row => {
        row.style.display = '';
    });
}

let cachedRoutes = null;

async function loadPermissionRoutes() {
    if (cachedRoutes) return cachedRoutes;

    const resp = await fetch(`{{ route('dashboard.permissions.routes') }}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });

    if (!resp.ok) throw new Error('No se pudieron cargar las rutas');

    const data = await resp.json();
    cachedRoutes = data;
    return data;
}

function getModuleRoutePrefix(selectEl) {
    if (!selectEl) return '';
    const opt = selectEl.selectedOptions[0];
    if (!opt) return '';

    const full = opt.dataset.moduleRoute || '';
    if (!full) return '';

    const lastDot = full.lastIndexOf('.');
    if (lastDot === -1) {
        return full + '.';
    }

    return full.substring(0, lastDot + 1);
}

function fillRouteSelect(selectEl, selectedKey = null, routePrefix = '') {
    if (!cachedRoutes || !selectEl) return;

    let routes = cachedRoutes;

    if (routePrefix) {
        routes = routes.filter(route => route.name.startsWith(routePrefix));
    }

    selectEl.innerHTML = '<option value="">Seleccione una ruta...</option>';

    routes.forEach(route => {
        const opt = document.createElement('option');
        opt.value = route.name; // nombre de la ruta
        opt.textContent = `${route.name} (${route.method} ${route.uri})`;
        if (selectedKey && selectedKey === route.name) {
            opt.selected = true;
        }
        selectEl.appendChild(opt);
    });
}

function buildPermissionKeyFromRoute(routeName) {
    const parts = routeName.split('.');
    if (parts.length < 2) return routeName;

    const module = parts[1];
    const actionRaw = parts[parts.length - 1];

    let action = actionRaw;
    if (actionRaw === 'index' || actionRaw === 'show') {
        action = 'view';
    } else if (actionRaw === 'store') {
        action = 'create';
    } else if (actionRaw === 'destroy') {
        action = 'delete';
    }

    return `${module}.${action}`;
}

/* CREAR PERMISO (AJAX) */
document.addEventListener('DOMContentLoaded', function () {
    const createForm = document.getElementById('createPermissionForm');
    if (createForm) {
        createForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(createForm);

            try {
                const resp = await fetch(`{{ route('dashboard.permissions.store') }}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrf(),
                    }
                });

                const data = await resp.json().catch(() => ({}));

                if (resp.ok) {
                    if (typeof Toast !== 'undefined' && Toast.success) {
                        Toast.success(data.message || 'Permiso creado con éxito.');
                    }
                    closeCreatePermissionModal();
                    setTimeout(() => location.reload(), 900);
                } else {
                    if (typeof Toast !== 'undefined' && Toast.error) {
                        Toast.error(data.message || 'No se pudo crear el permiso.');
                    }
                }
            } catch (e) {
                if (typeof Toast !== 'undefined' && Toast.error) {
                    Toast.error('Hubo un problema al crear el permiso.');
                }
            }
        });
    }

    const editForm = document.getElementById('editPermissionForm');
    if (editForm) {
        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const permissionId = document.getElementById('edit_id').value;
            const formData     = new FormData(editForm);

            try {
                const resp = await fetch(`{{ url('dashboard/permissions') }}/${permissionId}`, {
                    method: 'POST', // _method=PUT
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrf(),
                    }
                });

                const data = await resp.json().catch(() => ({}));

                if (resp.ok) {
                    if (typeof Toast !== 'undefined' && Toast.success) {
                        Toast.success(data.message || 'Permiso actualizado con éxito.');
                    }
                    closeEditPermissionModal();
                    setTimeout(() => location.reload(), 900);
                } else {
                    if (typeof Toast !== 'undefined' && Toast.error) {
                        Toast.error(data.message || 'No se pudo actualizar el permiso.');
                    }
                }
            } catch (e) {
                if (typeof Toast !== 'undefined' && Toast.error) {
                    Toast.error('Hubo un problema al actualizar el permiso.');
                }
            }
        });
    }
});

/* DELETE CON CONFIRM */
async function confirmAndDeletePermission(permissionId) {
    if (typeof Confirm === 'undefined' || typeof Confirm.open !== 'function') {
        if (!confirm('¿Seguro que quieres eliminar este permiso?')) return;
        return deletePermission(permissionId);
    }

    const ok = await Confirm.open({
        title: 'Eliminar permiso',
        message: 'Esta acción eliminará el permiso. ¿Deseas continuar?',
        confirmText: 'Eliminar',
        cancelText: 'Cancelar',
        danger: true,
    });

    if (!ok) return;
    deletePermission(permissionId);
}

async function deletePermission(permissionId) {
    try {
        const resp = await fetch(`{{ url('dashboard/permissions') }}/${permissionId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrf(),
                'Accept': 'application/json',
            }
        });

        const data = await resp.json().catch(() => ({}));

        if (resp.ok) {
            if (typeof Toast !== 'undefined' && Toast.success) {
                Toast.success(data.message || 'Permiso eliminado con éxito.');
            }
            setTimeout(() => location.reload(), 900);
        } else {
            if (typeof Toast !== 'undefined' && Toast.error) {
                Toast.error(data.message || 'No se pudo eliminar el permiso.');
            }
        }
    } catch (e) {
        if (typeof Toast !== 'undefined' && Toast.error) {
            Toast.error('Hubo un problema al eliminar el permiso.');
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