<div id="modalExportContractor" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalExportContractor()">&times;</span>
        <h2>Exportar archivo</h2>

        <form method="GET" action="{{ route('dashboard.contractors.export') }}">
            @csrf
            <label for="year_contract">Año:</label>
            <input type="number" name="year_contract" min="2000" max="2100" required>

            <label for="month_contract">Mes:</label>
            <select name="month_contract">
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

            <button type="submit" class="btn-submit">Exportar Excel</button>
        </form>
    </div>
</div>

<script>
function openModalExportContractor() {
    document.getElementById('modalExportContractor').style.display = 'flex';
}

function closeModalExportContractor() {
    document.getElementById('modalExportContractor').style.display = 'none';
}
</script>