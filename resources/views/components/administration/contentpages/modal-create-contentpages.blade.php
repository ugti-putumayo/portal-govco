<div id="modalCreateContentPage" class="modal-overlay">
  <div class="modal-content">
    <span class="close-modal" onclick="closeModalContentPage()">&times;</span>
    <h2>Agregar nueva Content Page</h2>

    <form id="createContentPageForm" enctype="multipart/form-data">
      @csrf

      <label for="module">Módulo:</label>
      <input type="text" id="module" name="module" placeholder="p.ej. transparency" required>

      <label for="title">Título:</label>
      <input type="text" id="title" name="title" required>

      <label for="slug">Slug:</label>
      <input type="text" id="slug" name="slug" placeholder="se-generará-desde-el-titulo" required>

      <label for="ordering">Orden:</label>
      <input type="number" id="ordering" name="ordering" min="0" value="0">

      <div class="row-checkbox">
        <input type="checkbox" id="state" name="state" value="1" checked>
        <label for="state">Activo</label>
      </div>

      <label for="image">Imagen (opcional):</label>
      <input type="file" id="image" name="image" accept="image/*">

      <fieldset>
        <legend>Meta (opcional)</legend>
        <label for="meta_title">Meta Title:</label>
        <input type="text" id="meta_title" name="meta[title]" placeholder="Título SEO">

        <label for="meta_description">Meta Description:</label>
        <textarea id="meta_description" name="meta[description]" rows="3" placeholder="Descripción SEO"></textarea>
      </fieldset>

      <button type="submit" class="btn-submit">Guardar</button>
    </form>
  </div>
</div>

<script>
function openModalContentPage() {
  document.getElementById('modalCreateContentPage').style.display = 'flex';
}

function closeModalContentPage() {
  document.getElementById('modalCreateContentPage').style.display = 'none';
}

function slugify(str) {
  return (str || '')
    .toString()
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .substring(0, 255);
}

(function attachSlugAutofill(){
  const titleEl = document.getElementById('title');
  const slugEl  = document.getElementById('slug');
  let userEditedSlug = false;

  slugEl.addEventListener('input', () => { userEditedSlug = slugEl.value.trim().length > 0; });
  titleEl.addEventListener('input', () => {
    if (!userEditedSlug || slugEl.value.trim() === '') {
      slugEl.value = slugify(titleEl.value);
    }
  });
})();

document.getElementById("createContentPageForm").addEventListener("submit", function (event) {
  event.preventDefault();

  const form = this;
  const formData = new FormData(form);

  if (!form.querySelector('#state').checked) {
    formData.set('state', '0');
  }

  fetch("{{ route('dashboard.contentpages.store') }}", {
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
        Toast.success(body.message || 'Página creada exitosamente.');
        closeModalContentPage();
        setTimeout(() => location.reload(), 900);
      } else {
        console.error("Respuesta inesperada:", body);
        Toast.error(body.message || 'No se pudo crear la página.');
      }
    })
    .catch(error => {
      console.error("Error inesperado:", error);
      Toast.error('Hubo un problema al crear la página.');
    });
});
</script>

<style>
  #modalCreateContentPage .modal-content { text-align: left; }

  #modalCreateContentPage .modal-content form input[type="text"],
  #modalCreateContentPage .modal-content form input[type="number"],
  #modalCreateContentPage .modal-content form input[type="file"],
  #modalCreateContentPage .modal-content form textarea {
    width: 100%;
    box-sizing: border-box;
    display: block;
  }

  #modalCreateContentPage .modal-content form textarea {
    resize: vertical;
    min-height: 110px;
  }

  #modalCreateContentPage .modal-content form label {
    display: block;
    margin: 12px 0 6px;
  }

  #modalCreateContentPage .modal-content input[type="checkbox"]{
    width: auto;            /* cancela el 100% global */
    padding: 0;             /* quita el padding de inputs */
    margin: 0;              /* alineado al borde izquierdo */
    border: none;           /* evita recuadros raros si hay reset global */
    box-shadow: none;
    appearance: auto;       /* usa el checkbox nativo */
    height: auto;           /* evita alturas forzadas */
  }

  /* Fila del checkbox (si usas la versión en <div class="row-checkbox">) */
  #modalCreateContentPage .row-checkbox{
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    margin: 12px 0;
    text-align: left;
  }
  #modalCreateContentPage .row-checkbox > label{
    all: unset;
    cursor: pointer;
    line-height: 1.2;
  }

  #modalCreateContentPage .modal-content form fieldset {
    padding: 12px 16px;
    margin-top: 12px;
    border: 1px solid var(--govco-border-color, #ddd);
  }
  #modalCreateContentPage .modal-content form legend {
    margin: 0 auto 8px;
    font-weight: 600;
  }
</style>