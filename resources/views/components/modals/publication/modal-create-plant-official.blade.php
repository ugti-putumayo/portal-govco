<div id="modalCreatePlantOfficial" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalCreatePlantOfficial()">&times;</span>
        <h2>Agregar Nuevo Funcionario</h2>

        <form id="createPlantOfficialForm" enctype="multipart/form-data">
            @csrf

            <label for="year_plantofficial">Año:</label>
            <input type="number" name="year_plantofficial" min="2000" max="2100" required>

            <label for="month_plantofficial">Mes:</label>
            <select name="month_plantofficial">
                <option value="">-- Todos los meses --</option>
                <option value="1">Enero</option>
                <option value="2">Febrero</option>
                <option value="3">Marzo</option>
                <option value="4">Abril</option>
                <option value="5">Mayo</option>
                <option value="6">Junio</option>
                <option value="7">Julio</option>
                <option value="8">Agosto</option>
                <option value="9">Septiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
            </select>

            <label for="document_type">Tipo de documento:</label>
            <select id="document_type" name="document_type" required>
                <option value="" selected disabled>Seleccione una opción...</option>
                <option value="Cédula de ciudadanía">Cédula de ciudadanía</option>
                <option value="Tarjeta de identidad">Tarjeta de identidad</option>
                <option value="Número de identificación tributaria">Número de identificación tributaria (NIT)</option>
            </select>

            <label for="document_number">Número de número:</label>
            <input type="text" id="document_number" name="document_number" required>

            <label for="fullname">Nombres y apellidos:</label>
            <input type="text" id="fullname" name="fullname" required>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email">

            <label for="cellphone">Celular:</label>
            <input type="text" id="cellphone" name="cellphone">

            <label for="charge">Cargo:</label>
            <select id="charge" name="charge" required>
                <option value="" selected disabled>Seleccione una opción...</option>
                <option value="Profesional Universitario">Profesional Universitario</option>
                <option value="Profesional Especializado">Profesional Especializado</option>
                <option value="Conductor">Conductor</option>
            </select>

            <label for="dependency">Dependencia:</label>
            <select id="dependency" name="dependency" required>
            </select>

            <label for="subdependencie">Subdependencia</label>
            <input type="text" id="subdependencie" name="subdependencie" required>

            <label for="code">Código:</label>
            <input type="text" id="code" name="code" required>

            <label for="grade">Grado:</label>
            <input type="text" id="grade" name="grade" required>

            <label for="level">Nivel:</label>
            <input type="text" id="level" name="level" required>

            <label for="denomination">Denominación:</label>
            <input type="text" id="denomination" name="denomination" required>

            <label for="total_value">Valor salario:</label>
            <input type="number" id="total_value" name="total_value" required>

            <label for="representation_expenses">Gastos de representación:</label>
            <input type="number" id="representation_expenses" name="representation_expenses">

            <label for="init_date">Fecha inicio:</label>
            <input type="date" id="init_date" name="init_date">

            <label for="vacation_date">Fecha vacaciones:</label>
            <input type="date" id="vacation_date" name="vacation_date">

            <label for="bonus_date">Fecha bonificación:</label>
            <input type="date" id="bonus_date" name="bonus_date">

            <label for="birthdate">Fecha de nacimiento:</label>
            <input type="date" id="birthdate" name="birthdate">

            <label for="eps">EPS:</label>
            <select id="eps" name="eps" required>
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

            <button type="submit" class="btn-submit">Guardar</button>
        </form>
    </div>
</div>
<script>
function openModalCreatePlantOfficial() {
    document.getElementById('modalCreatePlantOfficial').style.display = 'flex';
}

function closeModalCreatePlantOfficial() {
    document.getElementById('modalCreatePlantOfficial').style.display = 'none';
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

document.getElementById("createPlantOfficialForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch("{{ route('dashboard.plantofficials.store') }}", {
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
            alert(body.message || "Funcionario creado exitosamente.");
            closeModalCreatePlantOfficial();
            location.reload();
        }
    })
    .catch(error => {
        console.error("Error inesperado:", error);
        alert("Hubo un problema al crear el funcionario.");
    });
});
</script>