<div id="modalCreateSeries" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalCreateSeries()">&times;</span>
        <h2>Crear Serie</h2>

        <form id="createSeriesForm">
            @csrf

            <label for="series_name">Nombre:</label>
            <input type="text" id="series_name" name="name" required>

            <label for="series_department">Dependencia / Oficina:</label>
            <select id="series_department" name="dependency_id" required>
              <option value="" selected disabled>Seleccione una opción...</option>
            </select>

            <label for="series_prefix_base">Prefijo:</label>
            <div class="prefix-wrapper">
                <input type="text"
                      id="series_prefix_base"
                      placeholder="Se autocompleta…"
                      readonly>

                <span class="prefix-sep">-</span>

                <input type="text"
                      id="series_prefix_extra"
                      placeholder="Opcional, ej: EXT">
            </div>

            <input type="hidden" id="series_prefix" name="prefix" required>

            <label for="series_description">Descripción:</label>
            <textarea id="series_description" name="description" rows="3"></textarea>

            <label for="series_is_active">Estado:</label>
            <select id="series_is_active" name="is_active">
                <option value="1" selected>Activa</option>
                <option value="0">Inactiva</option>
            </select>

            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>

<script>
function openModalCreateSeries() {
  document.getElementById('modalCreateSeries').style.display = 'flex';
}

function closeModalCreateSeries() {
  document.getElementById('modalCreateSeries').style.display = 'none';
}

function updateSeriesPrefix() {
    const baseInput   = document.getElementById('series_prefix_base');
    const extraInput  = document.getElementById('series_prefix_extra');
    const hiddenInput = document.getElementById('series_prefix');

    if (!baseInput || !hiddenInput) return;

    const base  = (baseInput.value || '').trim();
    const extra = (extraInput?.value || '').trim();

    let full = base;
    if (base && extra) {
        full = `${base}-${extra}`;
    } else if (!base && extra) {
        full = extra;
    }

    hiddenInput.value = full;
}

document.addEventListener("DOMContentLoaded", function () {
    const select       = document.getElementById("series_department");
    const prefixBase   = document.getElementById('series_prefix_base');
    const prefixExtra  = document.getElementById('series_prefix_extra');

    fetch("/dashboard/dependencies/all")
        .then(response => response.json())
        .then(dependencies => {
            select.innerHTML = '<option value="" selected disabled>Seleccione una opción...</option>';

            dependencies.forEach(dep => {
                const option = document.createElement("option");
                option.value = dep.id;
                option.text  = dep.name;
                option.dataset.shortname = dep.shortname ?? dep.short_name ?? '';

                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error("Error cargando dependencias:", error);
        });

    select.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const shortname = opt.dataset.shortname || '';
        prefixBase.value = shortname;
        updateSeriesPrefix();
    });

    if (prefixExtra) {
        prefixExtra.addEventListener('input', updateSeriesPrefix);
    }

    const createSeriesForm = document.getElementById('createSeriesForm');
    if (createSeriesForm) {
        createSeriesForm.addEventListener('submit', function (e) {
            e.preventDefault();
            updateSeriesPrefix();
            const formData = new FormData(this);

            fetch("{{ route('dashboard.series.store') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrf()
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
                    Toast.success(body.message || 'Serie creada correctamente.');
                    closeModalCreateSeries();
                    setTimeout(() => location.reload(), 900);
                } else {
                    console.error('Error:', body);
                    Toast.error(body.message || 'No se pudo crear la serie.');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                Toast.error('No se pudo crear la serie.');
            });
        });
    }
});
</script>

<style>
.prefix-wrapper {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 0.5rem;
    align-items: center;
}

.prefix-wrapper input {
    width: 100%;
}

.prefix-sep {
    text-align: center;
    font-weight: bold;
}
</style>