<script>
    const defaultFontSize = 16;
    let currentFontSize = parseInt(localStorage.getItem('fontSize')) || defaultFontSize;
    const contrastClass = 'high-contrast';

    document.addEventListener('DOMContentLoaded', () => {
        document.documentElement.style.fontSize = currentFontSize + 'px';
        if (localStorage.getItem('contrast') === 'true') {
            document.body.classList.add(contrastClass);
        }
    });

    function updateFont() {
        document.documentElement.style.fontSize = currentFontSize + 'px';
        localStorage.setItem('fontSize', currentFontSize);
    }

    function increaseFontSize() {
        if (currentFontSize < 30) {
            currentFontSize += 2;
            updateFont();
        }
    }

    function decreaseFontSize() {
        if (currentFontSize > 12) {
            currentFontSize -= 2;
            updateFont();
        }
    }

    function toggleContrast() {
        const isHighContrast = document.body.classList.toggle(contrastClass);
        localStorage.setItem('contrast', isHighContrast);
    }
</script>

<div class="accessibility-bar" role="toolbar" aria-label="Herramientas de accesibilidad">
    <div class="btns">
        <button class="btn-accessibility" onclick="increaseFontSize()" title="Aumentar tamaño de letra">
            <img src="{{ asset('/icons/font-maximize.svg') }}" alt="" class="icon">
            <span class="text">Aumentar fuente</span>
        </button>
    </div>

    <div class="btns">
        <button class="btn-accessibility" onclick="decreaseFontSize()" title="Disminuir tamaño de letra">
            <img src="{{ asset('/icons/font-minimize.svg') }}" alt="" class="icon">
            <span class="text">Disminuir fuente</span>
        </button>
    </div>
    
    <div class="btns">
        <button class="btn-accessibility" onclick="toggleContrast()" title="Cambiar contraste">
            <img src="{{ asset('/icons/adjust.svg') }}" alt="" class="icon">
            <span class="text">Contraste</span>
        </button>
    </div>

    <div class="btns">
        <a href="https://ticsinbarreras.mintic.gov.co/791/w3-propertyvalue-339742.html" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="btn-accessibility" 
           title="Centro de Relevo (Abre en ventana nueva)">
            <img src="{{ asset('/icons/relevo.svg') }}" alt="" class="icon">
            <span class="text">Centro de Relevo</span>
        </a>
    </div>
</div>

<style>
.accessibility-bar {
    position: fixed;
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: 8px;
    background-color: var(--govco-secondary-color);
    padding: 8px;
    border-radius: 8px 0 0 8px;
    z-index: 10000;
    box-shadow: -2px 0 10px rgba(0,0,0,0.2);
}

.btns {
    position: relative;
    display: flex;
    justify-content: center;
}

.btn-accessibility {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background-color: var(--govco-white-color);
    border: 2px solid transparent;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 6px;
    text-decoration: none; /* Para el link de relevo */
}

.btn-accessibility:hover {
    background-color: var(--govco-primary-color);
    transform: translateX(-4px);
}

.btn-accessibility:focus {
    outline: 2px solid var(--govco-accent-color);
    outline-offset: 2px;
}

.icon {
    width: 20px;
    height: 20px;
    object-fit: contain;
    transition: filter 0.3s ease;
    /* Filtro para que el icono sea azul en el botón blanco */
    filter: brightness(0) saturate(100%) invert(26%) sepia(86%) saturate(1931%) hue-rotate(203deg) brightness(91%) contrast(101%);
}

.btn-accessibility:hover .icon {
    /* Icono blanco en hover */
    filter: brightness(0) invert(1);
}

.text {
    position: absolute;
    right: calc(100% + 12px);
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    visibility: hidden;
    white-space: nowrap;
    background-color: var(--govco-primary-color);
    color: #fff;
    border-radius: 4px;
    font-size: 14px;
    font-family: var(--govco-font-primary);
    padding: 6px 12px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.btns:hover .text {
    opacity: 1;
    visibility: visible;
}

/* --- AJUSTES PARA ALTO CONTRASTE --- */
body.high-contrast .accessibility-bar {
    background-color: #000;
    border: 1px solid #fff;
    border-right: none;
}

body.high-contrast .btn-accessibility {
    background-color: #000;
    border-color: #fff;
}

body.high-contrast .btn-accessibility:hover {
    background-color: #fff;
}

body.high-contrast .icon {
    /* Icono blanco sobre botón negro */
    filter: brightness(0) invert(1);
}

body.high-contrast .btn-accessibility:hover .icon {
    /* Icono negro sobre botón blanco en hover */
    filter: brightness(0);
}

body.high-contrast .text {
    background-color: #fff;
    color: #000;
    border: 1px solid #000;
}
</style>