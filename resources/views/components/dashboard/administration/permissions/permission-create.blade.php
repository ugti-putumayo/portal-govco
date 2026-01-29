<div id="modalCreatePermission" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeCreatePermissionModal()">&times;</span>
        <h2>Agregar nuevo permiso</h2>

        <form id="createPermissionForm" class="modal-form">
            @csrf

            <div class="modal-field">
                <label for="create_module_id">Módulo:</label>
                <select id="create_module_id" name="module_id">
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
                <label for="create_route_name">Ruta (opcional, usa el nombre como clave):</label>
                <select id="create_route_name">
                    <option value="">Seleccione una ruta...</option>
                </select>
                <small>Al seleccionar una ruta, la clave se llenará con el nombre de la ruta.</small>
            </div>

            <div class="modal-field">
                <label for="create_key">Clave (key):</label>
                <input type="text" id="create_key" name="key" required>
            </div>

            <div class="modal-field">
                <label for="create_name">Nombre (descriptivo):</label>
                <input type="text" id="create_name" name="name" required>
            </div>

            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
async function openCreatePermissionModal() {
    const modal        = document.getElementById('modalCreatePermission');
    const form         = document.getElementById('createPermissionForm');
    const moduleSelect = document.getElementById('create_module_id');
    const routeSelect  = document.getElementById('create_route_name');

    if (!modal || !form) return;

    form.reset();

    try {
        await loadPermissionRoutes();
        const routePrefix = getModuleRoutePrefix(moduleSelect);
        fillRouteSelect(routeSelect, null, routePrefix);
    } catch (e) {
        Toast?.error?.('No se pudieron cargar las rutas.');
    }

    modal.style.display = 'flex';
}

function closeCreatePermissionModal() {
    const modal = document.getElementById('modalCreatePermission');
    if (modal) modal.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    const moduleSelect = document.getElementById('create_module_id');
    const routeSelect  = document.getElementById('create_route_name');
    const keyInput     = document.getElementById('create_key');
    const nameInput    = document.getElementById('create_name');

    if (moduleSelect && routeSelect) {
        moduleSelect.addEventListener('change', async function () {
            try {
                await loadPermissionRoutes();
                const routePrefix = getModuleRoutePrefix(moduleSelect);
                fillRouteSelect(routeSelect, null, routePrefix);
                if (keyInput) keyInput.value = '';
                if (nameInput) nameInput.value = '';
            } catch (e) {
                Toast?.error?.('No se pudieron cargar las rutas filtradas.');
            }
        });
    }

    if (routeSelect && keyInput) {
        routeSelect.addEventListener('change', function () {
            if (!this.value) return;

            const key = buildPermissionKeyFromRoute(this.value);
            keyInput.value = key;

            if (nameInput && !nameInput.value) {
                const action = key.split('.').pop() || '';
                let readable = action;

                const map = {
                    view: 'Listar',
                    create: 'Crear',
                    update: 'Editar',
                    delete: 'Eliminar',
                    download: 'Descargar',
                    print: 'Imprimir',
                    upload: 'Cargar'
                };
                readable = map[action] || action;

                nameInput.value = readable;
            }
        });
    }
});
</script>
@endpush

<style>
.modal-form .modal-field {
    margin-bottom: 1rem;
}

.modal-form .modal-field label {
    display: block;
    margin-bottom: 0.25rem;
    font-weight: 600;
}

.modal-form .modal-field input,
.modal-form .modal-field select {
    width: 100%;
    box-sizing: border-box;
    display: block;
    padding: 0.65rem 0.75rem;
    border-radius: 4px;
    border: 1px solid #d0d0d0;
    font-family: inherit;
    font-size: 14px;
}

.modal-content {
    max-width: 520px;
    width: 100%;
}
</style>