<div id="modalEditInstitutionalContent" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalInstitutionalContent()">&times;</span>
        <h2>Editar Contenido</h2>

        <form id="editInstitutionalContentForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-content-id">

            <div class="form-group">
                <label>Sección</label>
                <input type="text" name="section" id="edit-section" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Contenido</label>
                <textarea id="edit-tinymce-editor" name="content" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn-submit">Actualizar</button>
        </form>
    </div>
</div>
<script>
function openModalEditInstitutionalContent(id) {
    fetch(`/dashboard/institutionalcontent/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("edit-content-id").value = data.id;
            document.getElementById("edit-section").value = data.section || '';
            document.getElementById("edit-tinymce-editor").value = data.content || '';

            const modal = document.getElementById('modalEditInstitutionalContent');
            modal.style.display = 'flex';

            setTimeout(() => {
                if (tinymce.get('edit-tinymce-editor')) {
                    tinymce.get('edit-tinymce-editor').remove();
                }

                tinymce.init({
                    selector: '#edit-tinymce-editor',
                    license_key: 'gpl',
                    height: 400,
                    plugins: 'image link code table fullscreen',
                    toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code fullscreen',
                    automatic_uploads: true,
                    branding: false,
                    setup: function (editor) {
                        editor.on('init', () => {
                            editor.setContent(data.content || '');
                        });
                    },
                    images_upload_handler: function (blobInfo) {
                        return new Promise((resolve, reject) => {
                            const formData = new FormData();
                            formData.append('file', blobInfo.blob(), blobInfo.filename());
                            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                            fetch('/dashboard/institutionalcontent/upload-image', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.location && typeof data.location === 'string') {
                                    resolve(data.location);
                                } else {
                                    Toast.error('La respuesta no contiene una URL válida.');
                                    //reject('La respuesta no contiene una URL válida.');
                                }
                            })
                            .catch(error => {
                                Toast.error(error.message || 'Error al subir la imagen');
                                //reject('Error al subir imagen: ' + error.message);                                
                            });
                        });
                    }
                });
            }, 50);
        })
        .catch(err => {
            Toast.error("No se pudo cargar la información del contenido.");
        });
}

function closeModalEditInstitutionalContent() {
    document.getElementById('modalEditInstitutionalContent').style.display = 'none';
}

document.getElementById("editInstitutionalContentForm").addEventListener("submit", function (event) {
    event.preventDefault();
    tinymce.triggerSave();

    const id = document.getElementById('edit-content-id').value;
    const formData = new FormData(this);
    fetch(`/dashboard/institutionalcontent/${id}`, {
        method: "POST",
        body: formData,
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": getCsrf()
        }
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
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
            Toast.success(body.message || 'Contenido actualizado exitosamente.');
            closeModalEditInstitutionalContent();
            setTimeout(() => location.reload(), 900);
        } else {
            console.error("Respuesta inesperada:", body);
            Toast.error(body.message || 'No se pudo actualizar el contenido.');
        }
    })
    .catch(error => {
        console.error("Error inesperado:", error);
        Toast.error('Hubo un problema al actualizar el contenido.');
    });
});
</script>