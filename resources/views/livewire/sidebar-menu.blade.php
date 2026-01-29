<div class="sidebar" id="sidebar">
    <div class="sidebar-inner">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="-5 -7 24 24">
                <path fill="#004085" d="M1 0h5a1 1 0 1 1 0 2H1a1 1 0 1 1 0-2m7 8h5a1 1 0 0 1 0 2H8a1 1 0 1 1 0-2M1 4h12a1 1 0 0 1 0 2H1a1 1 0 1 1 0-2"/>
            </svg>
        </button>

        <div class="sidebar-header">
            <div class="entity-header">
                <img src="{{ asset($entityLogo) }}" class="govco-logo" alt="Logo {{ $entityName }}">
            </div>
            @auth
                <p class="user-greeting">Bienvenido, {{ Auth::user()->name }}</p>
            @endauth
        </div>

        <nav class="sidebar-menu">
            <ul>
                @foreach ($modules as $module)
                    <li class="menu-item">
                        @if ($module->route)
                            <a href="{{ route($module->route) }}">
                        @else
                            <a href="#" onclick="toggleSubmenu(event, '{{ $module->id }}')">
                        @endif
                                <img src="{{ asset('icon/' . $module->icon) }}" alt="{{ $module->name }} Icono" class="menu-icon">
                                <span class="menu-text">{{ $module->name }}</span>

                                @if ($module->childrenRecursive->count() > 0)
                                    <svg id="toggle-icon-{{ $module->id }}" class="toggle-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                        <path fill="#003B70" d="M6 9l6 6 6-6z"></path>
                                    </svg>
                                @endif
                            </a>

                        @if ($module->childrenRecursive->count() > 0)
                            <ul class="submenu" id="submenu-{{ $module->id }}" style="display: none;">
                                @foreach ($module->childrenRecursive as $child)
                                    <li>
                                        <a href="{{ $child->route ? route($child->route) : '#' }}">
                                            <img src="{{ asset('icon/' . $child->icon) }}" alt="{{ $child->name }} Icono" class="submenu-icon">
                                            <span class="submenu-text">{{ $child->name }}</span>
                                        </a>

                                        {{-- Si hay más niveles, renderízalos también --}}
                                        @if ($child->childrenRecursive->count() > 0)
                                            <ul class="submenu" id="submenu-{{ $child->id }}" style="display: none;">
                                                @foreach ($child->childrenRecursive as $grandchild)
                                                    <li>
                                                        <a href="{{ $grandchild->route ? route($grandchild->route) : '#' }}">
                                                            <img src="{{ asset('icon/' . $grandchild->icon) }}" alt="{{ $grandchild->name }} Icono" class="submenu-icon">
                                                            <span class="submenu-text">{{ $grandchild->name }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24">
                        <path fill="#004085" d="M15 2h-1c-2.828 0-4.243 0-5.121.879C8 3.757 8 5.172 8 8v8c0 2.828 0 4.243.879 5.121C9.757 22 11.172 22 14 22h1c2.828 0 4.243 0 5.121-.879C21 20.243 21 18.828 21 16V8c0-2.828 0-4.243-.879-5.121C19.243 2 17.828 2 15 2" opacity=".6"/>
                        <path fill="#004085" d="M8 8c0-1.538 0-2.657.141-3.5H8c-2.357 0-3.536 0-4.268.732S3 7.143 3 9.5v5c0 2.357 0 3.535.732 4.268S5.643 19.5 8 19.5h.141C8 18.657 8 17.538 8 16z" opacity=".4"/>
                        <path fill="#004085" fill-rule="evenodd" d="M4.47 11.47a.75.75 0 0 0 0 1.06l2 2a.75.75 0 0 0 1.06-1.06l-.72-.72H14a.75.75 0 0 0 0-1.5H6.81l.72-.72a.75.75 0 1 0-1.06-1.06z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("collapsed");
}

function toggleSubmenu(event, moduleId) {
    event.preventDefault();
    const submenu = document.getElementById('submenu-' + moduleId);
    const icon = document.getElementById('toggle-icon-' + moduleId);
    if (submenu) {
        const isVisible = submenu.style.display === 'block';
        submenu.style.display = isVisible ? 'none' : 'block';
        if (icon) icon.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(180deg)';
    }
}
</script>

<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("collapsed");
    document.body.classList.toggle("sidebar-collapsed");
}

function toggleSubmenu(event, moduleId) {
    event.preventDefault();
    const submenu = document.getElementById('submenu-' + moduleId);
    const icon = document.getElementById('toggle-icon-' + moduleId);
    if (submenu) {
        const isVisible = submenu.style.display === 'block';
        submenu.style.display = isVisible ? 'none' : 'block';
        if (icon) icon.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(180deg)';
    }
}
</script>

<style>
#container {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 260px;
    background-color: #ffffff;
    color: var(--govco-fourth-color);
    transition: width 0.3s ease-in-out;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    border-right: 3px solid #dcdcdc;
    box-shadow: 2px 0px 5px rgba(0, 0, 0, 0.1);
    
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 1000;
}

.sidebar-inner {
    display: flex;
    flex-direction: column;
    height: 100%;
    width: 100%;
}

.sidebar.collapsed {
    width: 80px;
}

#main-content {
    width: 100%;
    margin-left: 260px;
    transition: margin-left 0.3s ease-in-out;
}

body.sidebar-collapsed #main-content {
    margin-left: 80px;
}

.sidebar-toggle {
    position: absolute;
    top: 5px;
    right: 5px;
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.3s;
    z-index: 10;
}

.sidebar-toggle:hover {
    background-color: var(--govco-fourth-color);
}

.sidebar-header {
    padding: 1rem;
    background-color: var(--govco-gray-color);
    text-align: center;
    border-bottom: 2px solid #dcdcdc;
    flex-shrink: 0;
}

.sidebar-header h2 {
    font-size: 1.4rem;
    margin: 0;
    font-weight: bold;
}

.sidebar-header p {
    font-size: 0.9rem;
    margin-top: 0.5rem;
    color: #555;
}

.sidebar.collapsed .sidebar-header h2,
.sidebar.collapsed .sidebar-header p {
    display: none;
}

.sidebar-menu {
    flex: 1;
    padding-top: 1rem;
    overflow-y: auto;
    overflow-x: hidden;
}

.sidebar-menu::-webkit-scrollbar {
    width: 6px;
}
.sidebar-menu::-webkit-scrollbar-track {
    background: transparent;
}
.sidebar-menu::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.1);
    border-radius: 3px;
}

.sidebar-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-menu ul li a {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    text-decoration: none;
    font-size: 1rem;
    font-weight: 600;
    transition: background 0.3s ease-in-out;
    border-radius: 5px;
    white-space: nowrap;
}

.sidebar-menu ul li a:hover {
    background-color: #FEDF13;
    color: #000;
    box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.05);
}

.sidebar-menu ul li a:hover svg {
    fill: #000;
}

.sidebar-menu ul li a svg {
    min-width: 25px;
    margin-right: 10px;
    fill: var(--govco-fourth-color);
}

.menu-text {
    margin-left: 0.5rem;
}

.sidebar-menu ul li.active > a {
    background-color: #FEDF13;
    color: #000;
    font-weight: bold;
}

.sidebar-menu ul li.active > a svg {
    fill: #000;
}

.submenu {
    display: none;
    padding-left: 20px;
}

.submenu li {
    margin-left: 15px;
}

.menu-item.active .submenu {
    display: block;
}

.expand-toggle {
    cursor: pointer;
    background: none;
    border: none;
    color: #00489A;
    font-size: 0.9rem;
    margin-left: auto;
}

.expand-toggle:hover {
    color: #002147;
}

.toggle-icon {
    transition: transform 0.3s ease-in-out;
    margin-left: auto;
}

.menu-item.active .toggle-icon {
    transform: rotate(180deg);
}

.sidebar.collapsed .menu-text {
    display: none;
}

.sidebar.collapsed .sidebar-menu ul li a,
.sidebar.collapsed .logout-button {
    justify-content: center;
}

.sidebar.collapsed .toggle-icon {
    display: none;
}

.sidebar.collapsed .submenu {
    display: none !important;
}

.sidebar-footer {
    border-top: 2px solid #dcdcdc;
    background: #f8f9fa;
    margin-top: auto;
    padding: 0;
    width: 100%;
    flex-shrink: 0;
    position: sticky;
    bottom: 0;
}

.logout-button {
    width: 100%;
    background: none;
    border: none;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    padding: 1rem;
    transition: background 0.3s;
    border-radius: 0;
}


.logout-button:hover {
    background-color: #FEDF13;
    color: #000;
}

.logout-button:hover svg {
    fill: #000;
}

@media (max-width: 768px) {
    .sidebar {
        width: 80px;
        transform: translateX(-100%);
    }
    
    .sidebar.collapsed {
        width: 80px;
        transform: translateX(0);
    }

    #main-content {
        margin-left: 0 !important;
    }
}

.entity-header {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0.5rem;
}

.govco-logo {
    max-width: 160px;
    height: auto;
    transition: all 0.3s ease-in-out;
}

.sidebar.collapsed .govco-logo {
    max-width: 40px;
    height: auto;
}

.sidebar.collapsed .user-greeting {
    display: none;
}
</style>