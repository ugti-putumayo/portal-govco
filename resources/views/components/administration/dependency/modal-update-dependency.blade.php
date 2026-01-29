<div id="modalEditArea" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeEditModal()">&times;</span>
        <h2>Editar Área</h2>
        
        <form id="editAreaForm" class="modal-form">
            @csrf
            <input type="hidden" id="edit_id" name="id">

            <label for="edit_name">Nombre:</label>
            <input type="text" id="edit_name" name="name" required>

            <label for="edit_cellphone">Teléfono:</label>
            <input type="text" id="edit_cellphone" name="cellphone">

            <label for="edit_ext">Ext:</label>
            <input type="text" id="edit_ext" name="ext">

            <label for="edit_email">Email:</label>
            <input type="text" id="edit_email" name="email">

            <label for="edit_address">Dirección:</label>
            <input type="text" id="edit_address" name="address">

            <label for="edit_description">Descripción:</label>
            <textarea id="edit_description" name="description" rows="4"></textarea>

            <label for="edit_image">Imagen:</label>
            <input type="file" id="edit_image" name="image" accept="image/*">

            <label for="edit_ubication">Ubicación:</label>
            <input type="text" id="edit_ubication" name="ubication">

            <label for="edit_shortname">Abreviatura:</label>
            <input type="text" id="edit_shortname" name="shortname" required>

            <label for="edit_user_id">Jefe de área:</label>
            <select id="edit_user_id" name="user_id" required>
                <option value="">Seleccione un Jefe de área</option>
            </select>

            <button type="submit" class="btn-submit">Guardar Cambios</button>
        </form>
    </div>
</div>

<script>
function openEditModal(areaId) {
    fetch(`/dashboard/dependencies/${areaId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_name').value = data.name ?? '';
            document.getElementById('edit_cellphone').value = data.cellphone ?? '';
            document.getElementById('edit_ext').value = data.ext ?? '';
            document.getElementById('edit_email').value = data.email ?? '';
            document.getElementById('edit_address').value = data.address ?? '';
            document.getElementById('edit_description').value = data.description ?? '';
            document.getElementById('edit_ubication').value = data.ubication ?? '';
            document.getElementById('edit_shortname').value = data.shortname ?? '';

            const select = document.getElementById('edit_user_id');
            select.innerHTML = '<option value="">Seleccione un Jefe de área</option>';

            fetch("{{ route('bosses') }}")
                .then(res => res.json())
                .then(jefes => {
                    jefes.forEach(jefe => {
                        const option = document.createElement("option");
                        option.value = jefe.id;
                        option.text = jefe.name;
                        if (jefe.id === data.user_id) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    });

                    document.getElementById('modalEditArea').style.display = 'flex';
                });
        })
        .catch(error => 
            console.error("Error al cargar datos:", error),
            Toast.error(error.message || 'Hubo un problema al crear el área.')
        );
}

document.getElementById("editAreaForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const form = document.getElementById("editAreaForm");
    const formData = new FormData(form);
    formData.append('_method', 'PUT');

    const areaId = document.getElementById('edit_id').value;

    fetch(`/dashboard/dependencies/${areaId}`, {
        method: "POST",
        body: formData,
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": getCsrf()
        }
    })
    .then(response => {
        if (!response.ok) throw new Error("Error en la respuesta del servidor");
        return response.json();
    })
    .then(data => {
        Toast.success(data.message || 'Área actualizada con éxito.');
        closeEditModal();
        setTimeout(() => location.reload(), 900);
    })
    .catch(error => {
        console.error("Error:", error);
        Toast.error(error.message || 'Hubo un problema al actualizar el área.');
    });
});

function closeEditModal() {
    document.getElementById('modalEditArea').style.display = 'none';
}
</script>