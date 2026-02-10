<div class="govco-container calls-section">
    <h2 class="section-title">Convocatorias Vigentes</h2>

    <div class="carousel-wrapper">
        <button class="nav-btn prev" onclick="scrollCarousel(-1)" aria-label="Anterior">
            &#8249;
        </button>

        <div class="cards-track" id="trackCarousel">
            @if($publications->isNotEmpty())
                @foreach($publications as $call)
                    @php
                        $fullTitle = $call->title ?? 'Sin título';
                        $limit = 90;
                        $shortTitle = \Illuminate\Support\Str::limit($fullTitle, $limit, '...');
                    @endphp

                    <div class="card-item">
                        <article class="doc-card">
                            <div class="card-header-blue">
                                <span class="watermark">DOC</span>
                                <span class="date-badge">
                                    {{ \Carbon\Carbon::parse($call->date)->format('d M Y') }}
                                </span>
                            </div>

                            <div class="card-body">
                                <h3 class="card-title" title="{{ $fullTitle }}">
                                    {{ $shortTitle }}
                                </h3>

                                @if($call->date_start && $call->date_end)
                                    <p class="card-dates">
                                        Vigencia: {{ \Carbon\Carbon::parse($call->date_start)->format('d/m') }} - {{ \Carbon\Carbon::parse($call->date_end)->format('d/m') }}
                                    </p>
                                @endif
                            </div>

                            <div class="card-footer">
                                <a href="{{ route('publications.show', ['id' => $call->id, 'type' => 4]) }}" class="link-action">
                                    Ver detalles &rarr;
                                </a>
                                @if(!empty($call->document))
                                    <a href="{{ Storage::url($call->document) }}" target="_blank" class="download-icon" title="Descargar">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    </a>
                                @endif
                            </div>
                        </article>
                    </div>
                @endforeach
            @else
                <p class="text-center w-100 no-calls-msg">No hay convocatorias disponibles.</p>
            @endif
        </div>

        <button class="nav-btn next" onclick="scrollCarousel(1)" aria-label="Siguiente">
            &#8250;
        </button>
    </div>
</div>

<script>
function scrollCarousel(direction) {
    const container = document.getElementById('trackCarousel');
    const scrollAmount = 324; 
    container.scrollBy({
        left: direction * scrollAmount,
        behavior: 'smooth'
    });
}
</script>

<style>
.calls-section {
    max-width: 1240px;
    margin: 2rem auto;
    padding: 0 1rem;
    font-family: var(--govco-font-primary), sans-serif;
    position: relative;
}

.section-title {
    text-align: center;
    color: var(--govco-secondary-color);
    font-weight: 800;
    font-size: 2rem;
    margin-bottom: 2rem;
}

.carousel-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.cards-track {
    display: flex;
    gap: 24px;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding: 10px 5px 20px 5px; 
    scrollbar-width: none; 
    -ms-overflow-style: none;
}
.cards-track::-webkit-scrollbar {
    display: none;
}

.card-item {
    min-width: 300px;
    max-width: 300px;
    flex: 0 0 auto;
}

.doc-card {
    background: var(--govco-white-color);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--govco-box-shadow);
    border: 1px solid var(--govco-gray-color);
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 380px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.doc-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.12);
}

.card-header-blue {
    background: var(--govco-secondary-color);
    height: 140px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.watermark {
    font-size: 3rem;
    font-weight: 900;
    color: rgba(255,255,255,0.15);
    user-select: none;
}

.date-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: var(--govco-white-color);
    color: var(--govco-secondary-color);
    font-size: 0.75rem;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 20px;
}

.card-body {
    padding: 1.5rem;
    flex: 1;
}

.card-title {
    font-size: 1rem;
    color: var(--govco-tertiary-color);
    font-weight: 700;
    line-height: 1.4;
    margin-bottom: 1rem;
}

.card-dates {
    font-size: 0.85rem;
    color: var(--govco-border-color);
    margin-top: 0.5rem;
}

.card-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--govco-gray-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.link-action {
    color: var(--govco-primary-color);
    font-weight: 700;
    text-decoration: none;
    font-size: 0.9rem;
}

.download-icon {
    color: var(--govco-border-color);
    transition: color 0.2s;
}
.download-icon:hover {
    color: var(--govco-primary-color);
}

.nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: var(--govco-white-color);
    border: 1px solid var(--govco-border-color);
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    cursor: pointer;
    z-index: 10;
    font-size: 2rem;
    line-height: 0;
    color: var(--govco-secondary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    padding-bottom: 4px;
    transition: all 0.2s;
}

.nav-btn:hover {
    background: var(--govco-secondary-color);
    color: var(--govco-white-color);
    border-color: var(--govco-secondary-color);
}

.nav-btn.prev { left: -20px; }
.nav-btn.next { right: -20px; }

body.high-contrast .no-calls-msg {
    color: #FFF !important;
}

body.high-contrast .doc-card {
    background: #000 !important;
    border: 2px solid #FFF !important;
}

body.high-contrast .card-header-blue {
    background: #000 !important;
    border-bottom: 1px solid #FFF;
}

body.high-contrast .date-badge {
    background: #FFF !important;
    color: #000 !important;
}

body.high-contrast .card-title, 
body.high-contrast .card-dates,
body.high-contrast .link-action {
    color: #FFF !important;
}

body.high-contrast .card-footer {
    border-top: 1px solid #FFF;
}

body.high-contrast .download-icon {
    color: #FFF !important;
}

body.high-contrast .nav-btn {
    background: #000 !important;
    color: #FFF !important;
    border: 2px solid #FFF !important;
}

body.high-contrast .nav-btn:hover {
    background: #FFF !important;
    color: #000 !important;
}

@media(max-width: 768px) {
    .nav-btn.prev { left: 0; }
    .nav-btn.next { right: 0; }
}
</style>