<div id="modal-show-consecutive" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Detalle del Consecutivo</h3>
            <button type="button" class="modal-close" onclick="closeModalShowConsecutive()">×</button>
        </div>
        
        <div class="modal-body">
            <div id="loading-show" class="text-center py-4">
                <p>Cargando información...</p>
            </div>

            <div id="content-show" style="display: none;">
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Consecutivo:</label>
                        <span id="show-full_consecutive" class="detail-value highlight"></span>
                    </div>
                    <div class="detail-item">
                        <label>Estado:</label>
                        <span id="show-status" class="detail-value"></span>
                    </div>
                    <div class="detail-item full-width">
                        <label>Asunto:</label>
                        <p id="show-subject" class="detail-text"></p>
                    </div>
                    <div class="detail-item">
                        <label>Destinatario:</label>
                        <span id="show-recipient" class="detail-value"></span>
                    </div>
                    <div class="detail-item">
                        <label>Serie:</label>
                        <span id="show-series" class="detail-value"></span>
                    </div>
                    <div class="detail-item">
                        <label>Generado por:</label>
                        <span id="show-user" class="detail-value"></span>
                    </div>
                    <div class="detail-item">
                        <label>Fecha Creación:</label>
                        <span id="show-date" class="detail-value"></span>
                    </div>
                </div>

                <div id="cancellation-info" class="cancellation-box" style="display: none;">
                    <h4 class="cancellation-title">Información de Anulación</h4>
                    <div class="detail-item">
                        <label>Anulado por:</label>
                        <span id="show-canceled-by"></span>
                    </div>
                    <div class="detail-item">
                        <label>Fecha Anulación:</label>
                        <span id="show-canceled-at"></span>
                    </div>
                    <div class="detail-item full-width">
                        <label>Motivo:</label>
                        <p id="show-cancellation-reason" class="detail-text"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const modalShow = document.getElementById('modal-show-consecutive');
    const loadingShow = document.getElementById('loading-show');
    const contentShow = document.getElementById('content-show');
    const cancelInfo = document.getElementById('cancellation-info');

    window.addEventListener('open-modal-show-consecutive', (event) => {
        const id = event.detail.id;
        openModalShowConsecutive(id);
    });

    function openModalShowConsecutive(id) {
        modalShow.style.display = 'flex';
        loadingShow.style.display = 'block';
        contentShow.style.display = 'none';
        cancelInfo.style.display = 'none';

        fetch(`/dashboard/consecutives/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('show-full_consecutive').textContent = data.full_consecutive;
                document.getElementById('show-subject').textContent = data.subject;
                document.getElementById('show-recipient').textContent = data.recipient;
                document.getElementById('show-series').textContent = data.series ? data.series.name : 'N/A';
                document.getElementById('show-user').textContent = data.user ? data.user.name : 'N/A';
                document.getElementById('show-date').textContent = new Date(data.created_at).toLocaleString();

                const statusSpan = document.getElementById('show-status');
                if(data.status === 'Canceled') {
                    statusSpan.textContent = 'ANULADO';
                    statusSpan.style.color = 'var(--govco-error-color)';
                    
                    cancelInfo.style.display = 'block';
                    document.getElementById('show-canceled-by').textContent = data.canceled_by ? data.canceled_by.name : 'N/A';
                    document.getElementById('show-canceled-at').textContent = new Date(data.canceled_at).toLocaleString();
                    document.getElementById('show-cancellation-reason').textContent = data.cancellation_reason;
                } else {
                    statusSpan.textContent = 'GENERADO';
                    statusSpan.style.color = 'var(--govco-success-color)';
                }

                loadingShow.style.display = 'none';
                contentShow.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los detalles.');
                closeModalShowConsecutive();
            });
    }

    function closeModalShowConsecutive() {
        modalShow.style.display = 'none';
    }
</script>

<style>
.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
.detail-item {
    display: flex;
    flex-direction: column;
}
.detail-item.full-width {
    grid-column: span 2;
}
.detail-item label {
    font-weight: bold;
    color: #555;
    font-size: 0.85rem;
}
.detail-value {
    font-size: 0.95rem;
    color: #000;
}
.detail-text {
    background: #f9f9f9;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #eee;
    margin-top: 5px;
}
.highlight {
    font-weight: bold;
    color: var(--govco-primary-color);
    font-size: 1.1rem;
}
.cancellation-box {
    margin-top: 20px;
    padding: 15px;
    border: 1px solid var(--govco-error-color);
    border-radius: 5px;
    background-color: #fff5f5;
}
.cancellation-title {
    color: var(--govco-error-color);
    font-weight: bold;
    margin-bottom: 10px;
    font-size: 1rem;
}
</style>