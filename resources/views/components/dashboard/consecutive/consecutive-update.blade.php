<div id="modalEditConsecutive" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalEditConsecutive()">&times;</span>
        <h2>Editar Consecutivo</h2>

        <form id="editConsecutiveForm" enctype="multipart/form-data">
            @csrf
            {{-- Importante: Method Spoofing se maneja en el JS para compatibilidad con FormData --}}
            <input type="hidden" id="edit_consecutive_id" name="id">

            <div style="margin-bottom: 10px;">
                <label for="edit_consecutive_full">Consecutivo:</label>
                <input type="text" id="edit_consecutive_full" readonly>
            </div>
            
            <label for="edit_consecutive_series_id">Serie:</label>
            <select id="edit_consecutive_series_id" name="series_id" disabled>
                @foreach($series as $serie)
                    <option value="{{ $serie->id }}">
                        {{ $serie->prefix }} - {{ $serie->name }}
                    </option>
                @endforeach
            </select>

            <label for="edit_consecutive_document_type">Tipo de documento:</label>
            <input type="text" id="edit_consecutive_document_type" name="document_type">

            <label for="edit_consecutive_subject">Asunto:</label>
            <textarea id="edit_consecutive_subject" name="subject" rows="3" required></textarea>

            {{-- START: Campo Destinatario con Autocomplete --}}
            <div class="form-group" style="position: relative; margin-top: 10px;">
                <label for="edit_consecutive_recipient">Destinatario * (Buscar para cambiar):</label>
                <input type="text" 
                       id="edit_consecutive_recipient" 
                       name="recipient" 
                       placeholder="Escriba para buscar y cambiar la persona..." 
                       autocomplete="off" required>
                {{-- ID Oculto de la Persona --}}
                <input type="hidden" id="edit_consecutive_person_id" name="person_id">
                <ul id="edit_recipient_suggestions" class="autocomplete-list" style="display: none;"></ul>
            </div>
            {{-- END: Campo Destinatario con Autocomplete --}}

            <label for="edit_consecutive_internal_reference">Referencia interna:</label>
            <input type="text" id="edit_consecutive_internal_reference" name="internal_reference">

            <label for="edit_consecutive_attachment">Adjunto (opcional):</label>
            <input type="file" id="edit_consecutive_attachment" name="attachment_url">

            <label for="edit_consecutive_notes">Notas:</label>
            <textarea id="edit_consecutive_notes" name="notes" rows="3"></textarea>

            <button type="submit" class="btn-submit">Actualizar</button>
        </form>
    </div>
</div>

<style>
    /* Estilo mínimo para el autocomplete, basado en tu modal de crear */
    .autocomplete-list {
        position: absolute; background: white; border: 1px solid #ccc; width: 100%; max-height: 200px;
        overflow-y: auto; list-style: none; padding: 0; margin: 0; z-index: 1000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0 0 4px 4px; top: 100%;
    }
    .autocomplete-list li { padding: 10px; cursor: pointer; border-bottom: 1px solid #eee; }
    .autocomplete-list li:hover { background-color: #f0f8ff; }
</style>

<script>
// --- DOM REFERENCES ---
let editSearchTimeout = null;
const editInputRecipient = document.getElementById('edit_consecutive_recipient');
const editListSuggestions = document.getElementById('edit_recipient_suggestions');
const editInputPersonId = document.getElementById('edit_consecutive_person_id');

// --- MODAL FUNCTIONS ---
function openModalEditConsecutive(id) {
    // 1. Fetch data from the controller (which now loads 'person')
    fetch(`/dashboard/consecutives/${id}/edit`)
      .then(res => res.json())
      .then(data => {
        // Manejar error si el consecutivo está anulado o no se encuentra
        if (data.message) {
             Toast.error(data.message);
             return;
        }

        document.getElementById('edit_consecutive_id').value               = data.id;
        document.getElementById('edit_consecutive_full').value             = data.full_consecutive || '';
        document.getElementById('edit_consecutive_subject').value          = data.subject || '';
        document.getElementById('edit_consecutive_document_type').value    = data.document_type || '';
        document.getElementById('edit_consecutive_internal_reference').value = data.internal_reference || '';
        document.getElementById('edit_consecutive_notes').value            = data.notes || '';

        // 2. Cargar datos de la Persona/Destinatario
        if (data.person_id && data.person) {
            editInputPersonId.value = data.person_id;
            // Usar fullname o company_name como respaldo para mostrar
            const name = data.person.fullname || data.person.company_name || data.recipient;
            editInputRecipient.value = name;
        } else {
            // Caso para datos antiguos sin person_id o persona borrada
            editInputPersonId.value = '';
            editInputRecipient.value = data.recipient || '';
        }
        
        // 3. Cargar Select de Serie
        const sel = document.getElementById('edit_consecutive_series_id');
        if (sel && data.series_id) {
          sel.value = String(data.series_id);
        }

        document.getElementById('modalEditConsecutive').style.display = 'flex';
      })
      .catch(err => {
        console.error('Error al cargar consecutivo:', err);
        Toast.error('No se pudo cargar la información del consecutivo.');
      });
}

function closeModalEditConsecutive() {
  document.getElementById('modalEditConsecutive').style.display = 'none';
}

// --- AUTOCOMPLETE LOGIC FOR EDIT ---
if(editInputRecipient) {
    editInputRecipient.addEventListener('input', function() {
        const query = this.value;
        clearTimeout(editSearchTimeout);
        
        // Si el texto cambia, se desvincula la persona actual hasta que se seleccione una nueva
        editInputPersonId.value = ''; 

        if (query.length < 2) {
            editListSuggestions.style.display = 'none';
            return;
        }

        editSearchTimeout = setTimeout(() => {
            fetch(`{{ url('dashboard/persons/search') }}?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    editListSuggestions.innerHTML = '';
                    
                    if (data.length > 0) {
                        data.forEach(person => {
                            const li = document.createElement('li');
                            
                            // Usar fullname o company_name como respaldo
                            const nameToShow = person.fullname || person.company_name || 'Nombre no disponible';
                            const docInfo = person.document_number ? ` (${person.document_number})` : '';
                            
                            li.textContent = nameToShow + docInfo;
                            
                            li.onclick = () => selectEditPerson(person);
                            editListSuggestions.appendChild(li);
                        });
                        editListSuggestions.style.display = 'block';
                    } else {
                         // Mensaje si no hay resultados
                         editListSuggestions.innerHTML = `<li style="text-align:center; color:#787878;">No se encontraron personas.</li>`;
                         editListSuggestions.style.display = 'block';
                    }
                });
        }, 300);
    });

    // Cerrar al hacer click fuera
    document.addEventListener('click', (e) => {
        if (e.target !== editInputRecipient) editListSuggestions.style.display = 'none';
    });
}

function selectEditPerson(person) {
    // Rellenar el campo visible
    const selectedName = person.fullname || person.company_name; 
    editInputRecipient.value = selectedName; 
    
    // Rellenar el ID oculto
    editInputPersonId.value = person.id;
    editListSuggestions.style.display = 'none';
}


// --- SUBMIT LISTENER ---
document.addEventListener('DOMContentLoaded', () => {
    const editConsecutiveForm = document.getElementById('editConsecutiveForm');
    if (editConsecutiveForm) {
        editConsecutiveForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const id = document.getElementById('edit_consecutive_id').value;
            const formData = new FormData(this);
            
            // --- CRITICAL: Laravel Method Spoofing ---
            formData.append('_method', 'PUT'); 

            fetch(`/dashboard/consecutives/${id}`, {
                method: 'POST', // Sent as POST, interpreted as PUT by Laravel
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrf(),
                    'Accept': 'application/json'
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
                    Toast.success(body.message || 'Consecutivo actualizado.');
                    closeModalEditConsecutive();
                    setTimeout(() => location.reload(), 900);
                } else {
                    console.error('Error:', body);
                    Toast.error(body.message || 'No se pudo actualizar el consecutivo.');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                Toast.error('No se pudo actualizar el consecutivo.');
            });
        });
    }
});
</script>