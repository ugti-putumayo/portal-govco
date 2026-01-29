<div id="modalCreateConsecutive" class="modal-overlay" style="display:none;">
    <div class="modal-content" style="max-width: 700px;">
        <span class="close-modal" onclick="closeModalCreateConsecutive()">&times;</span>
        <h2>Crear Consecutivo</h2>

        <form id="createConsecutiveForm" enctype="multipart/form-data">
            @csrf

            {{-- 1. CAMPOS DEL CONSECUTIVO (SERIE Y TIPO) --}}
            <div class="grid-2-col">
                <div>
                    <label for="consecutive_series_id">Serie *:</label>
                    <select id="consecutive_series_id" name="series_id" required class="form-control">
                        <option value="">Seleccione una serie</option>
                        @foreach($series as $serie)
                            <option value="{{ $serie->id }}">
                                {{ $serie->prefix }} - {{ $serie->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="consecutive_document_type">Tipo Documento:</label>
                    <input type="text" id="consecutive_document_type" name="document_type" class="form-control">
                </div>
            </div>

            <label for="consecutive_subject">Asunto *:</label>
            <textarea id="consecutive_subject" name="subject" rows="2" required class="form-control"></textarea>

            {{-- 2. SECCIÓN DESTINATARIO (BUSCADOR Y CREACIÓN) --}}
            <div class="form-group" style="position: relative; margin-top: 15px; margin-bottom: 15px;">
                <label for="consecutive_recipient">Destinatario *</label>
                
                <div id="search_person_wrapper">
                    <input type="text" 
                           id="consecutive_recipient" 
                           name="recipient" 
                           class="form-control" 
                           placeholder="Buscar persona o empresa..." 
                           autocomplete="off" required>
                    <input type="hidden" id="consecutive_person_id" name="person_id">
                    <ul id="recipient_suggestions" class="autocomplete-list" style="display: none;"></ul>
                </div>

                {{-- 3. SECCIÓN DESPLEGABLE: NUEVA PERSONA --}}
                <div id="new_person_section" style="display: none; background: #f9fafb; padding: 15px; border: 1px solid #e5e7eb; border-radius: 6px; margin-top: 5px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h4 style="margin:0; color: var(--govco-primary-color);">Nuevo Destinatario</h4>
                        <button type="button" class="btn-cancel" onclick="toggleNewPersonMode(false)" style="font-size: 0.8rem; padding: 4px 8px;">Cancelar / Buscar</button>
                    </div>

                    {{-- Campos de Persona --}}
                    <div class="grid-2-col">
                        <div>
                            <label>Tipo:</label>
                            <select id="new_person_type" class="form-control">
                                <option value="Natural">Natural</option>
                                <option value="Juridica">Jurídica</option>
                            </select>
                        </div>
                        <div>
                            <label>Documento / NIT:</label>
                            <input type="text" id="new_person_document" class="form-control">
                        </div>
                    </div>

                    <label>Nombre Completo / Razón Social *:</label>
                    <input type="text" id="new_person_fullname" class="form-control">

                    <div class="grid-2-col">
                        <div>
                            <label>Email:</label>
                            <input type="email" id="new_person_email" class="form-control">
                        </div>
                        <div>
                            <label>Teléfono:</label>
                            <input type="text" id="new_person_phone" class="form-control">
                        </div>
                    </div>

                    {{-- SELECTS DE UBICACIÓN --}}
                    <div class="grid-2-col">
                        <div>
                            <label>Departamento:</label>
                            <select id="new_person_department" class="form-control">
                                <option value="" disabled selected>Cargando...</option>
                            </select>
                        </div>
                        <div>
                            <label>Ciudad:</label>
                            <select id="new_person_city" class="form-control">
                                <option value="" disabled selected>Seleccione depto...</option>
                            </select>
                        </div>
                    </div>
                    
                    <label>Dirección:</label>
                    <input type="text" id="new_person_address" class="form-control">
                </div>
            </div>

            {{-- 4. OTROS CAMPOS DEL CONSECUTIVO --}}
            <div class="grid-2-col">
                <div>
                    <label for="consecutive_internal_reference">Ref. Interna:</label>
                    <input type="text" id="consecutive_internal_reference" name="internal_reference" class="form-control">
                </div>
                <div>
                    <label for="consecutive_attachment">Adjunto:</label>
                    <input type="file" id="consecutive_attachment" name="attachment_url" class="form-control">
                </div>
            </div>

            <label for="consecutive_notes">Notas:</label>
            <textarea id="consecutive_notes" name="notes" rows="2" class="form-control"></textarea>

            <div class="modal-footer" style="margin-top: 20px; text-align: right;">
                <button type="submit" class="btn-submit" id="btnSaveConsecutive">Generar consecutivo</button>
            </div>
        </form>
    </div>
</div>

<style>
    .grid-2-col { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px; }
    .form-control { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
    .autocomplete-list {
        position: absolute; background: white; border: 1px solid #ccc; width: 100%; max-height: 200px;
        overflow-y: auto; list-style: none; padding: 0; margin: 0; z-index: 1000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0 0 4px 4px; top: 100%;
    }
    .autocomplete-list li { padding: 10px; cursor: pointer; border-bottom: 1px solid #eee; }
    .autocomplete-list li:hover { background-color: #f0f8ff; }
    .autocomplete-list li.add-new { background-color: #e6fffa; color: #047857; font-weight: bold; text-align: center; }
    .autocomplete-list li.add-new:hover { background-color: #ccfbf1; }
</style>

<script>
// --- ESTADO GLOBAL ---
let isNewPersonMode = false;
let searchTimeout = null;

// Referencias DOM
const modalCreate = document.getElementById('modalCreateConsecutive');
const inputRecipient = document.getElementById('consecutive_recipient');
const listSuggestions = document.getElementById('recipient_suggestions');
const inputPersonId = document.getElementById('consecutive_person_id');
const sectionNewPerson = document.getElementById('new_person_section');
const wrapperSearch = document.getElementById('search_person_wrapper');
const deptSelect = document.getElementById('new_person_department');
const citySelect = document.getElementById('new_person_city');

// --- FUNCIONES MODAL ---
function openModalCreateConsecutive() {
    modalCreate.style.display = 'flex';
    loadDepartments();
}

function closeModalCreateConsecutive() {
    modalCreate.style.display = 'none';
    toggleNewPersonMode(false);
    document.getElementById('createConsecutiveForm').reset();
}

// --- LÓGICA AUTOCOMPLETE ---
if(inputRecipient) {
    inputRecipient.addEventListener('input', function() {
        const query = this.value;
        clearTimeout(searchTimeout);
        inputPersonId.value = ''; 

        if (query.length < 2) {
            listSuggestions.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ url('dashboard/persons/search') }}?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    listSuggestions.innerHTML = '';
                    
                    // Resultados existentes
                    if (data.length > 0) {
                        data.forEach(person => {
                            const li = document.createElement('li');
                            
                            // *** CAMBIO CRÍTICO AQUÍ ***
                            // Usamos fullname o company_name como respaldo
                            const nameToShow = person.fullname || person.company_name || 'Nombre no disponible';
                            const docInfo = person.document_number ? ` (${person.document_number})` : '';
                            
                            li.textContent = nameToShow + docInfo;
                            // ****************************
                            
                            li.onclick = () => selectPerson(person);
                            listSuggestions.appendChild(li);
                        });
                    }

                    // Opción "Agregar Nuevo"
                    const liAdd = document.createElement('li');
                    liAdd.className = 'add-new';
                    liAdd.innerHTML = `+ Crear nuevo: "<strong>${query}</strong>"`;
                    liAdd.onclick = () => toggleNewPersonMode(true, query);
                    listSuggestions.appendChild(liAdd);

                    listSuggestions.style.display = 'block';
                });
        }, 300);
    });

    // Cerrar al hacer click fuera
    document.addEventListener('click', (e) => {
        if (!wrapperSearch.contains(e.target)) listSuggestions.style.display = 'none';
    });
}

function selectPerson(person) {
    // *** CAMBIO CRÍTICO AQUÍ ***
    // Aseguramos que el campo visible siempre tenga un nombre
    const selectedName = person.fullname || person.company_name; 
                         
    inputRecipient.value = selectedName; 
    // ****************************
    
    inputPersonId.value = person.id;
    listSuggestions.style.display = 'none';
}

// --- LOGICA SECCIÓN NUEVA PERSONA ---
function toggleNewPersonMode(show, initialName = '') {
    isNewPersonMode = show;
    
    if (show) {
        inputRecipient.readOnly = true; 
        inputRecipient.value = "Creando nuevo destinatario...";
        listSuggestions.style.display = 'none';
        
        sectionNewPerson.style.display = 'block';
        document.getElementById('new_person_fullname').value = initialName;
        document.getElementById('new_person_document').focus();
    } else {
        inputRecipient.readOnly = false;
        inputRecipient.value = '';
        inputPersonId.value = '';
        sectionNewPerson.style.display = 'none';
        
        // Limpiar campos nueva persona
        document.getElementById('new_person_fullname').value = '';
        document.getElementById('new_person_document').value = '';
        document.getElementById('new_person_email').value = '';
        document.getElementById('new_person_phone').value = '';
        document.getElementById('new_person_address').value = '';
        document.getElementById('new_person_type').value = 'Natural';
        deptSelect.value = '';
        citySelect.innerHTML = '<option value="" disabled selected>Seleccione depto...</option>';
    }
}

// --- LOGICA DEPARTAMENTOS Y CIUDADES ---
function loadDepartments() {
    if (deptSelect.options.length > 1) return; 

    fetch("{{ route('locates') }}")
        .then(res => res.json())
        .then(data => {
            deptSelect.innerHTML = '<option value="" disabled selected>Seleccione un departamento...</option>';
            data.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.id;
                option.textContent = dept.name;
                deptSelect.appendChild(option);
            });
        })
        .catch(err => console.error("Error cargando deptos:", err));
}

if(deptSelect) {
    deptSelect.addEventListener('change', function() {
        const departmentId = this.value;
        
        // Limpiar y mostrar estado de carga
        citySelect.innerHTML = '<option value="" disabled selected>Cargando municipios...</option>';

        if (!departmentId) {
            citySelect.innerHTML = '<option value="" disabled selected>Seleccione un departamento</option>';
            return;
        }

        // Llamada fetch a tu endpoint
        fetch(`/locates/cities/${departmentId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar ciudades');
                }
                return response.json();
            })
            .then(cities => {
                citySelect.innerHTML = '<option value="" disabled selected>Seleccione un municipio</option>';
                cities.forEach(city => {
                    const option = document.createElement("option");
                    option.value = city.id;
                    option.textContent = city.name;
                    citySelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error("Error al cargar las ciudades:", error);
                citySelect.innerHTML = '<option value="" disabled selected>Error de carga</option>';
            });
    });
}

// --- PROCESO DE GUARDADO (EL CEREBRO) ---
document.getElementById('createConsecutiveForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btnSubmit = document.getElementById('btnSaveConsecutive');
    const originalBtnText = btnSubmit.innerText;
    btnSubmit.disabled = true;
    btnSubmit.innerText = 'Procesando...';

    try {
        // PASO 1: Si está en modo "Nueva Persona", crearla primero
        if (isNewPersonMode) {
            btnSubmit.innerText = 'Guardando Persona...';
            
            const personData = new FormData();
            personData.append('type_person', document.getElementById('new_person_type').value);
            personData.append('document_number', document.getElementById('new_person_document').value);
            personData.append('fullname', document.getElementById('new_person_fullname').value);
            personData.append('email', document.getElementById('new_person_email').value);
            personData.append('phone', document.getElementById('new_person_phone').value);
            personData.append('address', document.getElementById('new_person_address').value);
            
            if(deptSelect.value) personData.append('department_id', deptSelect.value);
            if(citySelect.value) personData.append('city_id', citySelect.value);

            const personRes = await fetch("{{ route('persons.store') }}", {
                method: 'POST',
                body: personData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const personResult = await personRes.json();

            if (!personResult.success) {
                // Si hay errores de validación, mostrarlos
                 const msg = personResult.errors ? Object.values(personResult.errors).flat().join("\n") : 'Error al crear la persona.';
                throw new Error(msg);
            }

            // Éxito creando persona -> Asignar ID al form principal
            inputPersonId.value = personResult.person.id;
            // Actualizar el input visible con el nombre real guardado (fullname o company_name)
            inputRecipient.value = personResult.person.fullname || personResult.person.company_name; 
        }

        // PASO 2: Crear el Consecutivo
        btnSubmit.innerText = 'Generando Consecutivo...';
        
        if (!inputPersonId.value) {
            throw new Error('Debe seleccionar o crear un destinatario válido.');
        }
        
        const consecutiveData = new FormData(this);
        consecutiveData.set('person_id', inputPersonId.value);
        consecutiveData.set('recipient', inputRecipient.value); // Usamos el nombre que quedó en el input

        const consecutiveRes = await fetch("{{ route('dashboard.consecutives.store') }}", {
            method: 'POST',
            body: consecutiveData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await consecutiveRes.json();

        if (consecutiveRes.status >= 200 && consecutiveRes.status < 300) {
            Toast.success(result.message || 'Consecutivo generado correctamente.');
            closeModalCreateConsecutive();
            setTimeout(() => location.reload(), 900);
        } else if (consecutiveRes.status === 422) {
            const msg = result.errors ? Object.values(result.errors).flat().join("\n") : 'Error de validación.';
            Toast.error(msg);
        } else {
            throw new Error(result.message || 'Error en el servidor.');
        }

    } catch (error) {
        console.error(error);
        Toast.error(error.message || 'Ocurrió un error inesperado.');
    } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerText = originalBtnText;
    }
});
</script>