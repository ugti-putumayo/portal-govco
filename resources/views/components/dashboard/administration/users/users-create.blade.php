<div id="modalCreateArea" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h2>Agregar Nuevo Usuario</h2>
        <form id="createUserForm">
            @csrf

            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" required>
                <button type="button"
                        class="password-toggle"
                        onclick="togglePassword('password')"
                        title="Ver/Ocultar contraseña">
                    <img src="{{ asset('icon/eye.svg') }}" alt="Ver" />
                </button>
            </div>

            <label for="password_confirmation">Confirmar contraseña:</label>
            <div class="password-wrapper">
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                <button type="button"
                        class="password-toggle"
                        onclick="togglePassword('password_confirmation')"
                        title="Ver/Ocultar contraseña">
                    <img src="{{ asset('icon/eye.svg') }}" alt="Ver" />
                </button>
            </div>

            <label for="dependency_id">Dependencia / Departamento:</label>
            <select id="dependency_id" name="dependency_id">
                <option value="" selected>Sin asignar</option>
            </select>

            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>

<script>
function openModal(){
  document.getElementById('modalCreateArea').style.display='flex';
}
function closeModal(){
  document.getElementById('modalCreateArea').style.display='none';
}

function togglePassword(fieldId) {
  const input = document.getElementById(fieldId);
  if (!input) return;
  input.type = input.type === 'password' ? 'text' : 'password';
}

document.addEventListener("DOMContentLoaded", function () {
    fetch("/dashboard/dependencies/all")
        .then(response => response.json())
        .then(dependencies => {
            const select = document.getElementById("dependency_id");
            if (!select) return;

            select.innerHTML = '<option value="" selected>Sin asignar</option>';

            dependencies.forEach(dep => {
                const option = document.createElement("option");
                option.value = dep.id;
                option.text  = dep.name;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error("Error cargando dependencias:", error);
        });

    const searchInput  = document.getElementById('search-input');
    const filterSelect = document.getElementById('filter-category');
    const createForm   = document.getElementById('createUserForm');
    const editForm     = document.getElementById('editAreaForm');

    if(searchInput && filterSelect){
        searchInput.addEventListener("keyup",function(){
            if(filterSelect.value===""){ searchAreas(); }
        });
        const btn = document.querySelector(".search-btn");
        if(btn) btn.addEventListener("click", searchAreas);
    }

    if(createForm){
        createForm.addEventListener("submit",async function(e){
            e.preventDefault();
            const formData = new FormData(createForm);

            try{
                const resp = await fetch("{{ route('dashboard.users.store') }}",{
                    method:"POST",
                    body:formData,
                    headers:{
                        "X-Requested-With":"XMLHttpRequest",
                        "X-CSRF-TOKEN":getCsrf()
                    }
                });

                const data = await resp.json().catch(() => ({}));

                if(resp.status === 422){
                    const msg = data.errors
                        ? Object.values(data.errors).flat().join("\n")
                        : "Hay errores de validación.";
                    Toast.error(msg, { title: "Validación" });
                    return;
                }

                if(!resp.ok){
                    console.error("Error:", data);
                    Toast.error(data.message || "Error al crear el usuario.");
                    return;
                }

                Toast.success(data.message || "Usuario creado con éxito.");
                closeModal();
                setTimeout(() => location.reload(), 900);
            }catch(err){
                console.error(err);
                Toast.error("Hubo un problema al crear el usuario.");
            }
        });
    }

    if(editForm){
        editForm.addEventListener("submit",async function(e){
            e.preventDefault();
            const formData = new FormData(editForm);
            const userId   = document.getElementById('edit_id').value;

            try{
                const resp = await fetch(`/dashboard/users/${userId}`,{
                    method:"POST",
                    body:formData,
                    headers:{
                        "X-Requested-With":"XMLHttpRequest",
                        "X-CSRF-TOKEN":getCsrf(),
                        "X-HTTP-Method-Override": "PUT"
                    }
                });

                const data = await resp.json().catch(() => ({}));

                if(resp.status === 422){
                    const msg = data.errors
                        ? Object.values(data.errors).flat().join("\n")
                        : "Hay errores de validación.";
                    Toast.error(msg, { title: "Validación" });
                    return;
                }

                if(!resp.ok){
                    console.error("Error:", data);
                    Toast.error(data.message || "Hubo un problema al actualizar el usuario.");
                    return;
                }

                Toast.success("Usuario actualizado con éxito");
                closeEditModal();
                setTimeout(() => location.reload(), 900);
            }catch(_e){
                Toast.error("Hubo un problema al actualizar el usuario.");
            }
        });
    }
});
</script>

<style>
.password-wrapper {
    position: relative;
}

.password-wrapper input {
    width: 100%;
    padding-right: 40px; /* espacio para el ícono */
}

.password-toggle {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    padding: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.password-toggle img {
    width: 18px;
    height: 18px;
}
</style>