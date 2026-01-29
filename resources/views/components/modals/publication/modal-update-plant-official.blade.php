<div id="modalEditPlantOfficial" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalEditPlantOfficial()">&times;</span>
        <h2>Editar Funcionario</h2>

        <form id="editPlantOfficialForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="edit_id" name="id">

            <label for="edit_document_type">Tipo de documento:</label>
            <select id="edit_document_type" name="document_type" required>
                <option value="" selected disabled>Seleccione una opción...</option>
                <option value="Cédula de ciudadanía">Cédula de ciudadanía</option>
                <option value="Tarjeta de identidad">Tarjeta de identidad</option>
                <option value="Número de identificación tributaria">Número de identificación tributaria (NIT)</option>
            </select>

            <label for="edit_document_number">Número de documento:</label>
            <input type="text" id="edit_document_number" name="document_number" required>

            <label for="edit_fullname">Nombres y apellidos:</label>
            <input type="text" id="edit_fullname" name="fullname" required>

            <label for="edit_email">Email:</label>
            <input type="text" id="edit_email" name="email">

            <label for="edit_cellphone">Celular:</label>
            <input type="text" id="edit_cellphone" name="cellphone">

            <label for="edit_charge">Cargo:</label>
            <select id="edit_charge" name="charge" required>
                <option value="" selected disabled>Seleccione una opción...</option>
                <option value="Profesional Universitario">Profesional Universitario</option>
                <option value="Profesional Especializado">Profesional Especializado</option>
                <option value="Conductor">Conductor</option>
            </select>

            <label for="edit_dependency">Área:</label>
            <select id="edit_dependency" name="dependency" required>
            </select>

            <label for="edit_subdependencie">Subdependencia:</label>
            <input type="text" id="edit_subdependencie" name="subdependencie" required>

            <label for="edit_code">Código:</label>
            <input type="text" id="edit_code" name="code" required>

            <label for="edit_grade">Grado:</label>
            <input type="text" id="edit_grade" name="grade" required>

            <label for="edit_level">Nivel:</label>
            <input type="text" id="edit_level" name="level" required>

            <label for="edit_denomination">Denominación:</label>
            <input type="text" id="edit_denomination" name="denomination" required>

            <label for="edit_total_value">Valor salario:</label>
            <input type="number" id="edit_total_value" name="total_value" required>

            <label for="edit_representation_expenses">Gastos de representación:</label>
            <input type="number" id="edit_representation_expenses" name="representation_expenses">

            <label for="edit_init_date">Fecha inicio:</label>
            <input type="date" id="edit_init_date" name="init_date">

            <label for="edit_vacation_date">Fecha vacaciones:</label>
            <input type="date" id="edit_vacation_date" name="vacation_date">

            <label for="edit_bonus_date">Fecha bonificación:</label>
            <input type="date" id="edit_bonus_date" name="bonus_date">

            <label for="edit_birthdate">Fecha de nacimiento:</label>
            <input type="date" id="edit_birthdate" name="birthdate">

            <label for="edit_eps">EPS:</label>
            <select id="edit_eps" name="eps" required>
                <option value="" selected disabled>Seleccione una opción...</option>
                <option value="Sura">Sura</option>
                <option value="Sanitas">Sanitas</option>
                <option value="Compensar">Compensar</option>
                <option value="Salud Total">Salud Total</option>
                <option value="Nueva EPS">Nueva EPS</option>
                <option value="Coosalud">Coosalud</option>
                <option value="Mutual SER">Mutual SER</option>
                <option value="Famisanar">Famisanar</option>
                <option value="Capital Salud">Capital Salud</option>
                <option value="Ecoopsos">Ecoopsos</option>
                <option value="Emssanar">Emssanar</option>
                <option value="Asmet Salud">Asmet Salud</option>
                <option value="Cajacopi">Cajacopi</option>
                <option value="Ambuq">Ambuq</option>
                <option value="Savia Salud">Savia Salud</option>
            </select>

            <button type="submit" class="btn-submit">Actualizar</button>
        </form>
    </div>
</div>

<script>
function openEditPlantOfficialModal(id) {
    fetch(`/dashboard/plantofficials/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("edit_id").value = data.id;
            document.getElementById("edit_document_type").value = data.document_type ?? '';
            document.getElementById("edit_document_number").value = data.document_number ?? '';
            document.getElementById("edit_fullname").value = data.fullname ?? '';
            document.getElementById("edit_email").value = data.email ?? '';
            document.getElementById("edit_cellphone").value = data.cellphone ?? '';
            document.getElementById("edit_charge").value = data.charge ?? '';
            document.getElementById("edit_subdependencie").value = data.subdependencie ?? '';
            document.getElementById("edit_code").value = data.code ?? '';
            document.getElementById("edit_grade").value = data.grade ?? '';
            document.getElementById("edit_level").value = data.level ?? '';
            document.getElementById("edit_denomination").value = data.denomination ?? '';
            document.getElementById("edit_total_value").value = data.total_value ?? '';
            document.getElementById("edit_representation_expenses").value = data.representation_expenses ?? '';
            document.getElementById("edit_init_date").value = data.init_date ?? '';
            document.getElementById("edit_vacation_date").value = data.vacation_date ?? '';
            document.getElementById("edit_bonus_date").value = data.bonus_date ?? '';
            document.getElementById("edit_birthdate").value = data.birthdate ?? '';
            document.getElementById("edit_eps").value = data.eps ?? '';

            // Cargar dependencias
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

            document.getElementById("modalEditPlantOfficial").style.display = "flex";
        })
        .catch(error => {
            console.error("Error al cargar funcionario:", error);
            alert("No se pudo cargar el funcionario.");
        });
}

function closeModalEditPlantOfficial() {
    document.getElementById("modalEditPlantOfficial").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("editPlantOfficialForm");
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(form);
        formData.append('_method', 'PUT');
        const id = document.getElementById("edit_id").value;

        fetch(`/dashboard/plantofficials/${id}`, {
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
            alert("Funcionario actualizado exitosamente.");
            closeModalEditPlantOfficial();
            location.reload();
        })
        .catch(error => {
            console.error("Error:", error);
            alert("No se pudo actualizar el funcionario.");
        });
    });
});
</script>