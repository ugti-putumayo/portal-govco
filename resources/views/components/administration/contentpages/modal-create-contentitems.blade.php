<div id="modalCreateContentItem" class="modal-overlay">
  <div class="modal-content">
    <span class="close-modal" onclick="closeModalContentItem()">&times;</span>
    <h2>Agregar nuevo Content Item</h2>

    <form id="createContentItemForm" enctype="multipart/form-data">
      @csrf

      <label for="content_page_id">Página (Content Page):</label>
      <select id="content_page_id" name="content_page_id" required>
        <option value="" disabled selected>Cargando páginas...</option>
      </select>

      <label for="title">Título:</label>
      <input type="text" id="title" name="title" required>

      <label for="ordering">Orden:</label>
      <input type="number" id="ordering" name="ordering" min="0" value="0">

      <label for="url">URL (opcional):</label>
      <input type="url" id="url" name="url" placeholder="https://...">

      <label for="document">Documento (PDF/DOC/DOCX):</label>
      <input type="file" id="document" name="document" accept=".pdf,.doc,.docx">

      <label for="image">Imagen (opcional):</label>
      <input type="file" id="image" name="image" accept="image/*">

      <label for="description">Descripción (opcional):</label>
      <textarea id="description" name="description" rows="4"></textarea>

      <!-- Extra como array libre -->
      <fieldset>
        <legend>Extra (opcional)</legend>
        <label for="extra_notes">Notas:</label>
        <input type="text" id="extra_notes" name="extra[notes]" placeholder="Texto libre">

        <label for="extra_tags">Tags (coma-separado):</label>
        <input type="text" id="extra_tags" name="extra[tags]" placeholder="ej: transparencia, reporte">
      </fieldset>

      <button type="submit" class="btn-submit">Guardar</button>
    </form>
  </div>
</div>

<script>
function getCsrf() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      || document.querySelector('input[name="_token"]')?.value;
}

async function openModalContentItem(preselectId = null) {
  const modal = document.getElementById('modalCreateContentItem');
  modal.style.display = 'flex';

  try {
    await loadContentPagesForSelect(preselectId);
  } catch (e) {
    console.error('Error cargando páginas:', e);
    Toast.error('No se pudieron cargar las páginas. Intenta nuevamente.');
  }
}

function closeModalContentItem() {
  document.getElementById('modalCreateContentItem').style.display = 'none';
}

async function loadContentPagesForSelect(preselectId = null) {
  const select = document.getElementById('content_page_id');
  if (!select) return;

  select.innerHTML = '<option value="" disabled selected>Cargando...</option>';

  const url = "{{ route('dashboard.contentpages.options') }}";
  const resp = await fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } });
  const data = await resp.json().catch(() => ({}));

  const pages = Array.isArray(data.pages) ? data.pages : [];
  if (pages.length === 0) {
    select.innerHTML = '<option value="" disabled selected>No hay páginas activas</option>';
    return;
  }

  select.innerHTML = '<option value="" disabled selected>Selecciona una página</option>';
  pages.forEach(p => {
    const opt = document.createElement('option');
    opt.value = p.id;
    opt.textContent = p.text + (p.slug ? ` (${p.slug})` : '');
    if (preselectId && String(preselectId) === String(p.id)) {
      opt.selected = true;
    }
    select.appendChild(opt);
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById("createContentItemForm");
  if (!form) return;

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch("{{ route('dashboard.contentitems.store') }}", {
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
        console.error("Errores de validación:", body);
        const msg = body.errors
          ? Object.values(body.errors || {}).flat().join("\n")
          : 'Hay errores de validación en el formulario.';
        Toast.error(msg, { title: 'Validación' });
      } else if (status >= 500) {
        console.error("Error del servidor:", body);
        Toast.error('Error interno del servidor. Revisa la consola.');
      } else if (status >= 200 && status < 300) {
        Toast.success(body.message || 'Item creado exitosamente.');
        closeModalContentItem();
        setTimeout(() => location.reload(), 900);
      } else {
        console.error("Respuesta inesperada:", body);
        Toast.error(body.message || 'No se pudo crear el item.');
      }
    })
    .catch(error => {
      console.error("Error inesperado:", error);
      Toast.error('Hubo un problema al crear el item.');
    });
  });
});
</script>