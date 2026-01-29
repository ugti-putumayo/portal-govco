<div id="modalEditPublication" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalEditPublication()">&times;</span>
        <h2>Editar Publicación</h2>

        <form id="editPublicationForm" enctype="multipart/form-data">
            @csrf
            @method('POST') {{-- Se sobrescribe con PUT vía script --}}
            <input type="hidden" id="edit_id" name="id">

            <label for="edit_title">Título:</label>
            <input type="text" id="edit_title" name="title" required>

            <label for="edit_description">Descripción:</label>
            <textarea id="edit_description" name="description" rows="4" required></textarea>

            <label for="edit_type_id">Tipo de Publicación:</label>
            <select id="edit_type_id" name="type_id" required>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>

            <label for="edit_date">Fecha visible:</label>
            <input type="date" id="edit_date" name="date">

            <label for="edit_date_start">Inicio publicación:</label>
            <input type="date" id="edit_date_start" name="date_start">

            <label for="edit_date_end">Fin publicación:</label>
            <input type="date" id="edit_date_end" name="date_end">

            <label for="edit_image">Imagen destacada (opcional):</label>
            <input type="file" id="edit_image" name="image" accept="image/*">

            <img id="preview_edit_image" src="" alt="Imagen actual" style="max-height: 150px; display: none; margin-top: 10px; border-radius: 8px;">

            <label for="edit_document">Documento adjunto (PDF o DOCX):</label>
            <input type="file" id="edit_document" name="document" accept=".pdf,.docx">

            <input type="hidden" name="state" value="1">
            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

            <button type="submit" class="btn-submit">Actualizar</button>
        </form>
    </div>
</div>

<script>
function openModalEditPublication(id) {
    fetch(`/dashboard/publication/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("edit_id").value = data.id;
            document.getElementById("edit_title").value = data.title || '';
            document.getElementById("edit_description").value = data.description || '';
            document.getElementById("edit_type_id").value = data.type_id || '';
            document.getElementById("edit_date").value = data.date || '';
            document.getElementById("edit_date_start").value = data.date_start || '';
            document.getElementById("edit_date_end").value = data.date_end || '';

            const preview = document.getElementById("preview_edit_image");
            if (data.image) {
                preview.src = `/storage/${data.image}`;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }

            document.getElementById("modalEditPublication").style.display = "flex";
        })
        .catch(err => {
            console.error("Error al cargar publicación:", err);
            alert("No se pudo cargar la publicación.");
        });
}

function closeModalEditPublication() {
    document.getElementById("modalEditPublication").style.display = "none";
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("editPublicationForm");
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const id = document.getElementById("edit_id").value;
        const formData = new FormData(this);

        fetch(`/dashboard/publication/${id}`, {
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
                alert(body.message || "Publicación actualizada");
                closeModalEditPublication();
                location.reload();
            }
        })
        .catch(err => {
            console.error("Error:", err);
            alert("No se pudo actualizar la publicación.");
        });
    });
});
</script>