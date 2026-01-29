<div id="modalEditContentItem" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <span class="close-modal" onclick="closeModalEditContentItem()">&times;</span>
    <h2>Editar Content Item</h2>

    <form id="editContentItemForm" enctype="multipart/form-data">
      @csrf
      @method('POST')
      <input type="hidden" id="edit_item_id" name="id">

      <label for="edit_content_page_id">Página (Content Page):</label>
      <select id="edit_content_page_id" name="content_page_id" required>
        <option value="" disabled selected>Cargando páginas...</option>
      </select>

      <label for="edit_title">Título:</label>
      <input type="text" id="edit_title" name="title" required>

      <label for="edit_ordering">Orden:</label>
      <input type="number" id="edit_ordering" name="ordering" min="0" value="0">

      <label for="edit_url">URL (opcional):</label>
      <input type="url" id="edit_url" name="url" placeholder="https://...">

      <label for="edit_document">Documento (PDF/DOC/DOCX):</label>
      <input type="file" id="edit_document" name="document" accept=".pdf,.doc,.docx">
      <div id="preview_edit_item_doc_wrap" style="display:none;margin:.5rem 0;">
        Documento actual: <a id="preview_edit_item_doc" href="#" target="_blank" rel="noopener">Ver/Descargar</a>
      </div>

      <label for="edit_image">Imagen (opcional):</label>
      <input type="file" id="edit_image" name="image" accept="image/*">
      <img id="preview_edit_item_image" src="" alt="Imagen actual" style="max-height:150px;display:none;margin-top:10px;border-radius:8px;">

      <label for="edit_description">Descripción (opcional):</label>
      <textarea id="edit_description" name="description" rows="4"></textarea>

      <fieldset>
        <legend>Extra (opcional)</legend>
        <label for="edit_extra_notes">Notas:</label>
        <input type="text" id="edit_extra_notes" name="extra[notes]">

        <label for="edit_extra_tags">Tags (coma-separado):</label>
        <input type="text" id="edit_extra_tags" name="extra[tags]">
      </fieldset>

      <button type="submit" class="btn-submit">Actualizar</button>
    </form>
  </div>
</div>

<script>
function absoluteOrAsset(path) {
  if (!path) return null;
  if (/^https?:\/\//i.test(path) || path.startsWith('/')) return path;
  return `{{ url('') }}/${path}`;
}

async function loadPagesOptionsInto(selectEl, preselectId = null) {
  const url = "{{ route('dashboard.contentpages.options') }}";
  const resp = await fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } });
  const data = await resp.json();
  const pages = Array.isArray(data.pages) ? data.pages : [];

  selectEl.innerHTML = '';
  if (pages.length === 0) {
    selectEl.innerHTML = '<option value="" disabled selected>No hay páginas activas</option>';
    return;
  }

  const ph = document.createElement('option');
  ph.value = '';
  ph.disabled = true;
  ph.selected = !preselectId;
  ph.textContent = 'Selecciona una página';
  selectEl.appendChild(ph);

  pages.forEach(p => {
    const opt = document.createElement('option');
    opt.value = p.id;
    opt.textContent = p.text + (p.slug ? ` (${p.slug})` : '');
    if (preselectId && String(preselectId) === String(p.id)) opt.selected = true;
    selectEl.appendChild(opt);
  });
}

async function openModalEditContentItem(id) {
  try {
    const res = await fetch(`/dashboard/contentitems/${id}/edit`, { headers: { "X-Requested-With": "XMLHttpRequest" }});
    const data = await res.json();

    document.getElementById('edit_item_id').value  = data.id;
    document.getElementById('edit_title').value    = data.title ?? '';
    document.getElementById('edit_ordering').value = (typeof data.ordering !== 'undefined') ? data.ordering : 0;
    document.getElementById('edit_url').value      = data.url ?? '';
    document.getElementById('edit_description').value = data.description ?? '';

    const extra = data.extra || {};
    document.getElementById('edit_extra_notes').value = extra.notes ?? '';
    document.getElementById('edit_extra_tags').value  = extra.tags ?? '';

    await loadPagesOptionsInto(document.getElementById('edit_content_page_id'), data.content_page_id ?? null);

    const docWrap = document.getElementById('preview_edit_item_doc_wrap');
    const docA    = document.getElementById('preview_edit_item_doc');
    if (data.document) {
      const docHref = absoluteOrAsset(data.document);
      if (docHref) {
        docA.href = docHref;
        docA.textContent = docHref.length > 60 ? (docHref.substring(0,57) + '...') : docHref;
        docWrap.style.display = 'block';
      } else {
        docWrap.style.display = 'none';
      }
    } else {
      docWrap.style.display = 'none';
    }

    const img = document.getElementById('preview_edit_item_image');
    if (data.image) {
      const src = absoluteOrAsset(data.image);
      if (src) {
        img.src = src;
        img.style.display = 'block';
      } else {
        img.style.display = 'none';
      }
    } else {
      img.style.display = 'none';
    }

    document.getElementById('modalEditContentItem').style.display = 'flex';
  } catch (e) {
    console.error("Error al cargar Content Item:", e);
    Toast.error("No se pudo cargar la información del item.");
  }
}

function closeModalEditContentItem() {
  document.getElementById('modalEditContentItem').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('editContentItemForm');

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    const id = document.getElementById('edit_item_id').value;
    const formData = new FormData(this);

    fetch(`/dashboard/contentitems/${id}`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrf(),
        'X-HTTP-Method-Override': 'PUT'
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
        Toast.success(body.message || "Item actualizado.");
        closeModalEditContentItem();
        setTimeout(() => location.reload(), 900);
      } else {
        console.error("Error inesperado:", body);
        Toast.error(body.message || "No se pudo actualizar el item.");
      }
    })
    .catch(err => {
      console.error("Error:", err);
      Toast.error("No se pudo actualizar el item.");
    });
  });
});
</script>