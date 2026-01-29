<div id="modalEditContrac" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalEditContrac()">&times;</span>
        <h2>Editar Contrato</h2>

        <form id="editContracForm" enctype="multipart/form-data">
            @csrf

            <input type="hidden" id="edit_id" name="id">

            <label for="edit_contract_number">Número de contrato:</label>
            <input type="text" id="edit_contract_number" name="contract_number" required>

            <label for="edit_dependency">Área:</label>
            <select id="edit_dependency" name="dependency" required></select>

            <label for="edit_contractor">Contratista:</label>
            <input type="text" id="edit_contractor" name="contractor" required>

            <label for="edit_nit">Nit:</label>
            <input type="text" id="edit_nit" name="nit" required>

            <label for="edit_name">Proceso:</label>
            <input type="text" id="edit_name" name="name" required>

            <label for="edit_objective">Objeto del contrato:</label>
            <textarea id="edit_objective" name="objective" rows="4" required></textarea>

            <label for="edit_total_value">Valor del contrato:</label>
            <input type="text" id="edit_total_value" name="total_value" required>

            <label for="edit_duration">Duración:</label>
            <input type="text" id="edit_duration" name="duration" required>

            <label for="edit_subscription_date">Fecha (visible):</label>
            <input type="date" id="edit_subscription_date" name="subscription_date">

            <label for="edit_start_date">Fecha inicio:</label>
            <input type="date" id="edit_start_date" name="start_date">

            <label for="edit_end_date">Fecha terminación:</label>
            <input type="date" id="edit_end_date" name="end_date">

            <label for="edit_cutoff_date">Fecha limite:</label>
            <input type="date" id="edit_cutoff_date" name="cutoff_date">

            <label for="edit_link_secop">Link Secop:</label>
            <input type="text" id="edit_link_secop" name="link_secop" required>

            <input type="hidden" name="state" value="1">
            <input type="hidden" name="user_register_id" value="{{ auth()->id() }}">

            <button type="submit" class="btn-submit">Actualizar</button>
        </form>
    </div>
</div>

<script>
function openEditContracModal(id) {
    fetch(`/dashboard/contracs/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("edit_id").value = data.id;
            document.getElementById("edit_contract_number").value = data.contract_number ?? '';
            document.getElementById("edit_contractor").value = data.contractor ?? '';
            document.getElementById("edit_nit").value = data.nit ?? '';
            document.getElementById("edit_name").value = data.name ?? '';
            document.getElementById("edit_objective").value = data.objective ?? '';
            document.getElementById("edit_total_value").value = data.total_value ?? '';
            document.getElementById("edit_duration").value = data.duration ?? '';
            document.getElementById("edit_subscription_date").value = data.subscription_date ?? '';
            document.getElementById("edit_start_date").value = data.start_date ?? '';
            document.getElementById("edit_end_date").value = data.end_date ?? '';
            document.getElementById("edit_cutoff_date").value = data.cutoff_date ?? '';
            document.getElementById("edit_link_secop").value = data.link_secop ?? '';

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

            document.getElementById("modalEditContrac").style.display = "flex";
        })
        .catch(error => {
            console.error("Error al cargar contrato:", error);
            alert("No se pudo cargar el contrato.");
        });
}

function closeModalEditContrac() {
    document.getElementById("modalEditContrac").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("editContracForm");
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(form);
        formData.append('_method', 'PUT');

        const id = document.getElementById("edit_id").value;

        fetch(`/dashboard/contracs/${id}`, {
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
            alert("Contrato actualizado exitosamente.");
            closeModalEditContrac();
            location.reload();
        })
        .catch(error => {
            console.error("Error:", error);
            alert("No se pudo actualizar el contrato.");
        });
    });
});
</script>