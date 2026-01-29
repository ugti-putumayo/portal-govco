<div id="modalCreateEvent" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalCreateEvent()">&times;</span>
        <h2>Agregar Nuevo Evento</h2>

        <form id="createEventForm" enctype="multipart/form-data">
            @csrf

            <label for="title">Título:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Descripción:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="start">Fecha inicio:</label>
            <input type="datetime-local" id="start" name="start">

            <label for="end">Fecha fin:</label>
            <input type="datetime-local" id="end" name="end">

            <label for="location">Ubicación</label>
            <input type="text" id="location" name="location" required>

            <label for="image">Imagen:</label>
            <input type="file" id="image" name="image" accept="image/*">

            <label for="is_public">Pública?:</label>
            <input type="text" id="is_public" name="is_public">

            <label for="dependency">Dependencia:</label>
            <select id="dependency" name="dependency" required>
                <option value="" selected disabled>Seleccione una opción...</option>
            </select>

            <label for="visibility">Visibilidad:</label>
            <select id="visibility" name="visibility" required>
                <option value="" selected disabled>Seleccione una opción...</option>
                <option value="Public">Público general</option>
                <option value="Dependency">Solo dependencias públicas</option>
                <option value="Internal">Interno</option>
            </select>

            <input type="hidden" name="state" value="1">
            <input type="hidden" name="created_by" value="{{ auth()->id() }}">

            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>
<script>
function openModalCreateEvent() {
    document.getElementById('modalCreateEvent').style.display = 'flex';
}

function closeModalCreateEvent() {
    document.getElementById('modalCreateEvent').style.display = 'none';
}

document.addEventListener("DOMContentLoaded", function () {
    fetch("/dashboard/dependencies/all")
        .then(response => response.json())
        .then(dependencies => {
            const select = document.getElementById("dependency");
            select.innerHTML = '';

            dependencies.forEach(dep => {
                let option = document.createElement("option");
                option.value = dep.name;
                option.text = dep.name;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error("Error cargando dependencias:", error);
        });
});

document.getElementById("createEventForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch("{{ route('dashboard.events.store') }}", {
        method: "POST",
        body: formData,
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        if (status === 422) {
            console.error("Errores de validación:", body);
            alert("Error: " + Object.values(body.errors).flat().join("\n"));
        } else if (status === 500) {
            console.error("Error del servidor:", body);
            alert("Error interno del servidor. Consulta la consola.");
        } else {
            alert(body.message || "Evento creado exitosamente.");
            closeModalCreateEvent();
            location.reload();
        }
    })
    .catch(error => {
        console.error("Error inesperado:", error);
        alert("Hubo un problema al crear el evento.");
    });
});
</script>