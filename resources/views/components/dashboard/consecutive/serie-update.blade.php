<div id="modalEditSeries" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalEditSeries()">&times;</span>
        <h2>Editar Serie</h2>

        <form id="editSeriesForm">
            @csrf
            {{-- No hace falta @method aquí porque usamos X-HTTP-Method-Override en el fetch --}}
            <input type="hidden" id="edit_series_id" name="id">

            <label for="edit_series_name">Nombre:</label>
            <input type="text" id="edit_series_name" name="name" required>

            <label for="edit_series_prefix">Prefijo:</label>
            <input type="text" id="edit_series_prefix" name="prefix" required>

            <label for="edit_series_dependency">Dependencia / Departamento:</label>
            <select id="edit_series_dependency" name="dependency_id" required>
                <option value="" selected disabled>Seleccione una opción...</option>
            </select>

            <label for="edit_series_description">Descripción:</label>
            <textarea id="edit_series_description" name="description" rows="3"></textarea>

            <label for="edit_series_is_active">Estado:</label>
            <select id="edit_series_is_active" name="is_active">
                <option value="1">Activa</option>
                <option value="0">Inactiva</option>
            </select>

            <button type="submit" class="btn-submit">Actualizar</button>
        </form>
    </div>
</div>

<script>
function closeModalEditSeries() {
  document.getElementById('modalEditSeries').style.display = 'none';
}

function loadDependenciesIntoEditSelect(selectedId = null) {
  const select = document.getElementById("edit_series_dependency");
  if (!select) return;

  // Si ya se cargaron antes, solo seleccionamos el valor
  if (select.dataset.loaded === '1') {
    if (selectedId) select.value = String(selectedId);
    return;
  }

  fetch("/dashboard/dependencies/all")
    .then(response => response.json())
    .then(dependencies => {
        select.innerHTML = '<option value="" disabled>Seleccione una opción...</option>';

        dependencies.forEach(dep => {
            const option = document.createElement("option");
            option.value = dep.id;
            option.text  = dep.name;
            select.appendChild(option);
        });

        select.dataset.loaded = '1';

        if (selectedId) {
          select.value = String(selectedId);
        }
    })
    .catch(error => {
        console.error("Error cargando dependencias:", error);
        Toast.error("No se pudieron cargar las dependencias.");
    });
}

function openModalEditSeries(id) {
  // Cargar datos de la serie
  fetch(`{{ url('dashboard/consecutives/series') }}/${id}/edit`)
    .then(res => res.json())
    .then(data => {
      document.getElementById('edit_series_id').value          = data.id;
      document.getElementById('edit_series_name').value        = data.name || '';
      document.getElementById('edit_series_prefix').value      = data.prefix || '';
      document.getElementById('edit_series_description').value = data.description || '';
      document.getElementById('edit_series_is_active').value   = data.is_active ? '1' : '0';

      // Cargar dependencias y seleccionar la actual
      loadDependenciesIntoEditSelect(data.dependency_id || null);

      document.getElementById('modalEditSeries').style.display = 'flex';
    })
    .catch(err => {
      console.error('Error al cargar serie:', err);
      Toast.error('No se pudo cargar la información de la serie.');
    });
}

document.addEventListener('DOMContentLoaded', () => {
  const editSeriesForm = document.getElementById('editSeriesForm');
  if (!editSeriesForm) return;

  editSeriesForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const id = document.getElementById('edit_series_id').value;
    const formData = new FormData(this);

    fetch(`{{ url('dashboard/consecutives/series') }}/${id}`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrf(),
        'X-HTTP-Method-Override': 'PUT'
      }
    })
    .then(async res => {
      const body = await res.json().catch(() => ({}));
      return { status: res.status, body };
    })
    .then(({ status, body }) => {
      if (status === 422) {
        const msg = body.errors
          ? Object.values(body.errors).flat().join("\n")
          : 'Hay errores de validación.';
        Toast.error(msg, { title: 'Validación' });
      } else if (status >= 200 && status < 300) {
        Toast.success(body.message || 'Serie actualizada correctamente.');
        closeModalEditSeries();
        setTimeout(() => location.reload(), 900);
      } else {
        console.error('Error:', body);
        Toast.error(body.message || 'No se pudo actualizar la serie.');
      }
    })
    .catch(err => {
      console.error('Error:', err);
      Toast.error('No se pudo actualizar la serie.');
    });
  });
});
</script>