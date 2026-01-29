<div id="modalCreateContractor" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalContractor()">&times;</span>
        <h2>Agregar Nuevo Contratista</h2>

        <form id="createContractorForm" enctype="multipart/form-data">
            @csrf
            <label for="contract_number">Número de contrato:</label>
            <input type="text" id="contract_number" name="contract_number" required>

            <label for="contractor">Contratista:</label>
            <input type="text" id="contractor" name="contractor" required>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>

            <label for="work_experience">Experiencia laboral:</label>
            <input type="text" id="work_experience" name="work_experience" required>

            <label for="object">Objeto del contrato:</label>
            <textarea id="object" name="object" rows="4" required></textarea>

            <label for="start_date">Fecha inicio:</label>
            <input type="date" id="start_date" name="start_date">

            <label for="cutoff_date">Fecha limite:</label>
            <input type="date" id="cutoff_date" name="cutoff_date">

            <label for="total_value">Valor total:</label>
            <input type="number" id="total_value" name="total_value" required>

            <label for="monthly_value">Valor mensual:</label>
            <input type="number" id="monthly_value" name="monthly_value" required>

            <label for="supervision">Supervisión:</label>
            <input type="text" id="supervision" name="supervision" required>

            <label for="dependency">Área:</label>
            <select id="dependency" name="dependency" required>
            </select>

            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>
<script>
function openModalCreateContractor() {
    document.getElementById('modalCreateContractor').style.display = 'flex';
}

function closeModalContractor() {
    document.getElementById('modalCreateContractor').style.display = 'none';
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

document.getElementById("createContractorForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch("{{ route('dashboard.contractors.store') }}", {
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
            alert(body.message || "Contratista creado exitosamente.");
            closeModalContractor();
            location.reload();
        }
    })
    .catch(error => {
        console.error("Error inesperado:", error);
        alert("Hubo un problema al crear el contratista.");
    });
});
</script>