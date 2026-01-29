<div id="modalEditContractor" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalEditContractor()">&times;</span>
        <h2>Editar Contratista</h2>

        <form id="editContractorForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="edit_id" name="id">

            <label for="edit_contract_number">Número de contrato:</label>
            <input type="text" id="edit_contract_number" name="contract_number" required>

            <label for="edit_contractor">Contratista:</label>
            <input type="text" id="edit_contractor" name="contractor" required>

            <label for="edit_email">Email:</label>
            <input type="email" id="edit_email" name="email" required>

            <label for="edit_work_experience">Experiencia laboral:</label>
            <input type="text" id="edit_work_experience" name="work_experience" required>

            <label for="edit_object">Objeto del contrato:</label>
            <input type="text" id="edit_object" name="object" required>

            <label for="edit_start_date">Fecha inicio:</label>
            <input type="date" id="edit_start_date" name="start_date">

            <label for="edit_cutoff_date">Fecha límite:</label>
            <input type="date" id="edit_cutoff_date" name="cutoff_date">

            <label for="edit_total_value">Valor total:</label>
            <input type="number" id="edit_total_value" name="total_value" required>

            <label for="edit_monthly_value">Valor mensual:</label>
            <input type="number" id="edit_monthly_value" name="monthly_value" required>

            <label for="edit_supervision">Supervisión:</label>
            <input type="text" id="edit_supervision" name="supervision" required>

            <label for="edit_dependency">Área:</label>
            <select id="edit_dependency" name="dependency" required></select>

            <button type="submit" class="btn-submit">Actualizar</button>
        </form>
    </div>
</div>

<script>
function openEditContractorModal(id) {
    fetch(`/dashboard/contractors/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("edit_id").value = data.id;
            document.getElementById("edit_contract_number").value = data.contract_number ?? '';
            document.getElementById("edit_contractor").value = data.contractor ?? '';
            document.getElementById("edit_email").value = data.email ?? '';
            document.getElementById("edit_work_experience").value = data.work_experience ?? '';
            document.getElementById("edit_object").value = data.object ?? '';
            document.getElementById("edit_start_date").value = data.start_date ?? '';
            document.getElementById("edit_cutoff_date").value = data.cutoff_date ?? '';
            document.getElementById("edit_total_value").value = data.total_value ?? '';
            document.getElementById("edit_monthly_value").value = data.monthly_value ?? '';
            document.getElementById("edit_supervision").value = data.supervision ?? '';

            fetch("/dashboard/dependencies/all")
                .then(response => response.json())
                .then(dependencies => {
                    const select = document.getElementById("edit_dependency");
                    select.innerHTML = '';
                    dependencies.forEach(dep => {
                        const option = document.createElement("option");
                        option.value = dep.name;
                        option.text = dep.name;
                        if (dep.name === data.dependency) option.selected = true;
                        select.appendChild(option);
                    });
                });

            document.getElementById("modalEditContractor").style.display = "flex";
        })
        .catch(error => {
            console.error("Error al cargar contratista:", error);
            alert("No se pudo cargar el contratista.");
        });
}

function closeModalEditContractor() {
    document.getElementById("modalEditContractor").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("editContractorForm");
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(form);
        formData.append('_method', 'PUT');
        const id = document.getElementById("edit_id").value;

        fetch(`/dashboard/contractors/${id}`, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => {
            if (!response.ok) throw new Error("Error al actualizar");
            return response.json();
        })
        .then(data => {
            alert("Contratista actualizado exitosamente.");
            closeModalEditContractor();
            location.reload();
        })
        .catch(error => {
            console.error("Error:", error);
            alert("No se pudo actualizar el contratista.");
        });
    });
});
</script>