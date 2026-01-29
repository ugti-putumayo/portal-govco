<div id="modalCreateContrac" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalContrac()">&times;</span>
        <h2>Agregar Nuevo Contrato</h2>

        <form id="createContracForm" enctype="multipart/form-data">
            @csrf
            <label for="contract_number">Número de contrato:</label>
            <input type="text" id="contract_number" name="contract_number" required>

            <label for="dependency">Área:</label>
            <select id="dependency" name="dependency" required>
            </select>

            <label for="contractor">Contratista:</label>
            <input type="text" id="contractor" name="contractor" required>

            <label for="nit">Nit:</label>
            <input type="text" id="nit" name="nit" required>

            <label for="name">Proceso:</label>
            <input type="text" id="name" name="name" required>

            <label for="objective">Objeto del contrato:</label>
            <textarea id="objective" name="objective" rows="4" required></textarea>

            <label for="total_value">Valor del contrato:</label>
            <input type="text" id="total_value" name="total_value" required>

            <label for="duration">Duración:</label>
            <input type="text" id="duration" name="duration" required>

            <label for="subscription_date">Fecha (visible):</label>
            <input type="date" id="subscription_date" name="subscription_date">

            <label for="start_date">Fecha inicio:</label>
            <input type="date" id="start_date" name="start_date">

            <label for="end_date">Fecha terminación:</label>
            <input type="date" id="end_date" name="end_date">

            <label for="cutoff_date">Fecha limite:</label>
            <input type="date" id="cutoff_date" name="cutoff_date">

            <label for="link_secop">Link Secop:</label>
            <input type="text" id="link_secop" name="link_secop" required>

            <input type="hidden" name="state" value="1">
            <input type="hidden" name="user_register_id" value="{{ auth()->id() }}">

            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>
<script>
function openModalCreateContrac() {
    document.getElementById('modalCreateContrac').style.display = 'flex';
}

function closeModalContrac() {
    document.getElementById('modalCreateContrac').style.display = 'none';
}

document.addEventListener("DOMContentLoaded", function () {
    fetch("/dashboard/dependencies/all")
        .then(response => response.json())
        .then(dependencies => {
            const select = document.getElementById("dependency");
            select.innerHTML = '';

            dependencies.forEach(dep => {
                let option = document.createElement("option");
                option.value = dep.name;
                option.text = dep.name;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error("Error cargando dependencias:", error);
        });
});

document.getElementById("createContracForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch("{{ route('dashboard.contracs.store') }}", {
        method: "POST",
        body: formData,
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        if (status === 422) {
            console.error("Errores de validación:", body);
            alert("Error: " + Object.values(body.errors).flat().join("\n"));
        } else if (status === 500) {
            console.error("Error del servidor:", body);
            alert("Error interno del servidor. Consulta la consola.");
        } else {
            alert(body.message || "Contrato creado exitosamente.");
            closeModalContrac();
            location.reload();
        }
    })
    .catch(error => {
        console.error("Error inesperado:", error);
        alert("Hubo un problema al crear el contrato.");
    });
});
</script>