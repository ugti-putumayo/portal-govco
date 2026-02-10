<header class="navbar-container">
    @php
        $currentLocale = app()->getLocale();
        $languages = [
            'es' => ['code' => 'ES', 'name' => 'Español'],
            'en' => ['code' => 'EN', 'name' => 'English'],
            'zh_CN' => ['code' => 'ZH', 'name' => '中文']
        ];
        $currentLang = $languages[$currentLocale] ?? $languages['es'];
    @endphp

    <div class="navbar-top">
        <div class="govco-logo">
            <a href="https://www.gov.co" target="_blank">
                <img src="/logos/logo_govco.png" alt="Logo GOV.CO">
            </a>
        </div>
        <div class="actions">
            <a href="https://historico.ticputumayo.gov.co" target="_blank" rel="noopener noreferrer" class="portal-historico__btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15 12.5v4l3 2l.75-1.25l-2.25-1.5V12.5zm7-.11V12c0-5.5-4.5-10-10-10C6.47 2 2 6.5 2 12s4.5 10 10 10c.13 0 .24 0 .37-.03c1.06.65 2.3 1.03 3.63 1.03c3.86 0 7-3.14 7-7c0-1.32-.38-2.56-1-3.61m-2.24-2.28l-.17-.11h.15c.01.03.01.07.02.11M18.92 8h-2.95a15.7 15.7 0 0 0-1.38-3.56c1.84.63 3.37 1.9 4.33 3.56M12 4.03c.83 1.2 1.5 2.54 1.91 3.97h-3.82c.41-1.43 1.08-2.77 1.91-3.97M9.66 10h2.75a7 7 0 0 0-2.84 3.24c-.04-.41-.07-.82-.07-1.24c0-.68.06-1.35.16-2M9.4 4.44C8.8 5.55 8.35 6.75 8 8H5.08A7.92 7.92 0 0 1 9.4 4.44M4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2s.06 1.34.14 2zm.82 2H8c.35 1.25.8 2.45 1.4 3.56A8 8 0 0 1 5.08 16M16 21c-2.76 0-5-2.24-5-5s2.24-5 5-5s5 2.24 5 5s-2.24 5-5 5"/>
                </svg>
                <span>Portal Histórico</span>
            </a>
            
            <div class="lang-dropdown">
                <button class="lang-btn" onclick="toggleLangMenu()">
                    <span class="lang-code-current">{{ $currentLang['code'] }}</span>
                    <span class="arrow-down">▼</span>
                </button>
                <ul id="lang-menu" class="lang-menu">
                    @foreach($languages as $key => $lang)
                        @if($key !== $currentLocale)
                            <li>
                                <a href="{{ route('lang.switch', $key) }}">
                                    <span class="lang-code-list">{{ $lang['code'] }}</span>
                                    <span class="lang-name">{{ $lang['name'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <nav class="navbar">
        @php
            $name = strtoupper($entityName);
            $words = explode(' ', $name);
            $last = array_pop($words);
            $firstLine = implode(' ', $words);
        @endphp
        <div class="logo entidad-logo">
            <a href="{{ route('home') }}" class="logo-link">
                <img src="{{ asset($entityLogo) }}" alt="{{ $entityName }}" class="logo-img">
            </a>
        </div>
        <div class="search__login">
            <div class="search-bar">
                <input type="text" placeholder="{{ __('navbar.search_placeholder') }}">
                <button type="submit">
                    <img src="/icons/search.svg" alt="{{ __('navbar.search_placeholder') }}" width="16" height="16">
                </button>
            </div>
            <div class="navbar__login">
                <a href="{{ route('login') }}" class="login__btn">{{ __('navbar.login') }}</a>
            </div>
        </div>
    </nav>

    <div class="navbar-bottom">
        <div class="nav-links">
            @foreach ($menus as $menu)
                <div class="menu-item {{ request()->is($menu->route ?? '') ? 'selected' : '' }}">
                    <a href="{{ url($menu->route ?? '#') }}">{{ __($menu->name) }}</a> @if ($menu->submenus->isNotEmpty())
                        <div class="submenu">
                            @foreach ($menu->submenus as $submenu)
                                <div class="submenu-item {{ request()->is($submenu->route ?? '') ? 'selected' : '' }}">
                                    <a href="{{ url($submenu->route ?? '#') }}">{{ __($submenu->name) }}</a>

                                    @if ($submenu->subsubmenus->isNotEmpty())
                                        <div class="subsubmenu">
                                            @foreach ($submenu->subsubmenus as $subsubmenu)
                                                <a href="{{ url($subsubmenu->link ?? '#') }}">{{ __($subsubmenu->name) }}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</header>

<script>
function toggleLangMenu() {
    document.getElementById('lang-menu').classList.toggle('show');
}

document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.navbar-bottom .menu-item, .navbar-bottom .submenu-item');

    menuItems.forEach(item => {
        const link = item.querySelector(':scope > a');
        const submenu = item.querySelector(':scope > .submenu, :scope > .subsubmenu');

        if (link && submenu) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                
                const parent = item.parentElement;
                parent.querySelectorAll(':scope > .is-open').forEach(openItem => {
                    if (openItem !== item) {
                        openItem.classList.remove('is-open');
                    }
                });

                item.classList.toggle('is-open');
            });
        }
    });

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.navbar-bottom')) {
            document.querySelectorAll('.navbar-bottom .is-open').forEach(item => {
                item.classList.remove('is-open');
            });
        }
        
        if (!event.target.closest('.lang-btn')) {
            var dropdowns = document.getElementsByClassName("lang-menu");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    });
});
</script>

<style scoped>
.navbar-container {
    top: 0;
    width: 100%;
    z-index: 1000;
    font-family: var(--govco-font-primary);
    height: auto; 
}

/* --- BARRA SUPERIOR (ACTUALIZADA) --- */
.navbar-top {
    background-color: var(--govco-secondary-color);
    color: var(--govco-white-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 2rem;
    height: 45px;
}

/* Arreglo del logo enorme */
.navbar-top .govco-logo img {
    max-height: 30px;
    width: auto;
    margin-right: 1rem;
}

.actions {
    display: flex;
    align-items: center;
    gap: 10px;
    height: 100%;
}

/* --- BOTÓN PORTAL HISTÓRICO (BLANCO/AZUL) --- */
.portal-historico__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 16px;
    height: 32px; /* Altura fija */
    color: var(--govco-secondary-color) !important; /* TEXTO AZUL */
    background-color: var(--govco-white-color) !important; /* FONDO BLANCO */
    text-decoration: none;
    border-radius: 20px;
    font-weight: bold;
    white-space: nowrap;
    transition: transform 0.2s ease;
}

.portal-historico__btn:hover {
    transform: scale(1.02);
}

.portal-historico__btn svg {
    fill: var(--govco-secondary-color); /* Icono azul */
    margin-right: 5px;
}

/* --- DROPDOWN IDIOMA (CORREGIDO BLANCO) --- */
.lang-dropdown { 
    position: relative; 
    display: flex;
    align-items: center;
    height: 100%;
}

.lang-btn {
    background-color: transparent !important;
    border: 1px solid rgba(255, 255, 255, 0.5) !important;
    padding: 0 12px;
    height: 32px; /* Igual que el botón histórico */
    cursor: pointer;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #FFFFFF !important; /* FORZADO BLANCO */
    font-family: var(--govco-font-primary);
}

.lang-btn span, .arrow-down {
    color: #FFFFFF !important;
    font-weight: 700;
    font-size: 0.9rem;
}

.lang-btn:hover { 
    background-color: rgba(255, 255, 255, 0.1) !important; 
    border-color: #FFFFFF !important;
}

.lang-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    margin-top: 5px;
    background-color: #FFFFFF;
    min-width: 140px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 2000;
    list-style: none;
    padding: 5px 0;
    border-radius: 4px;
    border: 1px solid #ccc;
}

.lang-menu.show { display: block; }

.lang-menu li a {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    text-decoration: none;
    color: var(--govco-tertiary-color) !important; /* TEXTO OSCURO */
    font-weight: 500;
    font-size: 0.9rem;
    gap: 10px;
}

.lang-menu li a:hover {
    background-color: var(--govco-gray-menu);
    color: var(--govco-secondary-color) !important;
}

.lang-code-list {
    font-weight: 800;
    color: var(--govco-secondary-color);
    min-width: 25px;
}

/* --- EL RESTO DE TUS ESTILOS (INTACTOS) --- */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background-color: var(--govco-white-color);
    border-bottom: 2px solid var(--govco-gray-color);
    font-family: var(--govco-font-family);
}

.logo { flex: 1; }

.search__login {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
    justify-content: flex-end;
}

.search-bar {
    display: flex;
    align-items: center;
    border: 1px solid var(--govco-border-color);
    background-color: var(--govco-white-color);
    border-radius: 4px;
    padding: 0.3rem 0.5rem;
}

.search-bar input {
    border: none;
    outline: none;
    font-size: 0.9rem;
    padding: 0.3rem;
    background-color: transparent;
    color: var(--govco-tertiary-color);
}

.search-bar button {
    background-color: transparent;
    border: none;
    color: var(--govco-primary-color);
    cursor: pointer;
}

.search-bar button:hover { color: var(--govco-highlight-color); }

.navbar__login {
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.login__btn {
    color: var(--govco-white-color);
    text-decoration: none;
    font-size: 0.9rem;
    background-color: var(--govco-secondary-color);
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.login__btn:hover {
    background-color: var(--govco-primary-color);
    color: var(--govco-white-color);
}

.navbar .logo img {
    max-width: 200px;
    height: auto;
}

.navbar-bottom {
    background-color: var(--govco-gray-menu);
    padding: 0 2rem;
    font-family: var(--govco-font-primary);
}

.nav-links {
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.menu-item {
    position: relative;
    display: flex;
    align-items: center;
    height: 100%;
    padding: 0 1rem;
    transition: background-color 0.3s ease;
}

.menu-item > a {
    color: var(--govco-tertiary-color);
    text-decoration: none;
    font-weight: 500;
    font-family: var(--govco-font-primary);
    font-size: 0.8rem;
    padding: 1rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.submenu a, .subsubmenu a {
    color: var(--govco-tertiary-color);
    background-color: var(--govco-gray-menu);
}

.menu-item:has(.submenu) > a::after,
.submenu-item:has(.subsubmenu) > a::after {
    content: ' ▼';
    font-size: 0.6em;
    margin-left: 6px;
    display: inline-block;
    transition: transform 0.2s ease;
}

.menu-item.is-open > a::after,
.submenu-item.is-open > a::after {
    transform: rotate(180deg);
}

.menu-item:hover { background-color: var(--govco-secondary-color); }
.menu-item:hover > a { color: var(--govco-white-color); }

.submenu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background-color: var(--govco-gray-menu);
    border: 1px solid var(--govco-border-color);
    border-radius: var(--govco-border-radius);
    padding: 0.5rem 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 10;
    min-width: 200px;
}

.submenu a {
    display: block;
    padding: 0.5rem 1rem;
    background-color: var(--govco-gray-menu);
    font-family: var(--govco-font-primary, sans-serif); 
    text-decoration: none;
    font-size: 0.9rem;
    transition: background-color 0.3s ease, color 0.3s ease;
    color: #000;
}

.submenu a:hover {
    background-color: var(--govco-secondary-color);
    color: var(--govco-white-color);
    padding: 0.5rem 1rem;
}

.subsubmenu {
    display: none;
    position: absolute;
    top: 0;
    left: 100%;
    width: 225px;
    background-color: var(--govco-gray-menu);
    border: 1px solid var(--govco-border-color);
    border-radius: var(--govco-border-radius);
    padding: 0.5rem 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 20;
}

.submenu-item { position: relative; }

.subsubmenu a {
    display: block;
    padding: 0.5rem 1rem;
    background-color: var(--govco-gray-menu);
    text-decoration: none;
    font-size: 0.9rem;
    transition: background-color 0.3s ease, color 0.3s ease;
    color: #000;
}

.subsubmenu a:hover {
    background-color: var(--govco-secondary-color);
    color: var(--govco-white-color);
}

.submenu-item.active, .submenu-item:hover {
    background-color: var(--govco-secondary-color);
    color: var(--govco-white-color);
}

.submenu-item:hover > a { color: var(--govco-white-color); }

.menu-item.is-open .submenu { display: block; }
.submenu-item.is-open .subsubmenu { display: block; }
.menu-item.selected {
    border: 2px solid var(--govco-success-color);
    border-radius: 4px;
    box-sizing: border-box;
}

/* --- NAVBAR ALTO CONTRASTE --- */
body.high-contrast .navbar-container {
    border-bottom: 1px solid #fff;
}

/* Forzar visibilidad en los botones con fondos fijos */
body.high-contrast .portal-historico__btn {
    background-color: #fff !important;
    color: #000 !important;
    border: 2px solid #000;
}

body.high-contrast .lang-btn {
    border-color: #fff !important;
    background-color: #000 !important;
}

/* Corregir el menú de idiomas que es blanco fijo */
body.high-contrast .lang-menu {
    background-color: #000 !important;
    border: 1px solid #fff !important;
}

body.high-contrast .lang-menu li a {
    color: #fff !important;
}

body.high-contrast .lang-menu li a:hover {
    background-color: #fff !important;
    color: #000 !important;
}

/* Input de búsqueda */
body.high-contrast .search-bar {
    border-color: #fff !important;
    background-color: #000 !important;
}

body.high-contrast .search-bar input {
    color: #fff !important;
}

/* Menu de navegación inferior */
body.high-contrast .navbar-bottom {
    background-color: #000 !important;
    border-bottom: 1px solid #fff;
}

body.high-contrast .menu-item > a {
    color: #fff !important;
}

body.high-contrast .menu-item:hover {
    background-color: #fff !important;
}

body.high-contrast .menu-item:hover > a {
    color: #000 !important;
}

/* Submenús en contraste */
body.high-contrast .submenu, 
body.high-contrast .subsubmenu {
    background-color: #000 !important;
    border: 1px solid #fff !important;
}

body.high-contrast .submenu a, 
body.high-contrast .subsubmenu a {
    color: #fff !important;
    background-color: #000 !important;
}

body.high-contrast .submenu a:hover, 
body.high-contrast .subsubmenu a:hover {
    background-color: #fff !important;
    color: #000 !important;
}

/* Inversión de logos en el Navbar si son oscuros */
body.high-contrast .navbar-top .govco-logo img,
body.high-contrast .navbar .logo img {
    filter: brightness(0) invert(1);
}

body.high-contrast .navbar .logo img {
    filter: brightness(0) invert(1);
}

body.high-contrast .navbar .logo img {
    filter: drop-shadow(0 0 2px white);
}

/* RESPONSIVE */
@media (max-width: 992px) {
    .navbar-top {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        height: auto; /* Altura auto en móvil */
    }

    .actions {
        display: flex;
        flex-direction: column; /* Apilados en móvil */
        width: 100%;
        gap: 0.5rem;
    }

    .portal-historico__btn { width: 100%; }
    .lang-dropdown { width: 100%; }
    .lang-btn { width: 100%; justify-content: space-between; }

    .navbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
    }

    .search__login {
        flex-direction: column;
        width: 100%;
        gap: 0.5rem;
        align-items: stretch;
    }

    .search-bar { width: 100%; box-sizing: border-box; }
    .search-bar input { flex-grow: 1; }
    .navbar__login { width: 100%; }
    .login__btn { width: 100%; text-align: center; box-sizing: border-box; }
    .navbar-bottom { padding: 0.5rem 1rem; }
    .nav-links {
        flex-direction: column; 
        justify-content: flex-start;
        gap: 0;
        align-items: stretch;
    }
    .menu-item { padding: 0; width: 100%; }
    .menu-item > a {
        font-size: 0.9rem;
        padding: 0.8rem 1rem;
        justify-content: space-between;
    }
    .submenu {
        position: static;
        box-shadow: none;
        border: none;
        background-color: rgba(0,0,0,0.05);
        padding: 0.5rem 0;
        border-radius: 0;
        min-width: 100%;
    }
    .submenu a { padding-left: 2rem; }
    .subsubmenu {
        position: static;
        box-shadow: none;
        border: none;
        background-color: rgba(0,0,0,0.1);
        width: 100%;
        padding: 0.5rem 0;
    }
    .subsubmenu a { padding-left: 3rem; }
}

@media (max-width: 768px) {
    .subsubmenu { width: 100%; }
    .entidad-linea-2 { font-size: 16px; letter-spacing: 0.2em; }
    .entidad-linea-1 { font-size: 12px; }
    .logo-img { height: 40px; }
}
</style>