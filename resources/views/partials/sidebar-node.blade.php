@php
  $hasChildren = $node->childrenRecursive->isNotEmpty();
@endphp

<li class="menu-item">
  <a href="{{ $node->route ? route($node->route) : '#' }}"
     @if($hasChildren) onclick="toggleSubmenu(event, '{{ $node->id }}')" @endif>
    @if ($node->icon)
      <img src="{{ asset($node->icon) }}" alt="{{ $node->name }} icon" class="menu-icon">
    @endif
    <span class="menu-text">{{ $node->name }}</span>
    @if ($hasChildren)
      <svg id="toggle-icon-{{ $node->id }}" class="toggle-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
        <path fill="#003B70" d="M6 9l6 6 6-6z"></path>
      </svg>
    @endif
  </a>

  @if ($hasChildren)
    <ul class="submenu" id="submenu-{{ $node->id }}" style="display: none;">
      @foreach ($node->childrenRecursive as $child)
        @include('partials.sidebar-node', ['node' => $child])
      @endforeach
    </ul>
  @endif
</li>
