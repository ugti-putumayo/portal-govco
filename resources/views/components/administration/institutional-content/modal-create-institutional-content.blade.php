<div id="modalCreateInstitutionalContent" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalInstitutionalContent()">&times;</span>
        <h2>Agregar Nuevo Contenido</h2>

        <form id="createInstitutionalContentForm" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Sección</label>
                <input type="text" name="section" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Contenido</label>
                <textarea id="tinymce-editor" name="content" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>
<script>
function openModalCreateInstitutionalContent() {
    const modal = document.getElementById('modalCreateInstitutionalContent');
    modal.style.display = 'flex';

    setTimeout(() => {
        if (typeof tinymce === 'undefined' || !tinymce.init) {
            console.error('TinyMCE no está disponible.');
            return;
        }

        const textarea = document.querySelector('#tinymce-editor');
        if (!textarea) {
            console.warn('Textarea no encontrado.');
            return;
        }

        if (tinymce.get('tinymce-editor')) {
            tinymce.get('tinymce-editor').remove();
        }

        tinymce.init({
            selector: '#tinymce-editor',
            license_key: 'gpl',
            height: 400,
            plugins: 'image link code table fullscreen',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code fullscreen',
            automatic_uploads: true,
            branding: false,
            setup: function (editor) {
                editor.on('BeforeUpload', () => {
                    const body = document.querySelector('body');
                    if (body && body.__x && body.__x.$data) {
                        body.__x.$data.loading = true;
                    }
                });

                editor.on('UploadComplete', () => {
                    const body = document.querySelector('body');
                    if (body && body.__x && body.__x.$data) {
                        body.__x.$data.loading = false;
                    }
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
                            reject('La respuesta no contiene una URL válida.');
                        }
                    })
                    .catch(error => {
                        reject('Error al subir imagen: ' + error.message);
                        Toast.error(error.message || 'Error al subir la imagen.');
                    });
                });
            }
        });
    }, 50);
}

function closeModalInstitutionalContent() {
    document.getElementById('modalCreateInstitutionalContent').style.display = 'none';
}

document.getElementById("createInstitutionalContentForm").addEventListener("submit", function (event) {
    event.preventDefault();
    tinymce.triggerSave();
    const formData = new FormData(this);
    fetch("{{ route('dashboard.institutionalcontent.store') }}", {
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
            Toast.success(body.message || 'Contenido creado exitosamente.');
            closeModalInstitutionalContent();
            setTimeout(() => location.reload(), 900);
        } else {
            console.error("Respuesta inesperada:", body);
            Toast.error(body.message || 'No se pudo crear el contenido.');
        }
    })
    .catch(error => {
        console.error("Error inesperado:", error);
        Toast.error('Hubo un problema al crear el contenido.');
    });
});
</script>

<style>
trix-editor {
    background-color: #fff;
    border: 1px solid #ccc;
    min-height: 200px;
    padding: 10px;
}
</style>