<div id="modalChangePasswordUser" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <span class="close-modal" onclick="closeChangePasswordModal()">&times;</span>
        <h2>Cambiar contraseña</h2>

        <form id="changePasswordForm" class="modal-form">
            @csrf
            @method('PUT')

            <input type="hidden" id="change_password_user_id" name="user_id">

            <div class="modal-field">
                <label for="new_password">Nueva contraseña:</label>
                <div class="password-field">
                    <input type="password" id="new_password" name="password" required>
                    <button type="button"
                            class="toggle-password-btn"
                            onclick="togglePasswordVisibility('new_password', this)"
                            aria-label="Mostrar u ocultar contraseña">
                        <img src="{{ asset('icon/eye.svg') }}" alt="Ver" class="toggle-icon show-icon">
                        <img src="{{ asset('icon/eye-off.svg') }}" alt="Ocultar" class="toggle-icon hide-icon">
                    </button>
                </div>
            </div>

            <div class="modal-field">
                <label for="new_password_confirmation">Confirmar contraseña:</label>
                <div class="password-field">
                    <input type="password" id="new_password_confirmation" name="password_confirmation" required>
                    <button type="button"
                            class="toggle-password-btn"
                            onclick="togglePasswordVisibility('new_password_confirmation', this)"
                            aria-label="Mostrar u ocultar contraseña">
                        <img src="{{ asset('icon/eye.svg') }}" alt="Ver" class="toggle-icon show-icon">
                        <img src="{{ asset('icon/eye-off.svg') }}" alt="Ocultar" class="toggle-icon hide-icon">
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">Guardar Contraseña</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openChangePasswordModal(userId) {
    const form  = document.getElementById('changePasswordForm');
    const input = document.getElementById('change_password_user_id');

    if (!form || !input) return;

    input.value = userId;

    const pwd1 = document.getElementById('new_password');
    const pwd2 = document.getElementById('new_password_confirmation');

    if (pwd1) { pwd1.value = ''; pwd1.type = 'password'; }
    if (pwd2) { pwd2.value = ''; pwd2.type = 'password'; }

    document.querySelectorAll('#modalChangePasswordUser .toggle-password-btn').forEach(btn => {
        btn.classList.remove('is-visible');
    });

    document.getElementById('modalChangePasswordUser').style.display = 'flex';
}

function closeChangePasswordModal() {
    document.getElementById('modalChangePasswordUser').style.display = 'none';
}

function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    if (isPassword) {
        btn.classList.add('is-visible');
    } else {
        btn.classList.remove('is-visible');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('changePasswordForm');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const userId   = document.getElementById('change_password_user_id').value;
        const formData = new FormData(form);

        try {
            const resp = await fetch(`{{ url('dashboard/users') }}/${userId}/password`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrf()
                }
            });

            const data = await resp.json().catch(() => ({}));

            if (resp.ok) {
                if (typeof Toast !== 'undefined' && Toast.success) {
                    Toast.success(data.message || 'Contraseña actualizada con éxito.');
                }
                closeChangePasswordModal();
            } else {
                if (typeof Toast !== 'undefined' && Toast.error) {
                    Toast.error(data.message || 'No se pudo actualizar la contraseña.');
                }
            }
        } catch (e) {
            if (typeof Toast !== 'undefined' && Toast.error) {
                Toast.error('Hubo un problema al actualizar la contraseña.');
            }
        }
    });
});
</script>
@endpush

<style>
.password-field {
    position: relative;
    display: flex;
    align-items: center;
}

.password-field input {
    width: 100%;
    padding-right: 2.5rem;
    box-sizing: border-box;
}

.toggle-password-btn {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    padding: 0;
    margin: 0;
    cursor: pointer;

    width: 24px;
    height: 24px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;

    outline: none;
}

.toggle-password-btn:hover {
    background: rgba(0, 0, 0, 0.04);
}

.toggle-password-btn:focus-visible {
    outline: 2px solid var(--govco-primary-color);
    outline-offset: 2px;
}

/* tamaño icono */
.toggle-password-btn .toggle-icon {
    width: 18px;
    height: 18px;
}

.toggle-password-btn .hide-icon {
    display: none;
}

.toggle-password-btn.is-visible .show-icon {
    display: none;
}

.toggle-password-btn.is-visible .hide-icon {
    display: inline-block;
}
</style>