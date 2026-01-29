<div id="modalEditEvent" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalEditEvent()">&times;</span>
        <h2>Editar Evento</h2>

        <form id="editEventForm" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <input type="hidden" id="edit_id" name="id">

            <label for="edit_title">Título:</label>
            <input type="text" id="edit_title" name="title" required>

            <label for="edit_description">Descripción:</label>
            <textarea id="edit_description" name="description" rows="4" required></textarea>

            <label for="edit_start">Fecha inicio:</label>
            <input type="datetime-local" id="edit_start" name="start">

            <label for="edit_end">Fecha fin:</label>
            <input type="datetime-local" id="edit_end" name="end">

            <label for="edit_location">Ubicación:</label>
            <input type="text" id="edit_location" name="location" required>

            <label for="edit_image">Imagen:</label>
            <input type="file" id="edit_image" name="image" accept="image/*">
            <img id="preview_edit_image" src="#" style="display: none; width: 100%; margin-top: 10px;" alt="Previsualización">

            <label for="edit_is_public">¿Público?:</label>
            <input type="text" id="edit_is_public" name="is_public">

            <label for="edit_dependency">Dependencia:</label>
            <select id="edit_dependency" name="dependency" required>
                <option value="" selected disabled>Seleccione una opción...</option>
            </select>

            <label for="edit_visibility">Visibilidad:</label>
            <select id="edit_visibility" name="visibility" required>
                <option value="" selected disabled>Seleccione una opción...</option>
                <option value="Public">Público general</option>
                <option value="Dependency">Solo dependencias públicas</option>
                <option value="Internal">Interno</option>
            </select>

            <input type="hidden" name="state" value="1">
            <input type="hidden" name="updated_by" value="{{ auth()->id() }}">

            <button type="submit" class="btn-submit">Actualizar</button>
        </form>
    </div>
</div>

<script>
function openEditModal(eventId) {
    fetch(`/dashboard/events/${eventId}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_title').value = data.title || '';
            document.getElementById('edit_description').value = data.description || '';
            document.getElementById('edit_start').value = data.start ? data.start.replace(' ', 'T') : '';
            document.getElementById('edit_end').value = data.end ? data.end.replace(' ', 'T') : '';
            document.getElementById('edit_location').value = data.location || '';
            document.getElementById('edit_is_public').value = data.is_public || '';
            document.getElementById('edit_visibility').value = data.visibility || '';

            // Dependencia
            fetch("/dashboard/dependencies/all")
                .then(response => response.json())
                .then(dependencies => {
                    const select = document.getElementById("edit_dependency");
                    select.innerHTML = '<option value="" disabled>Seleccione una opción...</option>';
                    dependencies.forEach(dep => {
                        const option = document.createElement("option");
                        option.value = dep.name;
                        option.textContent = dep.name;
                        if (dep.name === data.dependency) option.selected = true;
                        select.appendChild(option);
                    });
                });

            // Previsualización de imagen
            const preview = document.getElementById("preview_edit_image");
            if (data.image) {
                preview.src = data.image;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }

            document.getElementById('modalEditEvent').style.display = 'flex';
            closeEventCardModal();
        })
        .catch(err => {
            console.error("Error al cargar evento:", err);
            alert("No se pudo cargar el evento.");
        });
}

function closeModalEditEvent() {
    document.getElementById("modalEditEvent").style.display = "none";
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("editEventForm");
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const id = document.getElementById("edit_id").value;
        const formData = new FormData(this);

        fetch(`/dashboard/events/${id}`, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                "X-HTTP-Method-Override": "PUT"
            }
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            if (status === 422) {
                alert("Errores: " + Object.values(body.errors).flat().join("\n"));
            } else if (status === 500) {
                alert("Error del servidor");
            } else {
                alert(body.message || "Evento actualizado");
                closeModalEditEvent();
                location.reload();
            }
        })
        .catch(err => {
            console.error("Error:", err);
            alert("No se pudo actualizar el evento.");
        });
    });
});
</script>