<div id="modalCreateArea" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h2>Agregar Nueva Área</h2>
        <form id="createAreaForm">
            @csrf
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" required>

            <label for="cellphone">Telefono:</label>
            <input type="text" id="cellphone" name="cellphone">

            <label for="ext">Ext:</label>
            <input type="text" id="ext" name="ext">

            <label for="email">Email:</label>
            <input type="text" id="email" name="email">

            <label for="address">Dirección:</label>
            <input type="text" id="address" name="address">

            <label for="description">Descripción:</label>
            <textarea id="description" name="description" rows="4"></textarea>

            <label for="image">Imagen:</label>
            <input type="file" id="image" name="image" accept="image/*">
            
            <label for="ubication">Ubicación:</label>
            <input type="text" id="ubication" name="ubication">

            <label for="shortname">Abreviatura:</label>
            <input type="text" id="shortname" name="shortname" required>

            <label for="user_id">Jefe de área:</label>
            <select id="user_id" name="user_id" required>
                <option value="">Seleccione un Jefe de área</option>
            </select>

            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>

<script>
function openModal() {
  document.getElementById('modalCreateArea').style.display = 'flex';
}

function closeModal() {
  document.getElementById('modalCreateArea').style.display = 'none';
}

document.addEventListener("DOMContentLoaded", function () {
  fetch("/dashboard/users/bosses", {
    headers: { "X-Requested-With": "XMLHttpRequest" }
  })
    .then(response => response.json())
    .then(users => {
      const select = document.getElementById("user_id");
      if (!select) return;

      users.forEach(user => {
        const option = document.createElement("option");
        option.value = user.id;
        option.textContent = user.name;
        select.appendChild(option);
      });
    })
    .catch(error => {
      console.error("Error cargando jefes de área:", error);
      Toast.error('Error cargando jefes de área.');
    });

  const form = document.getElementById("createAreaForm");
  if (!form) return;

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch("{{ route('dashboard.dependencies.store') }}", {
      method: "POST",
      body: formData,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": getCsrf()
      }
    })
    .then(async response => {
      const data = await response.json().catch(() => ({}));
      if (!response.ok) {
        if (response.status === 422 && data.errors) {
          const msg = Object.values(data.errors).flat().join("\n");
          throw new Error(msg);
        }
        throw new Error(data.message || "Error en la respuesta del servidor");
      }
      return data;
    })
    .then(data => {
      Toast.success(data.message || 'Área creada con éxito.');
      closeModal();
      setTimeout(() => location.reload(), 900);
    })
    .catch(error => {
      console.error("Error:", error);
      Toast.error(error.message || 'Hubo un problema al crear el área.');
    });
  });
});
</script>