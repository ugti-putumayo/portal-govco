<div id="modalAssignAccess" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <span class="close-modal" onclick="closeAssignAccess()">&times;</span>
    <h3>Accesos del Usuario</h3>

    <form id="assignAccessForm">
      @csrf
      <input type="hidden" id="assign_user_id" name="user_id">

      <label for="assign_role_id">Rol:</label>
      <select id="assign_role_id" name="role_id" required>
        <option value="">Selecciona un rol</option>
        @foreach ($roles as $role)
          <option value="{{ $role->id }}">{{ $role->name }}</option>
        @endforeach
      </select>

        <div style="margin-top: 12px;">
            <div class="flex-header">
                <label class="block" style="font-weight:700;">Módulos y permisos</label>
                <div class="btns">
                <button type="button" class="btn small" onclick="expandAll(true)">Expandir todo</button>
                <button type="button" class="btn small" onclick="expandAll(false)">Contraer todo</button>
                </div>
            </div>

            <div id="tree_modules" class="tree">
                @foreach ($modules as $root)
                @include('components.modals.Users._node', ['mod' => $root, 'level' => 0])
                @endforeach
            </div>
        </div>

      <button type="submit" class="btn primary" style="margin-top:16px;">Guardar</button>
    </form>
  </div>
</div>

<style>
#modalAssignAccess .modal-content{
  background: var(--govco-white-color);
  padding:20px; width:760px; border-radius:12px; margin:auto; position:relative;
  box-shadow: var(--govco-box-shadow);
}
#modalAssignAccess select,#modalAssignAccess button{display:block;margin-bottom:12px}

.flex-header{
  position:sticky; top:0; z-index:5;
  display:flex; justify-content:space-between; align-items:center;
  padding:6px 0; background: var(--govco-white-color);
  border-bottom:1px solid var(--govco-gray-color);
  margin:6px 0 12px;
}
.btn{
  border:1px solid var(--govco-border-color);
  border-radius:8px; padding:8px 12px; background: var(--govco-white-color);
  cursor:pointer; color: var(--govco-tertiary-color);
}
.btn.small{padding:6px 10px; font-size:12px}
.btn.primary{
  background: var(--govco-primary-color);
  color: var(--govco-white-color);
  border-color: var(--govco-primary-color);
}

.tree{display:block}
.node{
  border:1px solid var(--govco-gray-color);
  border-radius:10px; margin-bottom:10px; background: var(--govco-white-color);
}

.node>.row{
  display:grid; grid-template-columns: 24px 1fr 20px; align-items:center; gap:10px;
  padding:10px 12px; border-radius:10px;
  margin-left: calc(var(--indent,0) * 16px);
}

.cb-module{width:16px; height:16px; cursor:pointer}

.module-name{font-weight:700; line-height:1.2; color: var(--govco-tertiary-color)}

.chevron{
  border:none; background:transparent; font-size:16px; line-height:1; padding:0;
  cursor:pointer;
  color: var(--govco-secondary-color);
  opacity:.95;
}
.chevron:hover{ color: var(--govco-primary-color) }
.chevron[disabled]{ color: var(--govco-border-color); opacity:.55; cursor:default }

.perms{
  display:block;
  margin-left: calc( (var(--indent,0) * 16px) + 24px );
  padding: 4px 12px 10px 0;
}

.perm{display:grid; grid-template-columns: 16px auto; align-items:center; gap:10px; color: var(--govco-tertiary-color)}
.perm-all{ margin-bottom:8px; font-weight:600 }
.perms-list{ display:flex; flex-direction:column; gap:8px }

.cb-perm,.cb-all-perms{ width:16px; height:16px; cursor:pointer }

.children{
  display:none; margin:4px 0 10px 0;
  padding-left: calc((var(--indent,0) + 1) * 16px);
  border-left:2px solid var(--govco-gray-color);
}
</style>

<script>
(function(){
  const modalEl   = document.getElementById('modalAssignAccess');
  const formEl    = document.getElementById('assignAccessForm');
  const roleSel   = document.getElementById('assign_role_id');
  const treeEl    = document.getElementById('tree_modules');
  let currentUserId = null;

  window.openAssignAccess = function(userId, currentRoleId = null){
    currentUserId = userId;
    document.getElementById('assign_user_id').value = userId;

    if (currentRoleId) roleSel.value = String(currentRoleId);

    // reset UI
    treeEl.querySelectorAll('input[type="checkbox"]').forEach(cb => { cb.checked=false; cb.indeterminate=false; });
    treeEl.querySelectorAll('.children').forEach(c => c.style.display='none');
    treeEl.querySelectorAll('.chev').forEach(c => c.textContent='▸');

    // precargar desde tu endpoint actual
    fetch(`/dashboard/usermodules/user/${userId}/permissions`)
      .then(r => r.json())
      .then(data => {
        const markedIds = (data.modules ?? []).map(m => m.id);
        markedIds.forEach(mid => {
          const cb = treeEl.querySelector(`.cb-module[data-module-id="${mid}"]`);
          if (cb) cb.checked = true;
        });

        const pairs = (data.permissions ?? []);
        pairs.forEach(p => {
          const cb = treeEl.querySelector(`.cb-perm[data-module-id="${p.module_id}"][data-perm-id="${p.permission_id}"]`);
          if (cb) cb.checked = true;
        });

        treeEl.querySelectorAll('[data-permissions-of]').forEach(w => refreshAllPermsCheckbox(parseInt(w.dataset.permissionsOf)));
      })
      .catch(()=>{});

    modalEl.style.display = 'flex';
  };

  window.closeAssignAccess = function(){ modalEl.style.display = 'none'; };

  window.toggleNode = function(moduleId){
    const cont = document.getElementById('children-' + moduleId);
    const chev = document.querySelector(`.node[data-node-id="${moduleId}"] .chevron`);
    if (!cont || !chev) return;
    const show = cont.style.display !== 'block';
    cont.style.display = show ? 'block' : 'none';
    chev.textContent = show ? '▾' : '▸';
  };

  window.expandAll = function(state){
    document.querySelectorAll('.children').forEach(c => c.style.display = state ? 'block' : 'none');
    document.querySelectorAll('.chevron:not([disabled])').forEach(ch => ch.textContent = state ? '▾' : '▸');
  };

  window.onModuleToggle = function(moduleId, checked){
    treeEl.querySelectorAll(`.cb-perm[data-module-id="${moduleId}"]`).forEach(cb => cb.checked = checked);
    refreshAllPermsCheckbox(moduleId);

    const children = document.querySelectorAll(`#children-${moduleId} .cb-module`);
    children.forEach(cb => {
      cb.checked = checked;
      const childId = parseInt(cb.dataset.moduleId);
      treeEl.querySelectorAll(`.cb-perm[data-module-id="${childId}"]`).forEach(pcb => pcb.checked = checked);
      refreshAllPermsCheckbox(childId);
      onModuleToggle(childId, checked);
    });

    refreshParentIndeterminate(moduleId);
  };

  window.onPermToggle = function(moduleId){
    refreshAllPermsCheckbox(moduleId);
    const modCb = treeEl.querySelector(`.cb-module[data-module-id="${moduleId}"]`);
    if (modCb) modCb.indeterminate = hasChildrenAnyChecked(moduleId) && countChecked(`.cb-perm[data-module-id="${moduleId}"]`)===0;
    refreshParentIndeterminate(moduleId);
  };

  window.toggleAllPerms = function(moduleId, checked){
    treeEl.querySelectorAll(`.cb-perm[data-module-id="${moduleId}"]`).forEach(cb => cb.checked = checked);
    const modCb = treeEl.querySelector(`.cb-module[data-module-id="${moduleId}"]`);
    if (modCb) modCb.checked = checked;
    refreshParentIndeterminate(moduleId);
  };

  function refreshAllPermsCheckbox(moduleId){
    const block = treeEl.querySelector(`[data-permissions-of="${moduleId}"]`);
    if (!block) return;
    const allPerms = block.querySelectorAll('.cb-perm');
    if (!allPerms.length) return;
    const checked = Array.from(allPerms).filter(cb => cb.checked).length;
    const master  = block.querySelector('.cb-all-perms');
    master.indeterminate = checked>0 && checked<allPerms.length;
    master.checked = checked===allPerms.length;

    const modCb = treeEl.querySelector(`.cb-module[data-module-id="${moduleId}"]`);
    if (modCb){
      modCb.indeterminate = checked>0 && checked<allPerms.length;
      if (checked===allPerms.length) modCb.checked = true;
      if (checked===0 && !hasChildrenAnyChecked(moduleId)) modCb.indeterminate = false;
    }
  }

  function refreshParentIndeterminate(moduleId){
    const parentId = getParentId(moduleId);
    if (!parentId) return;

    const parentCb = treeEl.querySelector(`.cb-module[data-module-id="${parentId}"]`);
    if (!parentCb) return;

    const childModuleCbs = document.querySelectorAll(`#children-${parentId} .cb-module`);
    const total = childModuleCbs.length;
    const chkd  = Array.from(childModuleCbs).filter(c => c.checked).length;

    parentCb.indeterminate = chkd>0 && chkd<total;
    parentCb.checked = chkd===total;

    refreshParentIndeterminate(parentId);
  }

  function hasChildrenAnyChecked(moduleId){
    const wrap = document.getElementById('children-' + moduleId);
    if (!wrap) return false;
    return Array.from(wrap.querySelectorAll('.cb-module, .cb-perm')).some(cb => cb.checked);
  }

  function countChecked(sel){ return Array.from(treeEl.querySelectorAll(sel)).filter(cb => cb.checked).length; }

  function getNodeChev(moduleId){
    const node = document.querySelector(`.node[data-node-id="${moduleId}"]`);
    return node ? node.querySelector('.chev') : null;
  }

  function getParentId(childId){
    const node = document.querySelector(`.node[data-node-id="${childId}"]`);
    if (!node) return null;
    const parentChildren = node.parentElement;
    if (!parentChildren || !/^children-/.test(parentChildren.id)) return null;
    return parseInt(parentChildren.id.replace('children-','')) || null;
  }

  formEl.addEventListener('submit', function(e){
    e.preventDefault();
    if (!currentUserId) return;

    const fd = new FormData(formEl);
    fd.set('user_id', currentUserId);

    // modules[] (marcados para menú)
    fd.delete('modules[]');
    Array.from(treeEl.querySelectorAll('.cb-module:checked'))
      .map(cb => parseInt(cb.dataset.moduleId))
      .filter(Boolean)
      .forEach(id => fd.append('modules[]', id));

    // permissions[moduleId][]
    for (const [k] of fd.entries()) if (k.startsWith('permissions[')) fd.delete(k);
    Array.from(treeEl.querySelectorAll('.cb-perm:checked')).forEach(cb => {
      fd.append(`permissions[${cb.dataset.moduleId}][]`, cb.dataset.permId);
    });

    fetch("{{ route('dashboard.usermodules.syncAll') }}", {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
        "X-Requested-With": "XMLHttpRequest"
      },
      body: fd
    })
    .then(async res => {
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw data;
      alert(data.message || 'Accesos actualizados');
      closeAssignAccess();
    })
    .catch(err => alert(err?.message || 'Ocurrió un error'));
  });
})();
</script>