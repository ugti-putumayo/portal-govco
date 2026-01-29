<script>
window.openPermissionModal = function(userId) {
    const container = document.getElementById("permission-module-list");
    container.innerHTML = "<p>Cargando módulos y permisos...</p>";

    document.getElementById("perm_user_id").value = userId;
    document.getElementById("modalAssignPermission").style.display = "flex";

    fetch(`/dashboard/usermodules/user/${userId}/permissions`)
        .then(res => res.json())
        .then(data => {
            container.innerHTML = "";

            const allPermissions = @json($permissions);
            const assignedPermissions = data.permissions || [];

            const grouped = {};
            data.submodules.forEach(sub => {
                if (!grouped[sub.module_id]) {
                    grouped[sub.module_id] = {
                        module_name: sub.module_name,
                        submodules: []
                    };
                }
                grouped[sub.module_id].submodules.push(sub);
            });

            Object.entries(grouped).forEach(([moduleId, { module_name, submodules }], index) => {
                const moduleBlock = document.createElement("div");
                moduleBlock.classList.add("module-block");

                const toggleId = `module-toggle-${index}`;
                const contentId = `module-content-${index}`;

                moduleBlock.innerHTML = `
                    <div class="module-header" onclick="toggleModule('${contentId}', '${toggleId}')">
                        <span class="module-title">${module_name}</span>
                        <span id="${toggleId}" class="toggle-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                <path fill="#004085" d="M17 9.17a1 1 0 0 0-1.41 0L12 12.71L8.46 9.17a1 1 0 0 0-1.41 0a1 1 0 0 0 0 1.42l4.24 4.24a1 1 0 0 0 1.42 0L17 10.59a1 1 0 0 0 0-1.42"/>
                            </svg>
                        </span>
                    </div>
                    <div id="${contentId}" class="module-content" style="display: none;"></div>
                `;

                const contentDiv = moduleBlock.querySelector(`#${contentId}`);

                const isOnlyModule = submodules.length === 1 && parseInt(submodules[0].id) === parseInt(moduleId);

                if (isOnlyModule) {
                    // Renderiza permisos para módulo sin submódulos
                    let permissionOptions = "";
                    allPermissions.forEach(permission => {
                        const isChecked = assignedPermissions.some(
                            p => parseInt(p.module_id) === parseInt(moduleId) &&
                                 !p.submodule_id &&
                                 parseInt(p.permission_id) === parseInt(permission.id)
                        );

                        permissionOptions += `
                            <label>
                                <input type="checkbox"
                                    name="permissions[module_${moduleId}][]"
                                    value="${permission.id}"
                                    ${isChecked ? 'checked' : ''}>
                                ${permission.name}
                            </label>
                        `;
                    });

                    contentDiv.innerHTML += `
                        <div class="submodule-title">${module_name}</div>
                        <div class="permissions-options">${permissionOptions}</div>
                    `;
                } else {
                    // Renderiza submódulos normalmente
                    submodules.forEach(sub => {
                        let permissionOptions = `
                            <label>
                                <input type="checkbox" class="check-all" data-submodule="${sub.id}">
                                <strong>Todos</strong>
                            </label>
                        `;

                        allPermissions.forEach(permission => {
                            const isChecked = assignedPermissions.some(
                                p => parseInt(p.submodule_id) === parseInt(sub.id) &&
                                     parseInt(p.permission_id) === parseInt(permission.id)
                            );

                            permissionOptions += `
                                <label>
                                    <input type="checkbox"
                                        name="permissions[${sub.id}][${permission.id}]"
                                        value="${permission.id}"
                                        class="perm-checkbox"
                                        data-submodule="${sub.id}"
                                        ${isChecked ? 'checked' : ''}>
                                    ${permission.name}
                                </label>
                            `;
                        });

                        const subGroup = document.createElement("div");
                        subGroup.classList.add("submodule-group");

                        subGroup.innerHTML = `
                            <div class="submodule-title">${sub.name}</div>
                            <div class="permissions-options">${permissionOptions}</div>
                            <input type="hidden" name="submodules[]" value="${sub.id}">
                        `;

                        contentDiv.appendChild(subGroup);
                    });
                }

                container.appendChild(moduleBlock);
            });

            // Manejar los check-all por submódulo
            setTimeout(() => {
                const allCheckAlls = document.querySelectorAll(".check-all");

                allCheckAlls.forEach(checkAll => {
                    const subId = checkAll.getAttribute("data-submodule");
                    checkAll.addEventListener("change", function () {
                        const checkboxes = document.querySelectorAll(`.perm-checkbox[data-submodule="${subId}"]`);
                        checkboxes.forEach(cb => {
                            cb.checked = this.checked;
                        });
                    });
                });
            }, 50);
        })
        .catch(err => {
            console.error("Error al cargar permisos:", err);
            container.innerHTML = "<p>Error al cargar permisos.</p>";
        });
};

window.toggleModule = function(contentId, toggleId) {
    const content = document.getElementById(contentId);
    const icon = document.getElementById(toggleId);

    const isOpen = content.style.display === "block";
    content.style.display = isOpen ? "none" : "block";

    icon.innerHTML = isOpen
        ? `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
               <path fill="#004085" d="M17 9.17a1 1 0 0 0-1.41 0L12 12.71L8.46 9.17a1 1 0 0 0-1.41 0a1 1 0 0 0 0 1.42l4.24 4.24a1 1 0 0 0 1.42 0L17 10.59a1 1 0 0 0 0-1.42"/>
           </svg>`
        : `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
               <path fill="#004085" d="m17 13.41l-4.29-4.24a1 1 0 0 0-1.42 0l-4.24 4.24a1 1 0 0 0 0 1.42a1 1 0 0 0 1.41 0L12 11.29l3.54 3.54a1 1 0 0 0 .7.29a1 1 0 0 0 .71-.29a1 1 0 0 0 .05-1.42"/>
           </svg>`;
}

function closePermissionModal() {
    document.getElementById("modalAssignPermission").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("assignPermissionForm");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        console.log("Formulario enviado");

        fetch("{{ route('dashboard.usermodules.sync') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                "X-Requested-With": "XMLHttpRequest"
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.message) {
                alert(data.message);
                closePermissionModal();
            } else {
                alert("Hubo un error al guardar los permisos");
            }
        })
        .catch(err => {
            console.error("Error al guardar permisos:", err);
            alert("Error de red o del servidor.");
        });
    });
});
</script>

<!-- Asignar Permisos -->
<div id="modalAssignPermission" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closePermissionModal()">&times;</span>
        <h3>Asignar Permisos a Usuario</h3>

        <form id="assignPermissionForm">
            @csrf
            <input type="hidden" id="perm_user_id" name="user_id">

            <div id="permission-module-list">
                <!-- Módulos y submódulos con checkboxes de permisos se generan aquí -->
            </div>

            <button type="submit">Guardar Permisos</button>
        </form>
    </div>
</div>

<!-- Estilos internos -->
<style>
#modalAssignPermission .modal-content {
    background: white;
    padding: 20px;
    width: 700px;
    max-height: 90vh;
    overflow-y: auto;
    border-radius: 10px;
    margin: auto;
    position: relative;
}

.module-block {
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 15px;
    background-color: #f9f9f9;
}

.module-header {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: 500;
    margin-bottom: 5px;
}

.toggle-icon {
    margin-right: 8px;
    font-size: 16px;
    user-select: none;
}

.module-title {
    font-size: 16px;
    color: #333;
}

.submodule-group {
    margin-bottom: 10px;
    padding-left: 10px;
}

.submodule-title {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 4px;
    color: #444;
}

.permissions-options {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding-left: 5px;
}

.permissions-options label {
    font-weight: normal;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.permissions-options label:first-child {
    margin-right: 10px;
    font-weight: bold;
    color: #0056b3;
}
</style>