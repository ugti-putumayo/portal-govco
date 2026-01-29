@php
use Illuminate\Support\Str;
@endphp

@extends('dashboard.dashboard')

@section('content')
<div class="container-modules with-app-navbar">
    <div class="navbar app-navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/chat-white.svg') }}" class="app-navbar__icon" alt="Chat">
            <h2 class="app-navbar__title">GoChat</h2>
        </div>
    </div>

    <div class="content-modules" style="margin-top: 90px;">
        <div class="chat-wrapper">
            <div class="chat-users">
                <h3 class="chat-section-title">Usuarios</h3>
                <ul id="chat-users-list">
                    @foreach ($users as $u)
                        <li class="chat-user-item"
                            data-user-id="{{ $u->id }}"
                            data-user-name="{{ $u->name }}">
                            <img src="{{ $u->profile_photo_url }}" class="chat-avatar">
                            <span>{{ $u->name }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="chat-panel">
                <div class="chat-header">
                    <span id="chat-with">Selecciona un usuario</span>

                    <div class="chat-options" id="chat-options-container" style="display: none;">
                        <button type="button" class="btn-options" id="btn-options">⋮</button>
                        
                        <div class="options-dropdown" id="options-dropdown">
                            <a href="#" id="opt-clear-chat" class="text-danger">Vaciar Chat</a>
                            <a href="#" id="opt-attach-file">Enviar Archivo</a>
                        </div>
                    </div>
                </div>

                <div id="chat-messages" class="chat-messages"></div>

                <form id="chat-form" class="chat-input-area">
                    @csrf
                    <input type="text" id="chat-input" class="chat-input"
                        placeholder="Escribe un mensaje…" autocomplete="off" disabled>
                    <button class="chat-send-btn" disabled>Enviar</button>
                </form>
            </div>
            <input type="file" id="file-input" style="display: none;">
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const authId       = {{ auth()->id() }};
    const chatBaseUrl  = "{{ url('dashboard/chat') }}";
    const usersList    = document.getElementById('chat-users-list');
    const messagesBox  = document.getElementById('chat-messages');
    const chatWith     = document.getElementById('chat-with');
    const form         = document.getElementById('chat-form');
    const input        = document.getElementById('chat-input');
    
    const optionsContainer = document.getElementById('chat-options-container');
    const btnOptions       = document.getElementById('btn-options');
    const dropdown         = document.getElementById('options-dropdown');
    const btnClear         = document.getElementById('opt-clear-chat');
    const btnAttach        = document.getElementById('opt-attach-file');
    const fileInput        = document.getElementById('file-input');

    let currentUserId = null;

    window.Echo.private(`user.${authId}`)
        .listen('.MessageSent', (e) => {            
            const m      = e.message;
            const fromId = m.sender_id;

            if (currentUserId && parseInt(currentUserId) === parseInt(fromId)) {
                appendMessage(m, false);
                scrollBottom();
                return;
            }

            const userItem = usersList.querySelector(`li[data-user-id="${fromId}"]`);
            
            if (userItem) {
                let badge = userItem.querySelector('.chat-unread');
                let count = 1;

                if (badge) {
                    count = parseInt(badge.textContent) + 1;
                } else {
                    badge = document.createElement('span');
                    badge.className = 'chat-unread';
                    userItem.appendChild(badge);
                }

                badge.textContent = count;
                usersList.prepend(userItem); 
            }
        });

    usersList.addEventListener('click', function (e) {
        const li = e.target.closest('li[data-user-id]');
        if (!li) return;

        const userId   = li.dataset.userId;
        const userName = li.dataset.userName;

        currentUserId = userId;
        chatWith.textContent = 'Chateando con ' + userName;
        messagesBox.innerHTML = '';
        input.disabled = false;
        form.querySelector('button').disabled = false;
        
        if (optionsContainer) optionsContainer.style.display = 'block';

        const badge = li.querySelector('.chat-unread');
        if (badge) badge.remove();

        fetch(`${chatBaseUrl}/${userId}`)
            .then(res => res.json())
            .then(messages => {
                messages.forEach(m => appendMessage(m, m.sender_id == authId));
                scrollBottom();
            });
    });

    if (btnOptions) {
        btnOptions.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
    }

    document.addEventListener('click', () => {
        if (dropdown) dropdown.classList.remove('show');
    });

    // VACIAR CHAT CON CONFIRM + TOAST
    if (btnClear) {
        btnClear.addEventListener('click', async (e) => {
            e.preventDefault();

            if (!currentUserId) {
                if (typeof Toast !== 'undefined' && Toast.error) {
                    Toast.error('Primero selecciona un usuario para vaciar el chat.');
                }
                return;
            }

            let ok = true;

            if (typeof Confirm !== 'undefined' && typeof Confirm.open === 'function') {
                ok = await Confirm.open({
                    title: 'Vaciar chat',
                    message: 'Esta acción borrará todo el historial de conversación con este usuario. ¿Deseas continuar?',
                    confirmText: 'Vaciar',
                    cancelText: 'Cancelar',
                    danger: true,
                });
            } else {
                // fallback muy simple si por alguna razón no existe Confirm
                ok = window.confirm('¿Seguro de borrar todo el historial?');
            }

            if (!ok) return;

            try {
                const res = await fetch(`${chatBaseUrl}/${currentUserId}/clear`, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (!res.ok) throw new Error('Respuesta no OK');

                messagesBox.innerHTML = '';

                if (typeof Toast !== 'undefined' && Toast.success) {
                    Toast.success('Historial de chat vaciado con éxito.');
                }
            } catch (err) {
                if (typeof Toast !== 'undefined' && Toast.error) {
                    Toast.error('Hubo un problema al vaciar el historial de chat.');
                }
            }
        });
    }

    if (btnAttach) {
        btnAttach.addEventListener('click', (e) => {
            e.preventDefault();
            if (!currentUserId) {
                if (typeof Toast !== 'undefined' && Toast.error) {
                    Toast.error('Primero selecciona un usuario para enviar archivos.');
                }
                return;
            }
            fileInput.click();
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                submitMessage(null, this.files[0]);
                this.value = '';
            }
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const text = input.value.trim();
        if (!text || !currentUserId) return;
        submitMessage(text, null);
    });

    function submitMessage(text, file) {
        if (!currentUserId) return;

        const formData = new FormData();
        if (text) formData.append('content', text);
        if (file) formData.append('attachment', file);

        if (text) {
            input.value = '';
            appendMessage({ content: text, sender_id: authId }, true); 
            scrollBottom();
        }

        fetch(`${chatBaseUrl}/${currentUserId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData
        })
        .then(res => res.json())
        .then(m => {
            if (file) {
                appendMessage(m, true);
                scrollBottom();
            }
        })
        .catch(() => {
            if (typeof Toast !== 'undefined' && Toast.error) {
                Toast.error('No se pudo enviar el mensaje.');
            }
        });
    }

    function appendMessage(m, mine) {
        const text       = m.content || '';
        const senderName = mine ? 'Tú' : (m.sender?.name || '');

        let attachmentHtml = '';
        if (m.attachment_path) {
            const url = `/storage/${m.attachment_path}`;
            const fileName = m.attachment_name 
                || (m.attachment_path ? m.attachment_path.split('/').pop() : 'Descargar archivo');

            if (m.attachment_type === 'image') {
                attachmentHtml = `
                    <br>
                    <a href="${url}" target="_blank">
                        <img src="${url}" class="chat-image" alt="${fileName}">
                    </a>
                `;
            } else {
                attachmentHtml = `
                    <br>
                    <a href="${url}" target="_blank" class="chat-file-link">
                        📎 ${fileName}
                    </a>
                `;
            }
        }

        const div = document.createElement('div');
        div.className = 'chat-msg ' + (mine ? 'mine' : 'theirs');
        div.innerHTML = `
            <div class="chat-msg-user">${senderName}</div>
            <div class="chat-msg-text">
                ${text}
                ${attachmentHtml}
            </div>
        `;
        messagesBox.appendChild(div);
    }

    function scrollBottom() {
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }
});
</script>
@endpush

<style>
.navbar-header-title {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* Ajuste del contenedor principal para reducir espacio superior */
.content-modules {
    margin-top: 20px !important; /* Reducido de 90px */
    height: calc(100vh - 100px); /* Ajuste para llenar pantalla */
    display: flex;
    flex-direction: column;
}

.chat-wrapper {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 1.5rem;
    height: 100%; /* Ocupar toda la altura disponible */
    max-height: 100%;
}

/* Panel de Usuarios */
.chat-users {
    background: white;
    border-radius: 12px;
    padding: 0; /* Padding controlado internamente */
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    border: 1px solid #f0f0f0;
}

.chat-section-title {
    font-family: var(--govco-font-primary);
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    padding: 1.5rem 1.5rem 1rem;
    margin: 0;
    border-bottom: 1px solid #f5f5f5;
}

#chat-users-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.chat-user-item {
    position: relative;
    padding: 12px 1.5rem;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    border-bottom: 1px solid #f9f9f9;
    transition: all 0.2s ease;
}

.chat-user-item:hover {
    background: #f8f9fa;
}

.chat-user-item.active {
    background: #eef2ff; /* Color suave de selección */
    border-left: 4px solid var(--govco-primary-color);
}

.chat-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Panel de Chat Principal */
.chat-panel {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #f0f0f0;
}

.chat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--govco-primary-color);
    color: #ffffff !important; /* Forzar color blanco */
    padding: 1rem 1.5rem;
    font-weight: 600;
    font-size: 1.05rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 10;
}

.chat-header span {
    color: #ffffff; /* Asegurar texto blanco */
}

.chat-messages {
    padding: 1.5rem;
    flex: 1;
    overflow-y: auto;
    background: #f4f6f8;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* Burbujas de Mensaje */
.chat-msg {
    max-width: 70%;
    padding: 10px 16px;
    border-radius: 12px;
    position: relative;
    font-size: 0.95rem;
    line-height: 1.5;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.chat-msg.mine {
    background: var(--govco-primary-color);
    color: white;
    margin-left: auto;
    border-bottom-right-radius: 2px;
}

.chat-msg.theirs {
    background: white;
    color: #333;
    margin-right: auto;
    border-bottom-left-radius: 2px;
    border: 1px solid #eee;
}

.chat-msg-user {
    font-size: 0.75rem;
    opacity: 0.8;
    margin-bottom: 4px;
    font-weight: 500;
}

/* Área de Input */
.chat-input-area {
    display: flex;
    padding: 1rem 1.5rem;
    border-top: 1px solid #eee;
    gap: 12px;
    background: white;
    align-items: center;
}

.chat-input {
    flex: 1;
    border: 1px solid #e0e0e0;
    border-radius: 24px;
    padding: 12px 20px;
    font-size: 0.95rem;
    transition: border-color 0.2s;
    background: #f9f9f9;
}

.chat-input:focus {
    outline: none;
    border-color: var(--govco-primary-color);
    background: white;
    box-shadow: 0 0 0 3px rgba(51, 102, 204, 0.1);
}

.chat-send-btn {
    background: var(--govco-secondary-color);
    color: white;
    padding: 0 24px;
    height: 45px;
    border-radius: 24px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: transform 0.1s, background 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-send-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    filter: brightness(1.1);
}

.chat-send-btn:active:not(:disabled) {
    transform: translateY(0);
}

.chat-send-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background: #ccc;
}

/* Badge de no leídos */
.chat-unread {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background-color: #ef4444; /* Rojo más moderno */
    color: white;
    font-weight: 700;
    font-size: 0.75rem;
    min-width: 22px;
    height: 22px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 6px;
    box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3);
    animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes popIn {
    from { transform: translateY(-50%) scale(0); }
    to { transform: translateY(-50%) scale(1); }
}

/* Opciones y Dropdown */
.chat-options {
    position: relative;
}

.btn-options {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.btn-options:hover {
    background: rgba(255,255,255,0.3);
}

.options-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 110%;
    background: white;
    border: 1px solid #eee;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    width: 180px;
    z-index: 100;
    overflow: hidden;
    animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.options-dropdown.show {
    display: block;
}

.options-dropdown a {
    display: block;
    padding: 12px 16px;
    text-decoration: none;
    color: #444;
    font-size: 14px;
    transition: background 0.2s;
}

.options-dropdown a:hover {
    background: #f8f9fa;
}

.text-danger { color: #dc3545 !important; }

/* Archivos e Imágenes */
.chat-image {
    max-width: 100%;
    max-height: 300px;
    border-radius: 8px;
    margin-top: 8px;
    cursor: pointer;
    border: 1px solid rgba(0,0,0,0.1);
}

.chat-file-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(0,0,0,0.05);
    padding: 10px 14px;
    border-radius: 8px;
    color: inherit;
    text-decoration: none;
    font-size: 0.9rem;
    margin-top: 8px;
    border: 1px solid rgba(0,0,0,0.05);
    transition: background 0.2s;
}

.chat-file-link:hover {
    background: rgba(0,0,0,0.1);
}

/* Scrollbar personalizado */
.chat-messages::-webkit-scrollbar,
.chat-users::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track,
.chat-users::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb,
.chat-users::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.1);
    border-radius: 3px;
}
</style>