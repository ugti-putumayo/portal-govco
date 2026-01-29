<script>
    const defaultFontSize = 16;
    let currentFontSize = defaultFontSize;
    const contrastClass = 'high-contrast';

    function increaseFontSize() {
        currentFontSize += 2;
        document.documentElement.style.fontSize = currentFontSize + 'px';
    }

    function decreaseFontSize() {
        currentFontSize = Math.max(12, currentFontSize - 2);
        document.documentElement.style.fontSize = currentFontSize + 'px';
    }

    function toggleContrast() {
        document.body.classList.toggle(contrastClass);
    }
</script>

<div class="accessibility-bar">
    <div class="btns btn__fontMax">
        <button class="btn-accessibility" onclick="increaseFontSize()">
            <img src="{{ asset('/icons/font-maximize.svg') }}" alt="Aumentar fuente" class="icon">
            <span class="text">Aumentar fuente</span>
        </button>
    </div>

    <div class="btns btn__fontMin">
        <button class="btn-accessibility" onclick="decreaseFontSize()">
            <img src="{{ asset('/icons/font-minimize.svg') }}" alt="Disminuir fuente" class="icon">
            <span class="text">Disminuir fuente</span>
        </button>
    </div>
    
    <div class="btns btn__access">
        <button class="btn-accessibility" onclick="toggleContrast()">
            <img src="{{ asset('/icons/adjust.svg') }}" alt="Contraste" class="icon">
            <span class="text">Contraste</span>
        </button>
    </div>
</div>

<style>
/* Contenedor principal */
.accessibility-bar {
    position: fixed;
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: 10px;
    background-color: var(--govco-secondary-color);
    padding: 10px;
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
    z-index: 9999;
}

.btns {
    position: relative;
}

.btn-accessibility {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px; /* Ancho fijo */
    height: 30px;
    background-color: var(--govco-white-color);
    color: var(--govco-secondary-color);
    border: none;
    border-radius: 8px;
    padding: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-accessibility:hover {
    background-color: var(--govco-primary-color);
    color: var(--govco-white-color);
}

.btn-accessibility:hover .icon {
    filter: brightness(0) invert(1); /* Icono blanco */
}

.icon {
    width: 16px;
    height: 16px;
    transition: transform 0.3s ease;
}

.text {
    position: absolute;
    right: calc(100% + 10px); /* Ubica el texto a la izquierda del botón */
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    visibility: hidden;
    white-space: nowrap;
    background-color: var(--govco-primary-color);
    color: #fff;
    border-radius: 5px;
    font-size: 0.8rem;
    font-family: var(--govco-font-primary);
    padding: 5px 10px;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.btn-accessibility:hover .text {
    opacity: 1;
    visibility: visible;
}
</style>