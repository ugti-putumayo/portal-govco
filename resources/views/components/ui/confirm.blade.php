<div id="confirm-overlay" class="c-overlay" style="display:none;">
  <div class="c-modal" role="dialog" aria-modal="true" aria-labelledby="confirm-title">
    <div class="c-header">
      <h3 id="confirm-title">¿Confirmar acción?</h3>
      <button class="c-close" type="button" aria-label="Cerrar" onclick="Confirm.close()">×</button>
    </div>
    <div class="c-body">
      <p id="confirm-message">¿Estás seguro?</p>
    </div>
    <div class="c-footer">
      <button class="btn btn-secondary" type="button" onclick="Confirm.cancel()">Cancelar</button>
      <button id="confirm-accept" class="btn btn-primary" type="button" onclick="Confirm.accept()">Aceptar</button>
    </div>
  </div>
</div>

<style>
.c-overlay{
  position:fixed; inset:0; display:flex; align-items:center; justify-content:center;
  background:rgba(0,0,0,.35); z-index:9999;
}
.c-modal{
  width: min(480px, 92vw);
  background: var(--govco-white-color);
  border:1px solid var(--govco-gray-color);
  border-radius:12px; overflow:hidden; box-shadow: var(--govco-box-shadow);
}
.c-header{ display:flex; justify-content:space-between; align-items:center;
  padding:12px 16px; border-bottom:1px solid var(--govco-gray-color);
}
.c-header h3{ margin:0; font-size:18px; color: var(--govco-secondary-color); }
.c-close{ background:transparent; border:none; font-size:22px; cursor:pointer; color: var(--govco-tertiary-color); }
.c-body{ padding:16px; color: var(--govco-tertiary-color); }
.c-footer{ display:flex; gap:8px; justify-content:flex-end; padding:12px 16px; background: #fafbfd; border-top:1px solid var(--govco-gray-color); }

.btn{ border-radius:8px; padding:8px 14px; cursor:pointer; border:1px solid var(--govco-border-color); }
.btn-primary{ background: var(--govco-primary-color); color:#fff; border-color: var(--govco-primary-color); }
.btn-primary:hover{ filter: brightness(0.95); }
.btn-secondary{ background:#fff; color: var(--govco-tertiary-color); }
</style>

<script>
(function(){
  const $overlay = document.getElementById('confirm-overlay');
  const $title   = document.getElementById('confirm-title');
  const $msg     = document.getElementById('confirm-message');
  const $accept  = document.getElementById('confirm-accept');

  let resolver = null;

  window.Confirm = {
    open(opts = {}){
      const {
        title   = '¿Confirmar acción?',
        message = '¿Estás seguro de continuar?',
        confirmText = 'Aceptar',
        cancelText  = 'Cancelar',
        danger = false,
      } = opts;

      $title.textContent = title;
      $msg.textContent   = message;
      $accept.textContent = confirmText;
      document.querySelector('#confirm-overlay .btn-secondary').textContent = cancelText;

      $accept.classList.toggle('btn-danger', !!danger);
      if (danger){
        $accept.style.background = 'var(--govco-error-color)';
        $accept.style.borderColor= 'var(--govco-error-color)';
      } else {
        $accept.style.background = 'var(--govco-primary-color)';
        $accept.style.borderColor= 'var(--govco-primary-color)';
      }

      $overlay.style.display = 'flex';
      return new Promise((resolve)=>{ resolver = resolve; });
    },
    close(){ $overlay.style.display = 'none'; },
    accept(){ if(resolver){ resolver(true); } this.close(); },
    cancel(){ if(resolver){ resolver(false);} this.close(); }
  };

  $overlay.addEventListener('click', (e)=>{ if(e.target === $overlay) Confirm.cancel(); });
  document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape' && $overlay.style.display==='flex'){ Confirm.cancel(); } });
})();
</script>