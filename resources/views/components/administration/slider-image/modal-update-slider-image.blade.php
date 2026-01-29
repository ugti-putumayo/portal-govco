<script>
function openEditModal(id) {
    fetch(`/dashboard/slider/images/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("edit_id").value = data.id;
            document.getElementById("edit_title").value = data.title || '';
            document.getElementById("edit_link").value = data.link || '';
            document.getElementById("modalEditSlider").style.display = "flex";
        })
        .catch(err => {
            Toast.error(err.message || "No se pudo cargar la información.");
        });
}

function closeModalEditSlider() {
    document.getElementById('modalEditSlider').style.display = 'none';
}

document.addEventListener("DOMContentLoaded", () => {
    const editForm = document.getElementById("editSliderForm");

    editForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const id = document.getElementById("edit_id").value;
        const formData = new FormData(this);

        fetch(`/dashboard/slider/images/${id}`, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": getCsrf(),
                'X-HTTP-Method-Override': 'PUT'
            },
            credentials: "same-origin"
        })
        .then(res => res.json())
        .then(data => {
            Toast.success(data.message || 'Imagen actualizada correctamente');
            closeModalEditSlider();
            setTimeout(() => location.reload(), 900);
        })
        .catch(err => {
            Toast.error(err.message || "No se pudo actualizar la imagen.");
        });
    });
});
</script>

<div id="modalEditSlider" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalEditSlider()">&times;</span>
        <h2>Editar imagen del slider</h2>

        <form id="editSliderForm" enctype="multipart/form-data">
            @csrf
            @method('POST') {{-- Usamos método dinámico en el script --}}

            <input type="hidden" id="edit_id" name="id">

            <label for="edit_title">Título:</label>
            <input type="text" id="edit_title" name="title" maxlength="250">

            <label for="edit_link">Enlace (opcional):</label>
            <input type="text" id="edit_link" name="link" placeholder="https://">

            <label for="edit_image">Imagen:</label>
            <input type="file" id="edit_image" name="image" accept="image/*">

            {{-- Previsualización de la imagen actual --}}
            <img id="preview_image" src="" alt="Imagen actual" style="display: none; width: 100%; max-height: 150px; margin-top: 10px; border-radius: 8px;">


            <input type="hidden" name="site_id" value="1">
            <input type="hidden" name="user_register_id" value="{{ auth()->id() }}">
            <button type="submit" class="btn-submit">Actualizar</button>
        </form>
    </div>
</div>
