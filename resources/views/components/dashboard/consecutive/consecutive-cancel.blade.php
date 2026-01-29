<div id="modalCancelConsecutive" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModalCancelConsecutive()">&times;</span>
        <h2>Cancelar Consecutivo</h2>

        <form id="cancelConsecutiveForm">
            @csrf
            <input type="hidden" id="cancel_consecutive_id" name="id">

            <p style="margin-bottom: 10px;">
                ¿Está seguro que desea cancelar este consecutivo?
            </p>

            <label for="cancel_consecutive_full">Consecutivo:</label>
            <input type="text" id="cancel_consecutive_full" readonly>

            <label for="cancel_consecutive_subject">Asunto:</label>
            <textarea id="cancel_consecutive_subject" rows="2" readonly></textarea>

            <label for="cancel_consecutive_reason">Motivo de cancelación:</label>
            <textarea id="cancel_consecutive_reason" name="cancellation_reason" rows="3" required></textarea>

            <button type="submit" class="btn-submit">Confirmar cancelación</button>
        </form>
    </div>
</div>

<script>
function openModalCancelConsecutive(id) {
  fetch(`{{ url('dashboard/consecutives') }}/${id}`)
    .then(res => res.json())
    .then(data => {
      document.getElementById('cancel_consecutive_id').value      = data.id;
      document.getElementById('cancel_consecutive_full').value    = data.full_consecutive || '';
      document.getElementById('cancel_consecutive_subject').value = data.subject || '';
      document.getElementById('cancel_consecutive_reason').value  = '';

      document.getElementById('modalCancelConsecutive').style.display = 'flex';
    })
    .catch(err => {
      console.error('Error al cargar consecutivo:', err);
      Toast.error('No se pudo cargar la información del consecutivo.');
    });
}

function closeModalCancelConsecutive() {
  document.getElementById('modalCancelConsecutive').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
  const cancelConsecutiveForm = document.getElementById('cancelConsecutiveForm');
  if (!cancelConsecutiveForm) return;

  cancelConsecutiveForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const id = document.getElementById('cancel_consecutive_id').value;

    const formData = new FormData(this);
    formData.append('_method', 'PATCH');

    fetch(`{{ url('dashboard/consecutives') }}/${id}/anular`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrf(),
        'Accept': 'application/json',
      },
    })
      .then(async res => {
        const body = await res.json().catch(() => ({}));
        return { status: res.status, body };
      })
      .then(({ status, body }) => {
        if (status === 422) {
          const msg = body.errors
            ? Object.values(body.errors).flat().join('\n')
            : 'Hay errores de validación.';
          Toast.error(msg, { title: 'Validación' });
        } else if (status >= 200 && status < 300) {
          Toast.success(body.message || 'Consecutivo cancelado correctamente.');
          closeModalCancelConsecutive();
          setTimeout(() => location.reload(), 900);
        } else {
          console.error('Error:', body);
          Toast.error(body.message || 'No se pudo cancelar el consecutivo.');
        }
      })
      .catch(err => {
        console.error('Error:', err);
        Toast.error('No se pudo cancelar el consecutivo.');
      });
  });
});
</script>