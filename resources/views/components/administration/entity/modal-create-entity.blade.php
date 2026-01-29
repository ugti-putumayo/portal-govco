<div id="modalCreateEntity" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalEntity()">&times;</span>
        <h2>Agregar Nueva Entidad</h2>
        <form id="createEntityForm">
            @csrf
            <label for="type">Tipo:</label>
            <select id="type" name="type" required>
                <option value="" selected disabled>Seleccione un tipo...</option>
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

            <label for="scope">Alcance:</label>
            <select id="scope" name="scope" required>
                <option value="" selected disabled>Seleccione un alcance...</option>
                <option value="Nacional">Nacional</option>
                <option value="Departamental">Departamental</option>
                <option value="Municipal">Municipal</option>
                <option value="Internacional">Internacional</option>
            </select>

            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name">

            <label for="phone">Teléfono:</label>
            <input type="text" id="phone" name="phone">

            <label for="mail">Correo:</label>
            <input type="text" id="mail" name="mail">

            <label for="address">Dirección:</label>
            <input type="text" id="address" name="address">

            <label for="link">Url:</label>
            <input type="text" id="link" name="link">
            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>

<script>
function openModalEntity() {
    document.getElementById('modalCreateEntity').style.display = 'flex';
}

function closeModalEntity() {
    document.getElementById('modalCreateEntity').style.display = 'none';
}

document.getElementById("createEntityForm").addEventListener("submit", function (event) {
    event.preventDefault();
    let formData = new FormData(this);
    fetch("{{ route('dashboard.entities.store') }}", {
        method: "POST",
        body: formData,
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": getCsrf()
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Error en la respuesta del servidor");
        }
        return response.json();
    })
    .then(data => {
        if (data.message) {
            Toast.success(data.message || 'Entidad actualizada con éxito.');
            closeModalEntity();
            setTimeout(() => location.reload(), 900);
        } else {
            alert("Error al crear la entidad");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        Toast.error(error.message || "Hubo un problema al crear la entidad.");
    });
});
</script>