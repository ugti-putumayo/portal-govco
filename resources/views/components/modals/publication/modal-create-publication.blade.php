<div id="modalCreatePublication" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalPublication()">&times;</span>
        <h2>Agregar Nueva Publicación</h2>

        <form id="createPublicationForm" enctype="multipart/form-data">
            @csrf

            <label for="title">Título:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Descripción:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="type_id">Tipo de Publicación:</label>
            <select id="type_id" name="type_id" required>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>

            <label for="date">Fecha (visible):</label>
            <input type="date" id="date" name="date">

            <label for="date_start">Inicio de publicación:</label>
            <input type="date" id="date_start" name="date_start">

            <label for="date_end">Fin de publicación:</label>
            <input type="date" id="date_end" name="date_end">

            <label for="image">Imagen destacada:</label>
            <input type="file" id="image" name="image" accept="image/*">

            <label for="document">Documento adjunto (PDF o DOCX):</label>
            <input type="file" id="document" name="document" accept=".pdf,.docx">

            <input type="hidden" name="state" value="1">
            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>
<script>
function openModalPublication() {
    document.getElementById('modalCreatePublication').style.display = 'flex';
}

function closeModalPublication() {
    document.getElementById('modalCreatePublication').style.display = 'none';
}

document.getElementById("createPublicationForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch("{{ route('dashboard.publication.store') }}", {
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
            alert(body.message || "Publicación creada exitosamente.");
            closeModalPublication();
            location.reload();
        }
    })
    .catch(error => {
        console.error("Error inesperado:", error);
        alert("Hubo un problema al crear la publicación.");
    });
});
</script>
