@php
  $level = $level ?? 0;
  $hasChildren = $mod->children && $mod->children->count();
@endphp

<div class="node" data-node-id="{{ $mod->id }}" style="--indent: {{ $level }};">
  <div class="row">
    {{-- checkbox al inicio, siempre en la misma columna --}}
    <input type="checkbox"
      class="cb-module"
      name="modules_mark[]"
      value="{{ $mod->id }}"
      data-module-id="{{ $mod->id }}"
      onchange="onModuleToggle({{ $mod->id }}, this.checked)">

    {{-- nombre del módulo (clic opcional para expandir si hay hijos) --}}
    <span class="module-name"
          @if($hasChildren) onclick="toggleNode({{ $mod->id }})" style="cursor:pointer" @endif>
      {{ $mod->name }}
    </span>

    {{-- chevron al extremo derecho --}}
    <button type="button"
      class="chevron"
      @if(!$hasChildren) disabled @endif
      onclick="toggleNode({{ $mod->id }})"
      aria-label="expandir">▸</button>
  </div>

  {{-- permisos (columna) --}}
  @if($mod->permissions && $mod->permissions->count())
    <div class="perms" data-permissions-of="{{ $mod->id }}">
      <label class="perm perm-all">
        <input type="checkbox" class="cb-all-perms"
               onchange="toggleAllPerms({{ $mod->id }}, this.checked)">
        <span>Seleccionar todos</span>
      </label>

      <div class="perms-list">
        @foreach($mod->permissions as $perm)
          <label class="perm">
            <input type="checkbox"
              class="cb-perm"
              name="permissions[{{ $mod->id }}][]"
              value="{{ $perm->id }}"
              data-module-id="{{ $mod->id }}"
              data-perm-id="{{ $perm->id }}"
              onchange="onPermToggle({{ $mod->id }})">
            <span>{{ $perm->name }}</span>
          </label>
        @endforeach
      </div>
    </div>
  @endif

  {{-- children en lista vertical --}}
  @if($hasChildren)
    <div class="children" id="children-{{ $mod->id }}">
      @foreach($mod->children as $ch)
        @include('components.modals.Users._node', ['mod' => $ch, 'level' => $level + 1])
      @endforeach
    </div>
  @endif
</div>