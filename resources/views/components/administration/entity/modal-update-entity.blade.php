<div id="modalEditEntity" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalEditEntity()">&times;</span>
        <h2>Editar Entidad</h2>
        <form id="editEntityForm">
            @csrf
            <input type="hidden" id="edit_id" name="id">

            <label for="edit_type">Tipo:</label>
            <select id="edit_type" name="type" required>
                <option value="" disabled>Seleccione un tipo...</option>
                <option value="Político">Político</option>
                <option value="Fiscal">Fiscal</option>
                <option value="Social">Social</option>
                <option value="Disciplinaria">Disciplinaria</option>
                <option value="Ministerios">Ministerios</option>
                <option value="Departamentos Administrativos">Departamentos Administrativos</option>
                <option value="Superintendencias">Superintendencias</option>
                <option value="Unidades Administrativas Especiales">Unidades Administrativas Especiales</option>
                <option value="Establecimientos Públicos">Establecimientos Públicos</option>
                <option value="Rama Judicial">Rama Judicial</option>
                <option value="Otro">Otro</option>
            </select>

            <label for="edit_scope">Alcance:</label>
            <select id="edit_scope" name="scope" required>
                <option value="" disabled>Seleccione un alcance...</option>
                <option value="Nacional">Nacional</option>
                <option value="Departamental">Departamental</option>
                <option value="Municipal">Municipal</option>
                <option value="Internacional">Internacional</option>
            </select>

            <label for="edit_name">Nombre:</label>
            <input type="text" id="edit_name" name="name">

            <label for="edit_phone">Teléfono:</label>
            <input type="text" id="edit_phone" name="phone">

            <label for="edit_mail">Correo:</label>
            <input type="text" id="edit_mail" name="mail">

            <label for="edit_address">Dirección:</label>
            <input type="text" id="edit_address" name="address">

            <label for="edit_link">Url:</label>
            <input type="text" id="edit_link" name="link">

            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>

<script>
function openModalEditEntity(id) {
    fetch(`/dashboard/entities/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("edit_id").value = data.id;
            document.getElementById("edit_type").value = data.type || '';
            document.getElementById("edit_scope").value = data.scope || '';
            document.getElementById("edit_name").value = data.name || '';
            document.getElementById("edit_phone").value = data.phone || '';
            document.getElementById("edit_mail").value = data.mail || '';
            document.getElementById("edit_address").value = data.address || '';
            document.getElementById("edit_link").value = data.link || '';

            document.getElementById("modalEditEntity").style.display = "flex";
        })
        .catch(err => {
            console.error("Error al cargar la entidad:", err);
            Toast.error(err.message || "No se pudo cargar la información de la entidad.");
        });
}

function closeModalEditEntity() {
    document.getElementById("modalEditEntity").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("editEntityForm");
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(form);
        formData.append('_method', 'PUT');

        const id = document.getElementById("edit_id").value;

        fetch(`/dashboard/entities/${id}`, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": getCsrf()
            }
        })
        .then(response => {
            if (!response.ok) throw new Error("Error al actualizar");
            return response.json();
        })
        .then(data => {
            Toast.success(data.message || "Entidad actualizada exitosamente.");
            closeModalEditEntity();
            setTimeout(() => location.reload(), 900);
        })
        .catch(error => {
            console.error("Error:", error);
            Toast.error(error.message || "No se pudo actualizar la entidad.");
        });
    });
});
</script>
