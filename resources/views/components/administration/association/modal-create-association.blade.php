<div id="modalCreateAssociation" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalAssociation()">&times;</span>
        <h2>Agregar Nueva Asociación</h2>

        <form id="createAssociationForm" enctype="multipart/form-data">
            @csrf

            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" required>

            <label for="classification">Clasificación:</label>
            <select id="classification" name="classification" required>
                <option value="">Selecciona una clasificación</option>
                <option value="Sin ánimo de lucro">Sin ánimo de lucro</option>
                <option value="Economía solidaria">Economía solidaria</option>
                <option value="Comunal">Comunal</option>
                <option value="Productiva">Productiva</option>
                <option value="Gremial">Gremial</option>
                <option value="Otro">Otro</option>
            </select>

            <label for="activity">Actividad:</label>
            <select id="activity" name="activity">
                <option value="">Selecciona una actividad</option>
                <option value="Agropecuaria">Agropecuaria</option>
                <option value="Cultural">Cultural</option>
                <option value="Deportiva">Deportiva</option>
                <option value="Educativa">Educativa</option>
                <option value="Ambiental">Ambiental</option>
                <option value="Tecnológica">Tecnológica</option>
                <option value="Salud">Salud</option>
                <option value="Turismo">Turismo</option>
                <option value="Artesanal">Artesanal</option>
                <option value="Comercial">Comercial</option>
                <option value="Otra">Otra</option>
            </select>

            <label for="description">Descripción:</label>
            <textarea id="description" name="description" rows="4"></textarea>

            <label for="sccope">Ámbito:</label>
            <input type="text" id="sccope" name="sccope">

            <label for="city">Ciudad:</label>
            <select id="city" name="city">
                <option value="" selected disabled>Selecciona una actividad</option>
                <option value="MOCOA">MOCOA</option>
                <option value="VILLAGARZÓN">VILLAGARZÓN</option>
                <option value="PUERTO GUZMÁN">PUERTO GUZMÁN</option>
                <option value="PUERTO CAICEDO">PUERTO CAICEDO</option>
                <option value="PUERTO ASÍS">PUERTO ASÍS</option>
                <option value="ORITO">ORITO</option>
                <option value="VALLE DEL GUAMUEZ">VALLE DEL GUAMUEZ</option>
                <option value="SAN MIGUEL">SAN MIGUEL</option>
                <option value="PUERTO LEGUIZAMO">PUERTO LEGUIZAMO</option>
                <option value="COLON">COLON</option>
                <option value="SAN FRANCISCO">SAN FRANCISCO</option>
                <option value="SANTIAGO">SANTIAGO</option>
                <option value="SIBUNDOY">SIBUNDOY</option>
            </select>

            <label for="address">Dirección:</label>
            <input type="text" id="address" name="address">

            <label for="cellphone">Teléfono:</label>
            <input type="text" id="cellphone" name="cellphone">

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email">

            <label for="link">Enlace (Web o Red Social):</label>
            <input type="url" id="link" name="link">

            <label for="image">Imagen:</label>
            <input type="file" id="image" name="image" accept="image/*">

            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>

<script>
function getCsrf() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      || document.querySelector('input[name="_token"]')?.value;
}

function openModalAssociation() {
  document.getElementById('modalCreateAssociation').style.display = 'flex';
}

function closeModalAssociation() {
  document.getElementById('modalCreateAssociation').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById("createAssociationForm");
  if (!form) return;

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch("{{ route('dashboard.association.store') }}", {
      method: "POST",
      body: formData,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": getCsrf()
      }
    })
    .then(async response => {
      const body = await response.json().catch(() => ({}));
      return { status: response.status, body };
    })
    .then(({ status, body }) => {
      if (status === 422) {
        const msg = body.errors
          ? Object.values(body.errors).flat().join("\n")
          : 'Hay errores de validación en el formulario.';
        Toast.error(msg, { title: 'Validación' });
      } else if (status === 500) {
        Toast.error('Error interno del servidor. Consulta la consola.');
      } else if (status >= 200 && status < 300) {
        Toast.success(body.message || 'Asociación creada exitosamente.');
        closeModalAssociation();
        setTimeout(() => location.reload(), 900);
      } else {
        Toast.error(body.message || 'No se pudo crear la asociación.');
      }
    })
    .catch(error => {
      Toast.error('Hubo un problema al crear la asociación.');
    });
  });
});
</script>