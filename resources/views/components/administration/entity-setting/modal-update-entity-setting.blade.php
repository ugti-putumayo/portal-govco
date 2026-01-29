<div id="modalEditEntitySetting" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalEditEntitySetting()">&times;</span>
        <h2>Editar Configuración</h2>

        <form id="editEntitySettingForm" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <input type="hidden" id="edit_id" name="id">

            <label for="edit_entity_name">Nombre:</label>
            <input type="text" id="edit_entity_name" name="edit_entity_name" required>

            <label for="edit_entity_acronym">Acrónimo:</label>
            <input type="text" id="edit_entity_acronym" name="edit_entity_acronym">

            <label for="edit_document_number">Número de identificación Tributaria:</label>
            <input type="text" id="edit_document_number" name="edit_document_number">

            <label for="edit_legal_representative">Representate Legal:</label>
            <input type="text" id="edit_legal_representative" name="edit_legal_representative" required>

            <label for="edit_address">Dirección:</label>
            <input type="text" id="edit_address" name="edit_address">

            <label for="edit_phone">Telefono:</label>
            <input type="text" id="edit_phone" name="edit_phone">

            <label for="edit_email">Correo electrónico:</label>
            <input type="email" id="edit_email" name="edit_email">

            <label for="edit_logo_path">Logo entidad:</label>
            <input type="file" id="edit_logo_path" name="edit_logo_path" accept="image/*">

            <img id="preview_edit_logo_path" src="" alt="Imagen actual" style="max-height: 150px; display: none; margin-top: 10px; border-radius: 8px;">

            <label for="edit_website">Página web:</label>
            <input type="url" id="edit_website" name="edit_website">  
            
            <label for="edit_department">Departamento:</label>
            <select id="edit_department" name="edit_department">
                <option value="" selected disabled>Seleccione un departamento...</option>
            </select>

            <label for="edit_city">Ciudad:</label>
            <select id="edit_city" name="edit_city">
                <option value="" selected disabled>Seleccione un municipio</option>
            </select>

            <button type="submit" class="btn-submit">Actualizar</button>
        </form>
    </div>
</div>
<script>
function openModalEditEntitySetting(id) {
    fetch(`/dashboard/settings/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("edit_id").value = data.id;
            document.getElementById("edit_entity_name").value = data.entity_name || '';
            document.getElementById("edit_entity_acronym").value = data.entity_acronym || '';
            document.getElementById("edit_document_number").value = data.document_number || '';
            document.getElementById("edit_legal_representative").value = data.legal_representative || '';
            document.getElementById("edit_address").value = data.address || '';
            document.getElementById("edit_phone").value = data.phone || '';
            document.getElementById("edit_email").value = data.email || '';
            document.getElementById("edit_website").value = data.website || '';

            fetch("{{ route('locates') }}")
                .then(res => res.json())
                .then(departments => {
                    const select = document.getElementById("edit_department");
                    select.innerHTML = '<option value="" disabled>Seleccione un departamento...</option>';
                    departments.forEach(dept => {
                        const option = document.createElement("option");
                        option.value = dept.id;
                        option.textContent = dept.name;
                        if (dept.id == data.department_id) option.selected = true;
                        select.appendChild(option);
                    });

                    if (data.department_id) {
                        fetch(`/locates/cities/${data.department_id}`)
                            .then(res => res.json())
                            .then(cities => {
                                const citySelect = document.getElementById("edit_city");
                                citySelect.innerHTML = '<option value="" disabled>Seleccione un municipio</option>';
                                cities.forEach(city => {
                                    const option = document.createElement("option");
                                    option.value = city.id;
                                    option.textContent = city.name;
                                    if (city.id == data.city_id) option.selected = true;
                                    citySelect.appendChild(option);
                                });
                            });
                    }
                });

            const preview = document.getElementById("preview_edit_logo_path");
            if (data.logo_path) {
                preview.src = `/${data.logo_path}`;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }

            document.getElementById("modalEditEntitySetting").style.display = "flex";
        })
        .catch(err => {
            console.error("Error al cargar configuración:", err);
            Toast.error(err.message || "No se pudo cargar la información.");
        });
}

function closeModalEditEntitySetting() {
    document.getElementById("modalEditEntitySetting").style.display = "none";
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("editEntitySettingForm");
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const id = document.getElementById("edit_id").value;
        const formData = new FormData(this);

        fetch(`/dashboard/settings/${id}`, {
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
                console.error("Errores de validación:", body);
                const msg = body.errors
                ? Object.values(body.errors || {}).flat().join("\n")
                : 'Hay errores de validación en el formulario.';
                Toast.error(msg, { title: 'Validación' });
            } else if (status >= 500) {
                console.error("Error del servidor:", body);
                Toast.error('Error interno del servidor. Revisa la consola.');
            } else if (status >= 200 && status < 300) {
                Toast.success(body.message || 'Configuración actualizada exitosamente.');
                closeModalEditEntitySetting();
                setTimeout(() => location.reload(), 900);
            } else {
                console.error("Respuesta inesperada:", body);
                Toast.error(body.message || 'No se pudo actualizar la configuración.');
            }
        })
        .catch(error => {
            console.error("Error inesperado:", error);
            Toast.error('Hubo un problema al actualizar la configuración.');
        });
    });
});

document.getElementById("edit_department").addEventListener("change", function () {
    const departmentId = this.value;
    const citySelect = document.getElementById("edit_city");
    citySelect.innerHTML = '<option value="" disabled selected>Seleccione un municipio</option>';

    fetch(`/locates/cities/${departmentId}`)
        .then(response => response.json())
        .then(cities => {
            cities.forEach(city => {
                const option = document.createElement("option");
                option.value = city.id;
                option.textContent = city.name;
                citySelect.appendChild(option);
            });
        });
});
</script>