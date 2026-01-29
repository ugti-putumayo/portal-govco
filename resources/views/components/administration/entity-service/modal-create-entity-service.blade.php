<div id="modalCreateEntityService" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalEntityService()">&times;</span>
        <h2>Agregar Servicio</h2>

        <form id="createEntityServiceForm" enctype="multipart/form-data">
            @csrf

            <label for="title">Título:</label>
            <input type="text" id="title" name="title" required>

            <label for="slug">Slug:</label>
            <input type="text" id="slug" name="slug" required>

            <label for="existing_icon">Seleccionar ícono existente:</label>
            <select id="existing_icon" name="existing_icon" class="form-control">
                <option value="">-- Ninguno --</option>
                @foreach($icons as $icon)
                    <option value="{{ $icon }}">{{ $icon }}</option>
                @endforeach
            </select>

            <label for="icon">O subir nuevo ícono (SVG):</label>
            <input type="file" id="icon" name="icon" accept=".svg">

            <label for="description">Descripción:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="type_id">Tipo de Servicio:</label>
            <select id="type_id" name="type_id" required>
                <option value="" disabled selected>Selecciona un tipo...</option>
            </select>
            
            <label for="url">Url:</label>
            <input type="text" id="url" name="url">

            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>
<script>
function openModalCreateEntityService() {
    document.getElementById('modalCreateEntityService').style.display = 'flex';
    const select = document.getElementById('type_id');
    if (select.options.length <= 1) {
        fetch("/dashboard/service/type")
            .then(response => response.json())
            .then(data => {
                data.forEach(type => {
                    const option = document.createElement("option");
                    option.value = type.id;
                    option.textContent = type.name;
                    select.appendChild(option);
                });
            })
            .catch(error => {
                console.error("Error cargando tipos de servicio:", error);
                Toast.error(error.message || "No se pudieron cargar los tipos de servicio.");
            });
    }
}

function closeModalEntityService() {
    document.getElementById('modalCreateEntityService').style.display = 'none';
}

document.getElementById("createEntityServiceForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch("{{ route('dashboard.entityservice.store') }}", {
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
        Toast.success(body.message || 'Servicio creado exitosamente.');
        closeModalContentItem();
        setTimeout(() => location.reload(), 900);
      } else {
        console.error("Respuesta inesperada:", body);
        Toast.error(body.message || 'No se pudo crear el servicio.');
      }
    })
    .catch(error => {
      console.error("Error inesperado:", error);
      Toast.error('Hubo un problema al crear el servicio.');
    });
});
</script>