<div id="modalUploadContractor" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalUploadContractor()">&times;</span>
        <h2>Subir archivo</h2>

        <form id="uploadContractorForm" enctype="multipart/form-data">
            @csrf

            <label for="excel_file">Cargar Excel:</label>
            <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv">

            <button type="submit" class="btn-submit">Subir Excel</button>
        </form>
    </div>
</div>
<script>
function openModalUploadContractor() {
    document.getElementById('modalUploadContractor').style.display = 'flex';
}

function closeModalUploadContractor() {
    document.getElementById('modalUploadContractor').style.display = 'none';
}

document.getElementById("uploadContractorForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch("{{ route('dashboard.contractors.import') }}", {
        method: "POST",
        body: formData,
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        closeModalUploadContractor();
        location.reload();
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Hubo un problema al importar el archivo.");
    });
});
</script>