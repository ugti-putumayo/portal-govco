<div id="modalEditContentPage" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <span class="close-modal" onclick="closeModalEditContentPage()">&times;</span>
    <h2>Editar Content Page</h2>

    <form id="editContentPageForm" enctype="multipart/form-data">
      @csrf
      @method('POST')
      <input type="hidden" id="edit_id" name="id">

      <label for="edit_module">Módulo:</label>
      <input type="text" id="edit_module" name="module" required>

      <label for="edit_title">Título:</label>
      <input type="text" id="edit_title" name="title" required>

      <label for="edit_slug">Slug:</label>
      <input type="text" id="edit_slug" name="slug" required>

      <label for="edit_ordering">Orden:</label>
      <input type="number" id="edit_ordering" name="ordering" min="0" value="0">

      <label for="edit_state" style="display:flex;align-items:center;gap:.5rem;">
        <input type="checkbox" id="edit_state" name="state" value="1">
        Activo
      </label>

      <label for="edit_image">Imagen (opcional):</label>
      <input type="file" id="edit_image" name="image" accept="image/*">
      <img id="preview_edit_image" src="" alt="Imagen actual" style="max-height:150px;display:none;margin-top:10px;border-radius:8px;">

      <fieldset>
        <legend>Meta (opcional)</legend>
        <label for="edit_meta_title">Meta Title:</label>
        <input type="text" id="edit_meta_title" name="meta[title]">
        <label for="edit_meta_description">Meta Description:</label>
        <textarea id="edit_meta_description" name="meta[description]" rows="3"></textarea>
      </fieldset>

      <button type="submit" class="btn-submit">Actualizar</button>
    </form>
  </div>
</div>

<script>
function openModalEditContentPage(id) {
  fetch(`/dashboard/contentpages/${id}/edit`, { headers: { "X-Requested-With": "XMLHttpRequest" }})
    .then(res => res.json())
    .then(data => {
      document.getElementById("edit_id").value = data.id;
      document.getElementById("edit_module").value = data.module ?? '';
      document.getElementById("edit_title").value  = data.title ?? '';
      document.getElementById("edit_slug").value   = data.slug ?? '';
      document.getElementById("edit_ordering").value = (typeof data.ordering !== 'undefined') ? data.ordering : 0;

      const stateEl = document.getElementById("edit_state");
      stateEl.checked = !!data.state;

      const meta = data.meta || {};
      document.getElementById("edit_meta_title").value = meta.title ?? '';
      document.getElementById("edit_meta_description").value = meta.description ?? '';

      const preview = document.getElementById("preview_edit_image");
      if (data.image) {
        const isAbsolute = /^https?:\/\//i.test(data.image) || data.image.startsWith('/');
        preview.src = isAbsolute ? data.image : `{{ url('') }}/${data.image}`;
        preview.style.display = 'block';
      } else {
        preview.style.display = 'none';
      }

      document.getElementById("modalEditContentPage").style.display = "flex";
    })
    .catch(err => {
      console.error("Error al cargar Content Page:", err);
      Toast.error("No se pudo cargar la información de la página.");
    });
}

function closeModalEditContentPage() {
  document.getElementById("modalEditContentPage").style.display = "none";
}

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("editContentPageForm");
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const id = document.getElementById("edit_id").value;
    const formData = new FormData(this);

    if (!document.getElementById('edit_state').checked) {
      formData.set('state', '0');
    }

    fetch(`/dashboard/contentpages/${id}`, {
      method: "POST",
      body: formData,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": getCsrf(),
        "X-HTTP-Method-Override": "PUT"
      }
    })
    .then(res => res.json().then(data => ({ status: res.status, body: data })))
    .then(({ status, body }) => {
      if (status === 422) {
        const msg = body.errors
          ? Object.values(body.errors).flat().join("\n")
          : "Hay errores de validación.";
        Toast.error(msg, { title: "Validación" });
      } else if (status === 500) {
        console.error("Error del servidor:", body);
        Toast.error("Error interno del servidor.");
      } else if (status >= 200 && status < 300) {
        Toast.success(body.message || "Página actualizada.");
        closeModalEditContentPage();
        setTimeout(() => location.reload(), 900);
      } else {
        console.error("Error inesperado:", body);
        Toast.error(body.message || "No se pudo actualizar la página.");
      }
    })
    .catch(err => {
      console.error("Error:", err);
      Toast.error("No se pudo actualizar la página.");
    });
  });
});
</script>