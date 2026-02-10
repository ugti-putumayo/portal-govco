<section class="tramites-section">
    <div class="tramites-content text-center">
        <div class="tabs">
            <button class="tab active" onclick="showTab('tramites', this)">Trámites y Servicios</button>
            <button class="tab inactive" onclick="showTab('atencion', this)">Atención al Ciudadano</button>
        </div>

        <div id="tab-tramites" class="tramites-grid tab-content active">
            @foreach ($tramiteServices as $service)
                <a href="{{ $service->url ?? '#' }}" class="tramite-item" target="_blank">
                    <div class="tramite-icon">
                        <img src="{{ asset('icon/' . $service->icon) }}" alt="{{ $service->title }}">
                    </div>
                    <p class="tramite-title">{!! nl2br(e($service->title)) !!}</p>
                </a>
            @endforeach
        </div>

        <div id="tab-atencion" class="tramites-grid tab-content">
            @foreach ($citizenServices as $service)
                <a href="{{ $service->url ?? '#' }}" class="tramite-item" target="_blank">
                    <div class="tramite-icon">
                        <img src="{{ asset('icon/' . $service->icon) }}" alt="{{ $service->title }}">
                    </div>
                    <p class="tramite-title">{!! nl2br(e($service->title)) !!}</p>
                </a>
            @endforeach
        </div>
        <a href="#" class="tramites-button">Ver más trámites</a>
    </div>
    <div class="curve-container">
        <svg viewBox="0 0 1440 180" preserveAspectRatio="none">
            <path d="M0,150 C480,220 960,80 1440,150 L1440,180 L0,180 Z"></path>
        </svg>
    </div>
</section>

<style>
.tramites-section {
    background-color: var(--govco-secondary-color);
    position: relative;
    overflow: hidden;
    transition: background-color 0.3s ease;
}

.tramites-content {
    position: relative;
    z-index: 2;
    padding: 4rem 1rem 12rem;
}

.tabs {
    display: flex;
    justify-content: center;
    margin-bottom: 1.5rem;
}

.tab {
    font-weight: bold;
    font-size: 1.1rem;
    padding: 0.6rem 1.8rem;
    margin: 0 0.5rem;
    border-radius: 4px 4px 0 0;
    border: none;
    transition: all 0.3s ease;
    color: var(--govco-white-color);
    font-family: var(--govco-font-primary);
    background-color: transparent;
}

.tab.active {
    background-color: var(--govco-white-color);
    color: var(--govco-secondary-color) !important;
    border-bottom: 4px solid var(--govco-fourth-color);
}

.tab.inactive {
    cursor: pointer;
    border-bottom: 4px solid transparent;
}

.tramites-grid {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 3rem 4rem;
    margin-bottom: 2rem;
}

.tramite-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    text-decoration: none;
    max-width: 160px;
    transition: transform 0.3s ease;
}

.tramite-item:hover {
    transform: translateY(-8px);
}

.tramite-icon {
    background-color: var(--govco-white-color);
    border-radius: 50%;
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.tramite-icon img {
    width: 50px;
    height: 50px;
    object-fit: contain;
    filter: brightness(0) saturate(100%) invert(18%) sepia(88%) saturate(1917%) hue-rotate(201deg) brightness(91%) contrast(101%);
}

body.high-contrast .tramite-icon {
    background-color: #000 !important;
    border: 2px solid #fff !important;
}

body.high-contrast .tramite-icon img {
    filter: brightness(0) invert(1) !important;
}

body.high-contrast .tab.active {
    background-color: #FFF !important;
    color: #000 !important;
    border-bottom: 4px solid #FFFF00 !important;
}

body.high-contrast .tab.inactive {
    background-color: #000 !important;
    color: #FFF !important;
    border: 1px solid #FFF !important;
}

.tramite-title {
    font-weight: bold;
    font-size: 0.95rem;
    color: var(--govco-white-color);
    font-family: var(--govco-font-secondary);
}

.tramites-button {
    display: inline-block;
    padding: 0.6rem 1.5rem;
    border: 2px solid var(--govco-fourth-color);
    color: var(--govco-white-color);
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-bottom: 4rem;
    background-color: transparent;
}

.tramites-button:hover {
    background-color: var(--govco-white-color);
    color: var(--govco-secondary-color) !important;
}

body.high-contrast .tramites-button {
    border-color: #fff !important;
    color: #fff !important;
}

.curve-container {
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 180px;
    z-index: 1;
    pointer-events: none;
}

.curve-container svg {
    width: 100%;
    height: 100%;
    display: block;
}

.curve-container svg path {
    fill: var(--govco-white-color);
    transition: fill 0.3s ease;
}

.tab-content { display: none; }
.tab-content.active { display: flex; }

@media (max-width: 992px) {
    .tramites-content { padding: 3rem 1rem 10rem; }
    .tramite-icon { width: 80px; height: 80px; }
    .curve-container { height: 100px; }
}

@media (max-width: 576px) {
    .tramite-item { max-width: 80px; }
    .tramite-icon { width: 65px; height: 65px; }
    .curve-container { height: 80px; }
    .tabs { flex-wrap: wrap; gap: 0.5rem; }
}
</style>