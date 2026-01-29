<div id="modalEditPermission" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeEditPermissionModal()">&times;</span>
        <h2>Editar permiso</h2>

        <form id="editPermissionForm" class="modal-form">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit_id" name="id">

            <div class="modal-field">
                <label for="edit_module_id">Módulo:</label>
                <select id="edit_module_id" name="module_id">
                    <option value="">Sin módulo asociado</option>
                    @foreach($modules as $module)
                        <option value="{{ $module->id }}"
                                data-module-route="{{ $module->route ?? '' }}">
                            {{ $module->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="modal-field">
                <label for="edit_route_name">Ruta (opcional):</label>
                <select id="edit_route_name">
                    <option value="">Seleccione una ruta...</option>
                </select>
                <small>Si seleccionas una ruta, puedes sincronizar la clave con el nombre de la ruta.</small>
            </div>

            <div class="modal-field">
                <label for="edit_key">Clave (key):</label>
                <input type="text" id="edit_key" name="key" required>
            </div>

            <div class="modal-field">
                <label for="edit_name">Nombre (descriptivo):</label>
                <input type="text" id="edit_name" name="name" required>
            </div>

            <button type="submit" class="btn-submit">Guardar Cambios</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
async function openEditPermissionModal(permissionId) {
    const modal        = document.getElementById('modalEditPermission');
    const form         = document.getElementById('editPermissionForm');
    const moduleSelect = document.getElementById('edit_module_id');
    const routeSelect  = document.getElementById('edit_route_name');

    if (!modal || !form) return;

    try {
        const resp = await fetch(`{{ url('dashboard/permissions') }}/${permissionId}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        if (!resp.ok) throw new Error('Error al cargar el permiso');
        const data = await resp.json();

        form.querySelector('#edit_id').value        = data.id;
        form.querySelector('#edit_module_id').value = data.module_id || '';
        form.querySelector('#edit_key').value       = data.key || '';
        form.querySelector('#edit_name').value      = data.name || '';

        await loadPermissionRoutes();
        const moduleRoute = getModuleRoute(moduleSelect);
        fillRouteSelect(routeSelect, data.key || null, moduleRoute);

        modal.style.display = 'flex';
    } catch (e) {
        if (typeof Toast !== 'undefined' && Toast.error) {
            Toast.error('No se pudo cargar el permiso.');
        }
    }
}

function closeEditPermissionModal() {
    const modal = document.getElementById('modalEditPermission');
    if (modal) modal.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    const moduleSelect = document.getElementById('edit_module_id');
    const routeSelect  = document.getElementById('edit_route_name');
    const keyInput     = document.getElementById('edit_key');
    const nameInput    = document.getElementById('edit_name');

    if (moduleSelect && routeSelect) {
        moduleSelect.addEventListener('change', async function () {
            try {
                await loadPermissionRoutes();
                const moduleRoute = getModuleRoute(moduleSelect);
                fillRouteSelect(routeSelect, null, moduleRoute);
            } catch (e) {
                if (typeof Toast !== 'undefined' && Toast.error) {
                    Toast.error('No se pudieron cargar las rutas filtradas.');
                }
            }
        });
    }

    if (routeSelect && keyInput) {
        routeSelect.addEventListener('change', function () {
            if (!this.value) return;
            keyInput.value = this.value;

            if (nameInput && !nameInput.value) {
                const lastPart = this.value.split('.').pop() || '';
                nameInput.value = lastPart
                    .replace(/[-_.]/g, ' ')
                    .replace(/\b\w/g, c => c.toUpperCase());
            }
        });
    }
});
</script>
@endpush