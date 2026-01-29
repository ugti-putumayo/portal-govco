<div id="toast-container" class="t-container" aria-live="polite" aria-atomic="true"></div>

<style>
.t-container{ position: fixed; top: 16px; right:16px; display:flex; flex-direction:column; gap:10px; z-index:10000; }
.t-item{
  min-width: 280px; max-width: 420px;
  background:#fff; border:1px solid var(--govco-gray-color); border-left:6px solid var(--govco-primary-color);
  border-radius:10px; padding:10px 12px; box-shadow: var(--govco-box-shadow);
  display:flex; gap:10px; align-items:flex-start;
}
.t-title{ font-weight:700; margin:0; color: var(--govco-tertiary-color); }
.t-msg{ margin:2px 0 0; color: var(--govco-tertiary-color); font-size:14px; }
.t-close{ margin-left:auto; background:transparent; border:none; font-size:18px; cursor:pointer; color:#777; }

.t-success{ border-left-color: var(--govco-success-color); }
.t-error  { border-left-color: var(--govco-error-color); }
.t-info   { border-left-color: var(--govco-primary-color); }
.t-warn   { border-left-color: #f59e0b; } /* ámbar */
</style>

<script>
(function(){
  const $wrap = document.getElementById('toast-container');

  function createToast(type, title, message, duration){
    const $t = document.createElement('div');
    $t.className = `t-item t-${type}`;
    $t.innerHTML = `
      <div>
        <h4 class="t-title">${title || (type==='success'?'Hecho':'Aviso')}</h4>
        ${message ? `<p class="t-msg">${message}</p>` : ``}
      </div>
      <button class="t-close" aria-label="Cerrar">×</button>
    `;
    $wrap.appendChild($t);

    const close = ()=>{ $t.style.opacity='0'; setTimeout(()=> $t.remove(), 180); };
    $t.querySelector('.t-close').addEventListener('click', close);
    if (duration !== 0){
      setTimeout(close, duration || 3500);
    }
  }

  window.Toast = {
    success(msg, opts={}){ createToast('success', opts.title || 'Éxito', msg, opts.duration); },
    error(msg, opts={})  { createToast('error',   opts.title || 'Error', msg, opts.duration); },
    info(msg, opts={})   { createToast('info',    opts.title || 'Información', msg, opts.duration); },
    warn(msg, opts={})   { createToast('warn',    opts.title || 'Atención', msg, opts.duration); },
  };
})();
</script>