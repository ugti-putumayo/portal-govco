<div id="modalEditEntityService" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalEditEntityService()">&times;</span>
        <h2>Editar Servicio</h2>

        <form id="editEntityServiceForm" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <input type="hidden" id="edit_id" name="id">

            <label for="title">Título:</label>
            <input type="text" id="edit_title" name="title" required>

            <label for="slug">Slug:</label>
            <input type="text" id="edit_slug" name="slug" required>

            <label for="existing_icon">Seleccionar ícono existente:</label>
            <select id="edit_existing_icon" name="existing_icon" class="form-control">
                <option value="">-- Ninguno --</option>
                @foreach($icons as $icon)
                    <option value="{{ $icon }}">{{ $icon }}</option>
                @endforeach
            </select>

            <label for="icon">O subir nuevo ícono (SVG):</label>
            <input type="file" id="edit_icon" name="icon" accept=".svg">

            <label for="description">Descripción:</label>
            <textarea id="edit_description" name="description" rows="4" required></textarea>

            <label for="type_id">Tipo de Servicio:</label>
            <select id="edit_type_id" name="type_id" required>
                <option value="" disabled selected>Selecciona un tipo...</option>
            </select>

            <label for="url">Url:</label>
            <input type="text" id="edit_url" name="url">

            <button type="submit" class="btn-submit">Actualizar</button>
        </form>
    </div>
</div>
<script>
function openModalEditEntityService(id) {
    fetch(`/dashboard/entityservice/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("edit_id").value = data.id;
            document.getElementById("edit_title").value = data.title || '';
            document.getElementById("edit_slug").value = data.slug || '';
            document.getElementById("edit_description").value = data.description || '';
            document.getElementById("edit_url").value = data.url || '';

            const iconSelect = document.getElementById("edit_existing_icon");
            if (data.icon) {
                for (let i = 0; i < iconSelect.options.length; i++) {
                    if (iconSelect.options[i].value === data.icon) {
                        iconSelect.options[i].selected = true;
                        break;
                    }
                }
            }

            const typeSelect = document.getElementById("edit_type_id");
            if (typeSelect.options.length <= 1) {
                fetch("/dashboard/service/type")
                    .then(response => response.json())
                    .then(types => {
                        types.forEach(type => {
                            const option = document.createElement("option");
                            option.value = type.id;
                            option.textContent = type.name;
                            typeSelect.appendChild(option);
                        });
                        typeSelect.value = data.type_id;
                    });
            } else {
                typeSelect.value = data.type_id;
            }

            document.getElementById("modalEditEntityService").style.display = "flex";
        })
        .catch(err => {
            console.error("Error al cargar el servicio:", err);
            Toast.error(err.message || "No se pudo cargar la información del servicio.");
        });
}

function closeModalEditEntityService() {
    document.getElementById("modalEditEntityService").style.display = "none";
}

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("editEntityServiceForm").addEventListener("submit", function (e) {
        e.preventDefault();

        const id = document.getElementById("edit_id").value;
        const formData = new FormData(this);

        fetch(`/dashboard/entityservice/${id}`, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": getCsrf(),
                "X-HTTP-Method-Override": "PUT"
            }
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            if (status === 422) {
                console.error("Errores de validación:", body);
                const msg = body.errors
                ? Object.values(body.errors || {}).flat().join("\n")
                : 'Hay errores de validación en el formulario.';
                Toast.error(msg, { title: 'Validación' });
            } else if (status >= 500) {
                console.error("Error del servidor:", body);
                Toast.error('Error interno del servidor. Revisa la consola.');
            } else if (status >= 200 && status < 300) {
                Toast.success(body.message || 'Servicio actualizado exitosamente.');
                closeModalEditEntityService();
                setTimeout(() => location.reload(), 900);
            } else {
                console.error("Respuesta inesperada:", body);
                Toast.error(body.message || 'No se pudo actualiza el servicio.');
            }
            })
            .catch(error => {
            console.error("Error inesperado:", error);
            Toast.error('Hubo un problema al actualizar el servicio.');
        });
    });
});
</script>