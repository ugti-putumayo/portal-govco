@extends('dashboard.dashboard') 
@push('scripts')
<script>
function getDefaultHeaders() {
    return {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    };
}

document.getElementById('confirm-delete')?.addEventListener('click', () => {
    if (!currentTargetId) return;

    fetch(`/dashboard/mipg/${currentTargetId}`, {
        method: 'DELETE',
        headers: getDefaultHeaders()
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const li = document.querySelector(`li[data-id="${currentTargetId}"]`);
            if (li) {
                li.style.transition = 'opacity 0.3s';
                li.style.opacity = '0';
                setTimeout(() => {
                    const parentUl = li.parentElement;
                    li.remove();
                    if (parentUl && parentUl.children.length === 0) {
                        parentUl.classList.add('d-none');
                    }
                }, 300);
            }
        } else {
            alert(data.message || 'Error al eliminar');
        }
        closeDeleteModal();
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('search-input');
    const filterSelect = document.getElementById('filter-category');
    const searchBtn = document.querySelector('.search-btn');

    function searchFile() {
        const category = filterSelect.value;
        const search = searchInput.value;
        const url = new URL(window.location.href.split('?')[0]);
        if (search) url.searchParams.set('search', search);
        if (category) url.searchParams.set('category', category);
        window.location.href = url.toString();
    }

    searchInput?.addEventListener("keyup", function () {
        if (filterSelect.value === "") {
            searchFile();
        }
    });

    searchBtn?.addEventListener("click", function () {
        searchFile();
    });
});

function toggleFolder(el) {
    const parent = el.parentElement;
    const nested = parent.querySelector(".file-tree");

    if (nested) {
        nested.classList.toggle("d-none");
    }
}

let currentTargetId = null;
let currentTargetType = null;
let currentPath = null;

function showContextMenu(event, elementId, elementType, elementPath) {
    if (event && typeof event.preventDefault === 'function') {
        event.preventDefault();
    } else {
        console.error("El argumento 'event' no es un objeto de evento válido:", event);
        return;
    }

    const menu = document.getElementById('context-menu');
    currentTargetId = elementId;
    currentTargetType = elementType;
    currentPath = elementPath;
    let x = event.pageX;
    let y = event.pageY;
    menu.style.left = x + 'px';
    menu.style.top = y + 'px';
    menu.classList.remove('d-none');
    const uploadOption = menu.querySelector('li[onclick="uploadToFolder()"]');
    if (uploadOption) {
        if (currentTargetType === 'directory') {
            uploadOption.style.display = 'flex';
        } else {
            uploadOption.style.display = 'none';
        }
    }
    const assignOption = menu.querySelector('li[onclick="assignAreaSelected()"]');
    if (assignOption) {
        if (currentTargetType === 'file') {
            assignOption.style.display = 'flex';
        } else {
            assignOption.style.display = 'none';
        }
    }
}

document.addEventListener('contextmenu', function (e) {
    const targetLi = e.target.closest('li[data-id]');
    const fileTreeContainer = e.target.closest('.content-modules');
    const contextMenuItself = e.target.closest('#context-menu');

    if (contextMenuItself) {
        return; 
    }
    if (targetLi) { 
        e.preventDefault();
        const id = targetLi.dataset.id;
        const type = targetLi.dataset.type || (targetLi.querySelector('.directory') ? 'directory' : 'file');
        const path = targetLi.dataset.path || '';
        showContextMenu(e, id, type, path);
    } else if (fileTreeContainer) {
        e.preventDefault();
        showContextMenu(e, null, 'container', '/');
    } else {
        const menu = document.getElementById('context-menu');
        if (menu) {
            menu.classList.add('d-none');
        }
    }
});

document.addEventListener('click', function (e) {
    const menu = document.getElementById('context-menu');
    if (menu && !menu.contains(e.target) && !e.target.closest('.file-tree li')) {
        menu.classList.add('d-none');
    }
});

function renameSelected() {
    document.getElementById('context-menu').classList.add('d-none');
    if (!currentTargetId) {
        alert('Ningún elemento seleccionado para renombrar.');
        return;
    }
    const li = document.querySelector(`li[data-id="${currentTargetId}"]`);
    const fileDiv = li ? (li.querySelector('.file') || li.querySelector('.directory')) : null;
    if (fileDiv) {
        enableRename(fileDiv, currentTargetId);
    } else {
        alert('No se pudo encontrar el elemento para renombrar.');
    }
}

function uploadToFolder() {
    document.getElementById('context-menu').classList.add('d-none');
    if (currentTargetType !== 'directory' || !currentTargetId) {
        alert('Seleccione una carpeta para subir archivos.');
        return;
    }
    alert('Subir archivo a la carpeta ID: ' + currentTargetId + ' (ruta: ' + currentPath + ')');
}

function createNewFolderInline(parentUl, parentId, path) {
    const li = document.createElement('li');
    const div = document.createElement('div');
    div.classList.add('directory');

    const input = document.createElement('input');
    input.type = 'text';
    input.placeholder = 'Nueva carpeta';
    input.className = 'rename-input';

    div.append('📁 ', input);
    li.appendChild(div);
    li.dataset.type = 'directory';

    parentUl.appendChild(li);
    input.focus();

    input.onblur = () => {
        const name = input.value.trim();
        if (!name) {
            li.remove();
            return;
        }

        fetch(`/dashboard/mipg/folder`, {
            method: 'POST',
            headers: {
                ...getDefaultHeaders(),
            },
            body: JSON.stringify({
                name,
                parent_id: parentId,
                path,
                type: 'directory'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                console.log('Respuesta del backend:', data);
                input.remove();
                const span = document.createElement('span');
                span.className = 'filename';
                span.innerText = name;
                div.appendChild(span);

                // Si quieres que sea renombrable al doble click:
                div.setAttribute('ondblclick', `enableRename(this, ${data.folder.id})`);
                li.dataset.id = data.folder.id;
                li.dataset.path = data.folder.path;
            } else {
                alert(data.message || 'Error al crear carpeta');
                li.remove();
            }
        })
        .catch(err => {
            console.error(err);
            li.remove();
        });
    };

    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') input.blur();
        if (e.key === 'Escape') li.remove();
    });
}

function deleteSelected() {
    if (!currentTargetId) return;
    openDeleteModal();
}

function openDeleteModal() {
    document.getElementById('delete-modal')?.classList.remove('d-none');
    document.getElementById('context-menu')?.classList.add('d-none'); 
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('d-none');
}

function createNewFolderSelected() {
    document.getElementById('context-menu').classList.add('d-none');

    let parentUl = document.querySelector('ul.file-tree');

    if (currentTargetType === 'directory' && currentTargetId) {
        const currentLi = document.querySelector(`li[data-id="${currentTargetId}"]`);
        let nestedUl = currentLi?.querySelector('ul.file-tree');

        if (!nestedUl) {
            nestedUl = document.createElement('ul');
            nestedUl.classList.add('file-tree');
            nestedUl.classList.remove('d-none');
            currentLi.appendChild(nestedUl);
        }

        parentUl = nestedUl;
    }

    createNewFolderInline(parentUl, currentTargetId, currentPath);
}

function uploadToFolder() {
    document.getElementById('context-menu')?.classList.add('d-none');

    if (currentTargetType !== 'directory' || !currentTargetId) {
        alert('Seleccione una carpeta para subir archivos.');
        return;
    }

    const fileInput = document.getElementById('upload-file');
    fileInput.click();

    fileInput.onchange = () => {
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('parent_id', currentTargetId);

        fetch('/dashboard/mipg/upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.file) {
                const fileData = data.file;
                const li = document.createElement('li');
                li.dataset.id = fileData.id;
                li.dataset.path = fileData.path;
                li.dataset.type = 'file';

                const div = document.createElement('div');
                div.className = 'file';
                div.setAttribute('ondblclick', `enableRename(this, ${fileData.id})`);

                const ext = fileData.name.split('.').pop().toLowerCase();
                let icon;
                switch (ext) {
                    case 'pdf': icon = '/icon/pdf.png'; break;
                    case 'doc':
                    case 'docx': icon = '/icon/word.png'; break;
                    case 'xls':
                    case 'xlsx': icon = '/icon/excel.png'; break;
                    case 'ppt':
                    case 'pptx': icon = '/icon/powerpoint.png'; break;
                    default: icon = '/icon/default.png';
                }

                const img = document.createElement('img');
                img.src = icon;
                img.style.width = '18px';
                img.style.marginRight = '5px';

                const a = document.createElement('a');
                a.href = `/storage/${fileData.file}`;
                a.target = '_blank';

                const span = document.createElement('span');
                span.className = 'filename';
                span.innerText = fileData.name;

                a.appendChild(span);
                div.appendChild(img);
                div.appendChild(a);
                li.appendChild(div);

                // Insertar en el árbol dentro del directorio
                const parentLi = document.querySelector(`li[data-id="${currentTargetId}"]`);
                let ul = parentLi.querySelector('ul.file-tree');

                if (!ul) {
                    ul = document.createElement('ul');
                    ul.classList.add('file-tree');
                    parentLi.appendChild(ul);
                }

                ul.classList.remove('d-none');
                ul.appendChild(li);
            } else {
                alert(data.message || 'Error al subir archivo.');
            }
        })
        .catch(err => {
            console.error('Error al subir archivo:', err);
            alert('Hubo un problema al subir el archivo.');
        });

        fileInput.value = '';
    };
}

function assignAreaSelected() {
    if (!currentTargetId) return;
    document.getElementById('context-menu')?.classList.add('d-none'); 
    document.getElementById('assign-area-modal').classList.remove('d-none');
}

function closeAssignAreaModal() {
    document.getElementById('assign-area-modal').classList.add('d-none');
}

function confirmAssignArea() {
    const areaId = document.getElementById('area-selector').value;
    if (!areaId) {
        alert("Debe seleccionar un área.");
        return;
    }

    fetch(`/dashboard/mipg/${currentTargetId}/assign-area`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ dependency_id: areaId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Área asignada correctamente.");
        } else {
            alert(data.message || "Error al asignar área.");
        }
        closeAssignAreaModal();
    })
    .catch(err => {
        console.error("Error al asignar área:", err);
        alert("Error inesperado.");
        closeAssignAreaModal();
    });
}

document.addEventListener("DOMContentLoaded", function () {
    fetch("/dashboard/dependencies/all")
        .then(response => response.json())
        .then(dependencies => {
            const select = document.getElementById("area-selector");
            select.innerHTML = `
                <option value="">Seleccione un área</option>
                <option value="0">General</option>
            `;
            dependencies.forEach(dep => {
                const option = document.createElement("option");
                option.value = dep.id;
                option.textContent = dep.name;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error("Error cargando dependencias:", error);
        });
});

let selectedNodeId = null;

document.addEventListener('contextmenu', function (e) {
    const item = e.target.closest('[data-id]');
    if (item) {
        selectedNodeId = item.dataset.id;
    }
});

function toggleVisibilitySelected() {
    document.getElementById('context-menu')?.classList.add('d-none');
    if (!selectedNodeId) return;

    fetch(`/dashboard/mipg/${selectedNodeId}/toggle-visibility`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const badge = document.querySelector(`[data-id="visibility-${selectedNodeId}"]`);
            if (badge) {
                badge.textContent = data.is_visible ? 'Activo' : 'Inactivo';
                badge.classList.toggle('active', data.is_visible);
                badge.classList.toggle('inactive', !data.is_visible);
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error al cambiar el estado.');
    });
}
</script>
@endpush

@section('content')
<div class="container-modules">
    <div class="navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/files-white.svg') }}" class="submenu-icon-area">
            <h2 class="navbar-title">MIPG - Documentos</h2>
        </div>

        <!-- <form method="GET" class="navbar-filters">
            <select id="filter-category" name="category" class="filter-select">
                <option value="">Filtrar por...</option>
                <option value="name" {{ request('category') == 'name' ? 'selected' : '' }}>Archivo</option>
            </select>

            <input type="text" id="search-input" name="search" class="search-box"
                placeholder="Buscar documento..." value="{{ request('search') }}">
            <button type="submit" class="search-btn">🔍</button>
        </form> -->
    </div>

    <input type="file" id="upload-file" class="d-none">

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="chart-grid">
        <div class="chart-box">
            <canvas id="dependencyChart"></canvas>
        </div>
        <div class="chart-box">
            <canvas id="typeSummaryChart"></canvas>
        </div>
    </div>

    <div class="content-modules">
        <ul class="file-tree">
            @foreach ($files as $file)
                @include('dashboard.mipg._node', ['file' => $file])
            @endforeach
        </ul>
    </div>

    <div id="context-menu" class="context-menu d-none">
        <ul>
            <li onclick="createNewFolderSelected()">
                <span class="context-menu-icon">📁</span> Crear nueva carpeta
            </li>
            <li onclick="renameSelected()">
                <span class="context-menu-icon">📝</span> Renombrar
            </li>
            <li onclick="deleteSelected()">
                <span class="context-menu-icon">🗑️</span> Eliminar
            </li>
            <li onclick="uploadToFolder()">
                <span class="context-menu-icon">📤</span> Subir archivo
            </li>
            <li onclick="assignAreaSelected()">
                <span class="context-menu-icon">🏷️</span> Asignar área
            </li>
            <li onclick="toggleVisibilitySelected()">
                <span class="context-menu-icon">👁️</span> Cambiar estado visibilidad
            </li>
        </ul>
    </div>

    <div id="delete-modal" class="modal-overlay-del d-none">
        <div class="modal-content-del">
            <h3>¿Estás seguro de eliminar este elemento?</h3>
            <div class="btns">
                <button id="confirm-delete" class="delete">Eliminar</button>
                <button onclick="closeDeleteModal()" class="cancel">Cancelar</button>
            </div>
        </div>
    </div>

    <div id="assign-area-modal" class="modal-overlay-del d-none">
        <div class="modal-content-del">
            <h3>Asignar área</h3>
                <select id="area-selector" style="width: 100%; padding: 8px; margin-bottom: 1rem;">
                    <option value="">Seleccione un área</option>
                </select>
            <div class="btns">
                <button onclick="confirmAssignArea()" class="delete">Asignar</button>
                <button onclick="closeAssignAreaModal()" class="cancel">Cancelar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function getRandomColor(opacity = 0.6) {
    const r = Math.floor(Math.random() * 156) + 100; // Evita tonos muy oscuros
    const g = Math.floor(Math.random() * 156) + 100;
    const b = Math.floor(Math.random() * 156) + 100;
    return `rgba(${r}, ${g}, ${b}, ${opacity})`;
}

document.addEventListener("DOMContentLoaded", function () {
    fetch("/dashboard/mipg/dependency-summary")
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.name);
            const values = data.map(item => item.total);
            const backgroundColors = values.map(() => getRandomColor(0.6));
            const borderColors = backgroundColors.map(color => color.replace(/0\.6/, '1'));
            const ctx = document.getElementById('dependencyChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total de documentos por área',
                        data: values,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.parsed.y} documento(s)`
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error("Error al cargar resumen de dependencias:", error));
});

document.addEventListener("DOMContentLoaded", function () {
    fetch('/dashboard/mipg/type-summary')
        .then(response => response.json())
        .then(data => {
            console.log(data);
            const labels = data.map(dep => dep.dependency_name);
            const allTypes = [...new Set(data.flatMap(dep => dep.document_types.map(dt => dt.name)))];

            const datasets = allTypes.map(type => {
                return {
                    label: type,
                    data: data.map(dep => {
                        const match = dep.document_types.find(dt => dt.name === type);
                        return match ? match.total : 0;
                    }),
                    backgroundColor: getRandomColor(0.7),
                    borderColor: getRandomColor(1),
                    borderWidth: 1
                };
            });

            const ctx = document.getElementById('typeSummaryChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: { mode: 'index', intersect: false },
                        title: {
                            display: true,
                            text: 'Cantidad de archivos por carpeta y área'
                        },
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            beginAtZero: true
                        },
                        y: {
                            stacked: true
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error("Error al cargar tipo de documentos:", error);
        });
});
</script>
@endpush

<style>
.navbar {
    position: sticky;
    top: 0;
    width: 100%;
    background-color: var(--govco-secondary-color);
    padding: 15px 20px;
    display: flex;
    flex-wrap: wrap; /* si quieres que el buscador baje en pantallas pequeñas */
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
}

.navbar-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.submenu-icon-area {
    width: 30px;
    height: 30px;
    color: white;
}

.container-modules {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100vh;
}

.content-modules {
    width: 100%;
    min-height: 92%;
    overflow-x: auto;
    padding: 10px
}

.navbar-title {
    color: var(--govco-white-color);
    font-family: var(--govco-font-primary);
    font-size: 20px;
    font-weight: bold;
}

/* Contenedor de filtros */
.navbar-filters {
    display: flex;
    gap: 10px;
    align-items: center;
}

/* Estilo del Select */
.filter-select {
    padding: 8px;
    border-radius: var(--govco-border-radius);
    border: 1px solid var(--govco-border-color);
    font-family: var(--govco-font-primary);
}

/* Estilo del Input de Búsqueda */
.search-box {
    padding: 8px;
    border-radius: var(--govco-border-radius);
    border: 1px solid var(--govco-border-color);
    font-family: var(--govco-font-primary);
}

/* Estilo del Botón de Búsqueda */
.search-btn {
    padding: 8px 12px;
    border: none;
    background-color: var(--govco-accent-color);
    color: var(--govco-white-color);
    border-radius: var(--govco-border-radius);
    cursor: pointer;
}

.search-btn:hover {
    background-color: var(--govco-primary-color);
}

.title {
    color: var(--govco-primary-color);
    font-family: var(--govco-font-primary);
    margin-bottom: 20px;
}

/* Alertas */
.alert-success {
    background-color: var(--govco-success-color);
    color: var(--govco-white-color);
    padding: 10px;
    border-radius: var(--govco-border-radius);
    margin-bottom: 15px;
}

.file-tree {
    list-style-type: none;
    font-family: var(--govco-font-primary);
    padding-left: 20px;
}

.file-tree li {
    cursor: pointer;
    margin: 5px 0;
}

.file-tree .file {
    cursor: default;
    padding-left: 20px;
}

.file-tree .directory::before {
    content: none;
    display: inline-block;
    margin-right: 5px;
    transform: rotate(0deg);
    transition: transform 0.2s ease;
}

.file-tree .directory.open::before {
    transform: rotate(90deg);
}

.directory > div:hover {
    background-color: #f0f0f0;
    border-radius: 5px;
    padding: 3px;
}

ul, li {
    list-style: none;      /* elimina puntos o íconos */
    margin: 0;             /* elimina márgenes por defecto */
    padding: 0;            /* elimina indentación */
}

.context-menu {
    position: absolute;
    z-index: 1000;
    background-color: #fdfdfd; /* Slightly off-white like Windows */
    border: 1px solid #cccccc; /* Softer border */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15); /* Subtle shadow */
    border-radius: 6px; /* Rounded corners */
    padding: 5px 0;
    min-width: 200px; /* Minimum width */
    font-family: Segoe UI, sans-serif; /* Windows font */
    font-size: 14px;
}

.context-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.context-menu ul li {
    padding: 8px 15px 8px 12px; /* Adjust padding for icon */
    cursor: pointer;
    display: flex; /* For icon alignment */
    align-items: center;
    color: #333; /* Darker text */
}

.context-menu ul li:hover {
    background-color: #e9e9e9; /* Hover effect like Windows */
}

.context-menu-icon {
    margin-right: 10px; /* Space between icon and text */
    display: inline-block;
    width: 16px; /* Fixed width for alignment */
    text-align: center;
}

.d-none {
    display: none !important;
}

/* confirm delete */
.modal-overlay-del {
    display: flex;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(6px);
    background-color: rgba(0, 0, 0, 0.45);
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    z-index: 9999;
}

.modal-content-del {
    background: #ffffff;
    padding: 2rem;
    border-radius: 12px;
    width: 420px;
    max-width: 90%;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    text-align: center;
    font-family: "Segoe UI", "Helvetica Neue", sans-serif;
    animation: fadeInModal 0.2s ease-out;
}

.modal-content-del h3 {
    font-size: 20px;
    margin-bottom: 1.5rem;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.modal-content-del h3::before {
    content: "⚠️";
    font-size: 24px;
}

.modal-content-del .btns {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-top: 20px;
}

.modal-content-del button {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}

.modal-content-del button.delete {
    background-color: #d9534f;
    color: white;
}

.modal-content-del button.delete:hover {
    background-color: #c9302c;
}

.modal-content-del button.cancel {
    background-color: #6c757d;
    color: white;
}

.modal-content-del button.cancel:hover {
    background-color: #5a6268;
}

@keyframes fadeInModal {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.directory.drag-over {
    outline: 2px dashed #4a90e2;
    background-color: #f0f8ff;
}

.chart-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    padding: 20px;
}

.chart-box {
    background: #fff;
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    height: 400px;
}
</style>