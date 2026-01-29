<div id="modalEditUser" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeEditModal()">&times;</span>
        <h2>Editar Usuario</h2>

        <form id="editUserForm" class="modal-form">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit_id" name="id">

            <div class="modal-field">
                <label for="edit_name">Nombre:</label>
                <input type="text" id="edit_name" name="name" required>
            </div>

            <div class="modal-field">
                <label for="edit_email">Email:</label>
                <input type="email" id="edit_email" name="email" required>
            </div>

            <div class="modal-field">
                <label for="edit_dependency_id">Dependencia:</label>
                <select id="edit_dependency_id" name="dependency_id">
                    <option value="" selected disabled>Seleccione una opción...</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Guardar Cambios</button>
        </form>
    </div>
</div>

<script>
function loadDependenciesForEdit(selectedId = null) {
    const select = document.getElementById('edit_dependency_id');
    if (!select) return;

    fetch("/dashboard/dependencies/all")
        .then(response => response.json())
        .then(dependencies => {
            select.innerHTML = '<option value="" disabled>Seleccione una opción...</option>';

            dependencies.forEach(dep => {
                const option   = document.createElement("option");
                option.value   = dep.id;
                option.text    = dep.name;
                select.appendChild(option);
            });

            if (selectedId) {
                select.value = String(selectedId);
            } else {
                select.selectedIndex = 0;
            }
        })
        .catch(error => {
            console.error("Error cargando dependencias:", error);
            if (typeof Toast !== 'undefined' && Toast.error) {
                Toast.error('No se pudieron cargar las dependencias.');
            }
        });
}

function openEditModal(userId) {
    fetch(`{{ url('dashboard/users') }}/${userId}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => {
        if (!r.ok) throw new Error('Error al cargar datos del usuario');
        return r.json();
    })
    .then(data => {
        document.getElementById('edit_id').value    = data.id;
        document.getElementById('edit_name').value  = data.name;
        document.getElementById('edit_email').value = data.email;

        loadDependenciesForEdit(data.dependency_id ?? null);

        document.getElementById('modalEditUser').style.display = 'flex';
    })
    .catch(() => {
        if (typeof Toast !== 'undefined' && Toast.error) {
            Toast.error('No se pudieron cargar los datos del usuario.');
        }
    });
}

function closeEditModal() {
    document.getElementById('modalEditUser').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    const editForm = document.getElementById('editUserForm');

    if (editForm) {
        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(editForm);
            const userId   = document.getElementById('edit_id').value;

            try {
                const resp = await fetch(`{{ url('dashboard/users') }}/${userId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrf()
                    }
                });

                const data = await resp.json().catch(() => ({}));

                if (resp.ok) {
                    if (typeof Toast !== 'undefined' && Toast.success) {
                        Toast.success(data.message || 'Usuario actualizado con éxito.');
                    }
                    closeEditModal();
                    setTimeout(() => location.reload(), 900);
                } else {
                    if (typeof Toast !== 'undefined' && Toast.error) {
                        Toast.error(data.message || 'No se pudo actualizar el usuario.');
                    }
                }
            } catch (_e) {
                if (typeof Toast !== 'undefined' && Toast.error) {
                    Toast.error('Hubo un problema al actualizar el usuario.');
                }
            }
        });
    }
});
</script>

<style>
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

.modal-form .modal-field select {
    background-color: #fff;
    height: auto;
}
</style>