@extends('public.transparency.shared.sidebar')
@section('sidebar')
  @include('partials.sidebar')
@endsection

@section('main-content')
@php
  $sections = collect(isset($pages) ? $pages : (isset($page) ? [$page] : []));

  $slides = $sections->flatMap(function($p){
    return $p->items->take(6)->map(function($it) use ($p){
      return [
        'page_title' => $p->title,
        'bg'         => $p->image ? Storage::url($p->image) : null,
        'title'      => $it->title,
        'desc'       => (string) $it->description,
        'url'        => $it->url,
      ];
    });
  })->values();

  $firstMeta = (optional($sections->first())->meta) ?? [];
  $kpis = [
    ['label' => 'Conjuntos de indicadores', 'value' => data_get($firstMeta,'sets') ?? $sections->count() ],
    ['label' => 'Dimensiones',               'value' => data_get($firstMeta,'dimensions') ?? null],
    ['label' => 'Temáticas',                 'value' => data_get($firstMeta,'topics') ?? $sections->count()],
    ['label' => 'Indicadores',               'value' => data_get($firstMeta,'indicators') ?? $sections->sum(fn($p)=>$p->items->count())],
  ];
@endphp

<div class="govco-container">

  @if($slides->isNotEmpty())
    <div class="hero-slider" id="heroSlider" aria-roledescription="carousel">
      @foreach($slides as $i => $s)
        <div
          class="slide {{ $i === 0 ? 'active' : '' }}"
          id="slide-{{ $i }}"
          data-index="{{ $i }}"
          role="group"
          aria-roledescription="slide"
          aria-label="{{ $i+1 }} de {{ $slides->count() }}"
        >
          <div class="slide-media">
            <img
              class="slide-img"
              src="{{ $i === 0 ? ($s['bg'] ?? asset('images/placeholder-hero.jpg')) : asset('images/placeholder-hero.jpg') }}"
              data-src="{{ $s['bg'] ?? asset('images/placeholder-hero.jpg') }}"
              alt="{{ $s['page_title'] }} — {{ $s['title'] }}"
              loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
              decoding="async"
            />
            <div class="slide-overlay"></div>
          </div>

          <div class="slide-content">
            <div class="tile">
              <div class="tile-eyebrow">{{ $s['page_title'] }}</div>
              <div class="tile-title">{{ $s['title'] }}</div>
              <div class="tile-value">{!! nl2br(e((string)($s['desc'] ?? '—'))) !!}</div>
              @if($s['url'])
                <a class="tile-link" href="{{ $s['url'] }}" target="_blank" rel="noopener">Ver fuente</a>
              @endif
            </div>
          </div>
          <div class="slide-caption">{{ $s['page_title'] }}</div>
        </div>
      @endforeach

      <button class="nav prev" aria-label="Anterior" type="button"><span>‹</span></button>
      <button class="nav next" aria-label="Siguiente" type="button"><span>›</span></button>

      <div class="dots" role="tablist" aria-label="Paginación">
        @foreach($slides as $i => $_)
          <button role="tab" aria-selected="{{ $i===0 ? 'true':'false' }}" aria-controls="slide-{{ $i }}" data-i="{{ $i }}" class="{{ $i===0?'on':'' }}" type="button"></button>
        @endforeach
      </div>
    </div>
  @endif

  <section class="about">
    <h2>Gestión de Estadística</h2>
    <p>
      La información estadística en las entidades públicas del orden nacional y territorial ejerce un papel principal en la toma de decisiones y el diseño de estrategias y planes institucionales. 
      Provee herramientas poderosas para proyectar escenarios y realizar pronósticos acertados sobre el comportamiento de los activos que actúan como fuentes de información, 
      de modo que los grupos de valor expresen sus intereses y se anticipe su posible reacción frente a situaciones específicas.
    </p>
  </section>

  <section class="kpi-bar">
    @foreach($kpis as $k)
      @if(!is_null($k['value']))
      <div class="kpi">
        <div class="kpi-value" data-target="{{ (int) $k['value'] }}">0</div>
        <div class="kpi-label">{{ $k['label'] }}</div>
      </div>
      @endif
    @endforeach
  </section>

  @forelse($sections as $p)
    @php
      $slug = Str::slug($p->title);
      $items = $p->items ?? collect();
      $pagesForSection = $items->chunk(12)->values();
      $totalPages = $pagesForSection->count();
    @endphp

    <details class="section-block" id="sec-{{ $slug }}" data-section="{{ $slug }}">
      <summary class="section-summary">
        <span class="title">{{ $p->title }}</span>
        <span class="badge">{{ $items->count() }}</span>
      </summary>

      @if($p->image)
        <img
          src="{{ Storage::url($p->image) }}"
          class="section-image"
          alt="{{ $p->title }}"
          loading="lazy"
          decoding="async"
        >
      @endif

      @if($items->isNotEmpty())
        <div class="section-pages" data-total="{{ $totalPages }}">
          @foreach($pagesForSection as $pageIndex => $chunk)
            <div class="cards page {{ $pageIndex === 0 ? 'page-active' : '' }}" data-page="{{ $pageIndex+1 }}">
              @foreach($chunk as $it)
                <a class="card card--bubble" href="{{ $it->url ?? '#' }}" @if($it->url) target="_blank" rel="noopener" @endif>
                  @if($it->image)
                    <div class="card-icon">
                      <img src="{{ Storage::url($it->image) }}" alt="{{ $it->title }}" loading="lazy" decoding="async">
                    </div>
                  @endif
                  <div class="card-body">
                    <div class="card-title">{{ $it->title }}</div>
                    @if($it->description)
                      @php
                        $desc = (string) $it->description;
                        // normaliza: si llegaran "\n" literales, conviértelos a saltos reales
                        $desc = preg_replace('/\\\\r\\\\n|\\\\n|\\\\r/', "\n", $desc);
                      @endphp
                      <p class="card-desc">{!! nl2br(e($desc)) !!}</p>
                    @endif

                    @php
                      $value = data_get($it->extra,'value');
                      $unit  = data_get($it->extra,'unit');
                      $year  = data_get($it->extra,'year');
                    @endphp
                    @php
                      $raw = (string) data_get($it->extra,'value');
                      $valueLines = preg_split('/\r\n|\r|\n/', $raw);
                    @endphp

                    @if(($raw || $unit || $year))
                      <div class="pill">
                        @foreach($valueLines as $line)
                          <span class="pill-line">{{ $line }}</span>
                        @endforeach
                        @if($unit) <span class="unit">{{ $unit }}</span> @endif
                        @if($year) <span class="year">• {{ $year }}</span> @endif
                      </div>
                    @endif
                    @if($it->document)
                      <div class="doc"><a href="{{ Storage::url($it->document) }}" target="_blank" rel="noopener">Documento</a></div>
                    @endif
                  </div>
                </a>
              @endforeach
            </div>
          @endforeach
        </div>

        @if($totalPages > 1)
        <nav class="pager" aria-label="Paginación {{ $p->title }}">
          <button class="pg prev" type="button" data-action="prev" data-section="{{ $slug }}" disabled>Anterior</button>
          <div class="pg-numbers">
            @for($n=1; $n <= $totalPages; $n++)
              <button class="pg-num {{ $n===1 ? 'active' : '' }}" type="button" data-section="{{ $slug }}" data-page="{{ $n }}">{{ $n }}</button>
            @endfor
          </div>
          <button class="pg next" type="button" data-action="next" data-section="{{ $slug }}">Siguiente</button>
        </nav>
        @endif
      @else
        <div class="govco-empty">No hay elementos en {{ $p->title }}.</div>
      @endif
    </details>
  @empty
    <div class="govco-empty">No hay secciones para mostrar.</div>
  @endforelse

  <section class="links-cta">
    <div class="links links-pro">
      <div class="links-col links-cards">
        <h3>Demográficos</h3>
        <ul class="list-unstyled">
          <li>
            <a class="link-item" href="https://www.dane.gov.co/index.php/estadisticas-por-tema/demografia-y-poblacion/censo-nacional-de-poblacion-y-vivenda-2018/cuantos-somos" target="_blank" rel="noopener">
              <span class="link-icon">📊</span>
              <span class="link-text">
                <span class="link-title">Perfiles censo nacional y vivienda 2018 (DANE)</span>
                <small class="link-meta">dane.gov.co</small>
              </span>
              <span class="link-ext">↗</span>
            </a>
          </li>
          <li>
            <a class="link-item" href="https://www.dane.gov.co/index.php/estadisticas-por-tema/demografia-y-poblacion/discapacidad" target="_blank" rel="noopener">
              <span class="link-icon">🧩</span>
              <span class="link-text">
                <span class="link-title">Discapacidad - Datos departamentales (DANE)</span>
                <small class="link-meta">dane.gov.co</small>
              </span>
              <span class="link-ext">↗</span>
            </a>
          </li>
          <li>
            <a class="link-item" href="https://www.dane.gov.co/index.php/estadisticas-por-tema/demografia-y-poblacion/proyecciones-de-poblacion" target="_blank" rel="noopener">
              <span class="link-icon">👥</span>
              <span class="link-text">
                <span class="link-title">Proyecciones de Población 1950-2017 y 2018-2070 con base en el CNPV (DANE)</span>
                <small class="link-meta">dane.gov.co</small>
              </span>
              <span class="link-ext">↗</span>
            </a>
          </li>
          <li>
            <a class="link-item" href="https://geoportal.dane.gov.co/geovisores/sociedad/autorreconocimiento-etnico/" target="_blank" rel="noopener">
              <span class="link-icon">🗺️</span>
              <span class="link-text">
                <span class="link-title">Mapa étnico de Colombia (DANE)</span>
                <small class="link-meta">dane.gov.co</small>
              </span>
              <span class="link-ext">↗</span>
            </a>
          </li>
        </ul>
      </div>
      <div class="cta pro-cta">
        <h3>¿No encontraste el dato que buscabas?</h3>
        <p>Cuéntanos qué información deberíamos incluir en esta herramienta.</p>
        <a 
          class="govco-btn btn-primary" 
          href="https://docs.google.com/forms/d/e/1FAIpQLSf0manVZnPJr3yY1gCG6x_UYFRtyTYHrm3xNS2n8bWrYyD1vw/viewform?usp=header" 
          target="_blank" 
          rel="noopener"
        >
          Contáctenos
        </a>
      </div>
    </div>
  </section>
</div>

<div class="modal" id="contactModal" aria-hidden="true">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="contactTitle">
    <div class="modal-header">
      <h3 id="contactTitle">Sugerir datos</h3>
      <button type="button" class="close" id="closeContact" aria-label="Cerrar">×</button>
    </div>
    <form method="POST" action="{{ route('transparency.statisticals') }}">
      @csrf
      <div class="modal-body">
        <label class="fld">
          <span>Nombre</span>
          <input type="text" name="name" required>
        </label>
        <label class="fld">
          <span>Correo</span>
          <input type="email" name="email" required>
        </label>
        <label class="fld">
          <span>Mensaje</span>
          <textarea name="message" rows="4" required placeholder="¿Qué dato te gustaría ver aquí?"></textarea>
        </label>
      </div>
      <div class="modal-footer">
        <button type="button" class="govco-btn btn-ghost" id="cancelContact">Cancelar</button>
        <button type="submit" class="govco-btn btn-primary">Enviar</button>
      </div>
    </form>
  </div>
</div>
@endsection

<style>
  .govco-container{padding:1rem}

  /* === HERO SLIDER REDISEÑADO === */
  .hero-slider {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 1.5rem;
    background: #0d1726;
    isolation: isolate;
  }

  .slide { display: none; position: relative; }
  .slide.active { display: block; }

  .slide-media {
    position: relative;
  }
  .slide-img {
    width: 100%;
    height: clamp(300px, 42vw, 560px);
    object-fit: cover;
    object-position: center;
    background: #0d1726;
  }
  .slide-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(0,0,0,.7) 0%, rgba(0,0,0,.45) 40%, rgba(0,0,0,.1) 100%);
    z-index: 0;
  }

  /* Panel lateral */
  .slide-content {
    position: absolute;
    inset: 0;
    z-index: 1;
    display: flex;
    align-items: center;
    padding: 2rem;
  }

  .tile {
    max-width: 460px;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(14px);
    border-radius: 20px;
    padding: 1.5rem;
    color: #fff;
    box-shadow: 0 10px 40px rgba(0,0,0,.35);
    border: 1px solid rgba(255,255,255,.2);
  }

  .tile .tile-eyebrow {
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: .75;
    margin-bottom: .35rem;
  }

  .tile-title {
    font-weight: 900;
    font-size: clamp(1.5rem, 2.8vw, 2.2rem);
    line-height: 1.2;
    margin-bottom: 1rem;
  }

  .tile-value {
    white-space: pre-line;
    font-size: 1.1rem;
    line-height: 1.45;
    margin-bottom: 1.2rem;
  }

  .tile-link {
    display: inline-block;
    padding: .6rem 1.1rem;
    border-radius: 12px;
    background: #fff;
    color: #0b2c6f;
    font-weight: 700;
    text-decoration: none;
    transition: background .2s ease;
  }
  .tile-link:hover {
    background: #dbe3ff;
  }

  /* Caption arriba */
  .slide-caption {
    position: absolute;
    top: .8rem; left: .9rem;
    z-index: 2;
    background: #1c3f88;
    padding: .3rem .75rem;
    font-weight: 700;
    font-size: .85rem;
    color: #fff;
    border-radius: 8px;
  }

  /* Flechas */
  .hero-slider .nav {
    position: absolute; top: 50%;
    transform: translateY(-50%);
    z-index: 3;
    border: 0; width: 44px; height: 44px;
    border-radius: 50%; cursor: pointer;
    display: grid; place-items: center;
    box-shadow: 0 4px 12px rgba(0,0,0,.35);
    background: rgba(0,0,0,.55) !important;
    color: #fff !important;
  }
  .hero-slider .nav:hover { background: rgba(0,0,0,.8) !important; }
  .hero-slider .prev { left: .6rem; }
  .hero-slider .next { right: .6rem; }

  /* Dots */
  .hero-slider .dots {
    position: absolute; bottom: 1rem; left: 0; right: 0;
    display: flex; gap: .4rem; justify-content: center;
    z-index: 2;
  }
  .hero-slider .dots button {
    width: 12px; height: 12px;
    border-radius: 50%; border: 0;
    background: rgba(255,255,255,.5);
    cursor: pointer;
  }
  .hero-slider .dots button.on {
    background: #fff;
    transform: scale(1.15);
  }

  /* Descriptivo (rediseño) */
  .about{
    position: relative;
    margin: 1.1rem 0;
    padding: 1.25rem 1.35rem;
    border: 0; 
    border-radius: 16px;
    background: linear-gradient(180deg,#ffffff,#f7f9ff);
    box-shadow: 0 10px 28px rgba(18,38,63,.08);
    overflow: hidden;
  }

  /* franja de acento a la izquierda */
  .about::before{
    content:"";
    position:absolute; inset:0 auto 0 0;
    width:6px; border-radius:16px 0 0 16px;
    background: linear-gradient(180deg, var(--govco-secondary-color), #8fd3fe);
    opacity:.9;
  }

  /* highlight suave en la esquina superior izquierda */
  .about::after{
    content:"";
    position:absolute; left:-40px; top:-40px;
    width:160px; height:160px; pointer-events:none;
    background: radial-gradient(closest-side, rgba(125,177,255,.25), transparent 70%);
  }

  .about h2{
    margin: 0 0 .45rem;
    color: #123a6b;
    font-weight: 900;
    letter-spacing: .2px;
  }

  .about p{
    margin: 0;
    color: #3b4457;
    line-height: 1.55;
    max-width: 90ch;
  }

  /* === KPIs (franja única, sin tarjetas anidadas) === */
  .kpi-bar{
    position: relative;
    display: grid;
    grid-template-columns: repeat(2, minmax(0,1fr));
    margin: 1.1rem 0;
    border-radius: 16px;
    overflow: hidden;
    padding: .4rem 0;
    background: linear-gradient(180deg,#ffffff,#f6f8ff);
    border: 1px solid #e7ecf8;
    box-shadow: 0 10px 28px rgba(18,38,63,.08);
  }

  .kpi{
    position: relative;
    text-align: center;
    padding: .9rem .8rem;
    background: transparent;
  }

  /* separadores sutiles entre columnas */
  .kpi:not(:last-child)::after{
    content:"";
    position: absolute;
    right: 0; top: 18%; bottom: 18%;
    width: 1px;
    background: #e6ebf7;
    opacity: .9;
  }

  /* número protagonista */
  .kpi-value{
    font-size: clamp(1.4rem, 3.2vw, 2rem);
    font-weight: 900;
    color: var(--govco-secondary-color);
    line-height: 1;
    font-variant-numeric: tabular-nums;
    will-change: contents;
  }

  /* etiqueta compacta */
  .kpi-label{
    margin-top: .35rem;
    color: #3b4457;
    font-weight: 700;
    letter-spacing: .2px;
  }

  /* highlight suave arriba */
  .kpi-bar::before{
    content:"";
    position:absolute; left:-40px; top:-40px;
    width:180px; height:180px; pointer-events:none;
    background: radial-gradient(closest-side, rgba(125,177,255,.22), transparent 70%);
  }

  /* responsivo */
  @media (min-width: 700px){
    .kpi-bar{ grid-template-columns: repeat(4, minmax(0,1fr)); }
  }

  /* toque sutil al pasar el mouse (opcional) */
  .kpi:hover .kpi-value{ transform: translateY(-1px); }

  /* Secciones */
  .section-block{margin:1.2rem 0;border:1px solid #e6e6e6;border-radius:12px;overflow:hidden;background:#fff}
  .section-summary{display:flex;align-items:center;justify-content:space-between;gap:.5rem;padding:.8rem 1rem;cursor:pointer;list-style:none}
  .section-summary .title{font-weight:800;color:var(--govco-secondary-color)}
  .section-summary .badge{background:var(--govco-gray-color);border-radius:999px;padding:.1rem .5rem;font-weight:700;color:#333}
  .section-block[open] .section-summary{border-bottom:1px solid #eee}
  .section-image{width:100%;max-height:260px;object-fit:cover;margin:.5rem 0 1rem}

  .section-pages{padding:0 1rem 1rem}
  .page{display:none}
  .page-active{display:grid}
  .cards{gap:1rem;grid-template-columns:1fr}
  @media(min-width:700px){.cards{grid-template-columns:repeat(2,1fr)}}
  @media(min-width:1100px){.cards{grid-template-columns:repeat(3,1fr)}}
  .card{display:flex;gap:.8rem;align-items:flex-start;background:#fff;border:1px solid #e6e6e6;
        border-radius:20px;padding:.8rem .9rem;box-shadow:0 2px 8px rgba(0,0,0,.05);transition:transform .06s ease}
  .card:hover{transform:translateY(-1px)}
  .card img{width:64px;height:64px;object-fit:contain;border-radius:14px;background:#f7f9ff;border:1px solid #eef2ff}
  .card-body{flex:1}
  .card-title{color:var(--govco-secondary-color);font-weight:800;margin:0 0 .25rem;line-height:1.2}
  .card-desc{margin:.15rem 0 .3rem;color:#444}
  .pill{display:inline-block;background:var(--govco-gray-color);border-radius:999px;padding:.2rem .6rem;font-weight:700;font-size:.85rem;color:#333}
  .doc a{font-weight:700}
  .pill-line { display:block; line-height:1.35; }
  a.card { text-decoration:none !important; white-space:normal !important; }

  /* Paginación (forzado de colores) */
  .pager{display:flex;align-items:center;justify-content:center;gap:.5rem;margin-top:1rem}
  .pg{
    border:1px solid var(--govco-secondary-color) !important;
    background:var(--govco-white-color) !important;
    color:var(--govco-secondary-color) !important;
    padding:.45rem .8rem;border-radius:8px;cursor:pointer;font-weight:700
  }
  .pg:hover{filter:brightness(.96)}
  .pg[disabled]{opacity:.45 !important;cursor:not-allowed !important}
  .pg-numbers{display:flex;gap:.35rem;flex-wrap:wrap}
  .pg-num{
    border:1px solid var(--govco-secondary-color) !important;
    background:var(--govco-white-color) !important;
    color:var(--govco-secondary-color) !important;
    padding:.4rem .65rem;border-radius:8px;cursor:pointer;font-weight:700
  }
  .pg-num.active{
    background:var(--govco-secondary-color) !important;
    color:#fff !important;border-color:var(--govco-secondary-color) !important;
  }

  /* Modal */
  .modal{position:fixed;inset:0;display:none}
  .modal.show{display:block}
  .modal-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.45)}
  .modal-dialog{position:relative;z-index:2;max-width:580px;margin:8vh auto;background:#fff;border-radius:14px;overflow:hidden;border:1px solid #eaeaea}
  .modal-header{display:flex;justify-content:space-between;align-items:center;padding:.8rem 1rem;border-bottom:1px solid #eee}
  .modal-body{padding:1rem;display:grid;gap:.7rem}
  .modal-footer{padding:.8rem 1rem;border-top:1px solid #eee;display:flex;justify-content:flex-end;gap:.5rem}
  .close{background:transparent;border:0;font-size:1.3rem;cursor:pointer}
  .fld span{display:block;font-size:.9rem;margin-bottom:.25rem;color:#333}
  .fld input,.fld textarea{width:100%;border:1px solid #d9d9d9;border-radius:10px;padding:.55rem .7rem}

  .tile-value, .pill {
    display: block !important; 
    white-space: pre-line !important;
    line-height: 1.4 !important;
  }
  .pill .unit, .pill .year { display: inline-block; margin-left: .25rem; }

  .cards { gap:1.25rem; }
  .card.card--bubble{
    flex-direction:column;
    align-items:center;
    text-align:center;
    padding:1.2rem 1.1rem;
    border-radius:28px;
    border:1px solid #e8ebf4;
    background:#fff;
    box-shadow:0 10px 30px rgba(18,38,63,.08), 0 2px 8px rgba(18,38,63,.06);
    transition:transform .15s ease, box-shadow .15s ease;
  }
  .card.card--bubble:hover{
    transform:translateY(-3px);
    box-shadow:0 16px 40px rgba(18,38,63,.12), 0 4px 14px rgba(18,38,63,.08);
  }
  .card--bubble .card-icon{
    width:86px; height:86px;
    border-radius:22px;
    background:#f7f9ff;
    display:grid; place-items:center;
    box-shadow:inset 0 0 0 1px #eef2ff;
    margin-bottom:.75rem;
  }
  .card--bubble .card-icon img{
    width:68px; height:68px; object-fit:contain;
  }
  .card--bubble .card-title{
    font-weight:800; color:#1c3f88; margin:0 0 .35rem; line-height:1.25;
  }
  .card--bubble .card-desc{ color:#4a4a4a; }
  .pill{
    margin-top:.35rem;
    background:#f1f4ff;
    color:#1c3f88;
    border:1px solid #e1e7ff;
    padding:.28rem .7rem;
    border-radius:999px;
    font-weight:700;
  }

  .slide-img{ object-fit:cover; object-position:center; aspect-ratio:16/9; }
  .slide.no-crop .slide-img{ object-fit:contain; background:#0d1b2a; }
  .card--bubble .card-icon img{ object-fit:contain; }

    /* grid más pro para la sección */
  .links-pro {
    display: grid;
    gap: 1rem;
    grid-template-columns: 1fr;
  }
  @media (min-width: 1000px) {
    .links-pro { grid-template-columns: 2fr 1fr 1.4fr 1.2fr; }
  }

  /* tarjetas de enlaces (sin bullets) */
  .list-unstyled { list-style: none; margin: 0; padding: 0; display: grid; gap: .6rem; }
  .link-item{
    display: grid;
    grid-template-columns: 1fr;
    align-items: center;
    gap: .65rem;
    padding: .65rem .75rem;
    border: 1px solid #e8ebf4;
    border-radius: 14px;
    text-decoration: none;
    background: #fff;
    box-shadow: 0 6px 16px rgba(18,38,63,.06);
    transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    color: #1c3f88;
  }
  .link-item:hover{
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(18,38,63,.10);
    border-color: #dbe3ff;
  }
  .link-icon{
    width: 40px; height: 40px; display: grid; place-items: center;
    background: #f1f4ff; border-radius: 12px; font-size: 1.1rem;
    box-shadow: inset 0 0 0 1px #e6ecff;
  }
  .link-text .link-title{ display:block; font-weight: 800; line-height: 1.25; }
  .link-text .link-meta{ color:#5b6b88; }
  .link-ext{ color:#7c8cb2; font-weight: 800; }

  .links-cards h3, .map-card h3 { margin: 0 0 .6rem; color:#222; font-weight:800; }

  /* chips de secciones */
  .chip-list { display: flex; flex-wrap: wrap; gap: .45rem; }
  .chip{
    display:inline-block; padding: .45rem .65rem; border-radius: 999px;
    border: 1px solid #e1e7ff; background:#f7f9ff; color:#1c3f88;
    font-weight:700; text-decoration:none; transition: background .12s ease;
  }
  .chip:hover{ background:#eef3ff; }
  /* CTA mejorada */
  .pro-cta {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: .75rem;
    border: 1px solid var(--govco-secondary-color);
    border-radius: 14px;
    background: #fff;
    padding: 1.25rem;
    box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
    transition: transform .15s ease, box-shadow .15s ease;
  }

  .pro-cta:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(0, 0, 0, .12);
  }

  .pro-cta h3 {
    margin: 0;
    color: var(--govco-secondary-color);
    font-weight: 800;
    font-size: 1.15rem;
  }

  .pro-cta p {
    margin: 0;
    color: #444;
    line-height: 1.5;
  }

  .pro-cta a.govco-btn {
    align-self: flex-start;
    background: var(--govco-secondary-color);
    color: #fff !important;
    border-radius: 8px;
    padding: .55rem 1.1rem;
    font-weight: 700;
    text-decoration: none;
    transition: background .2s ease;
  }

  .pro-cta a.govco-btn:hover {
    background: #003366;
  }

  @media (min-width: 900px) {
    .links {
      grid-template-columns: 1fr 1fr;
      align-items: start;
    }
  }

  .links-col, .cta {
    width: 100%;
  }
</style>
<script>
  (function(){
    const ready = (fn) => {
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn, { once:true });
      else fn();
    };

    ready(() => {
      const slider = document.getElementById('heroSlider');
      if (slider) {
        const slides = Array.from(slider.querySelectorAll('.slide'));
        const dots   = Array.from(slider.querySelectorAll('.dots button'));
        const prev   = slider.querySelector('.prev');
        const next   = slider.querySelector('.next');

        if (slides.length) {
          let i = Math.max(0, slides.findIndex(s => s.classList.contains('active')));
          if (i < 0) { i = 0; slides[0].classList.add('active'); }

          const ensureDot = (idx) => dots[idx] || null;

          const decideFit = (img, slide) => {
            if (!img.naturalWidth || !img.naturalHeight) return;
            const ratio = img.naturalWidth / img.naturalHeight;
            if (ratio < 1.25) slide.classList.add('no-crop'); else slide.classList.remove('no-crop');
          };

          const lazyLoad = (idx) => {
            const slide = slides[idx];
            const img = slide?.querySelector('.slide-img');
            if (!img) return;
            if (img.dataset.src && img.src !== img.dataset.src) {
              img.addEventListener('load', () => decideFit(img, slide), { once:true });
              img.src = img.dataset.src;
            } else if (img.complete) {
              decideFit(img, slide);
            } else {
              img.addEventListener('load', () => decideFit(img, slide), { once:true });
            }
          };

          const setActive = (n) => {
            slides[i].classList.remove('active');
            const d0 = ensureDot(i);
            d0 && d0.classList.remove('on');
            d0 && d0.setAttribute('aria-selected','false');

            i = (n + slides.length) % slides.length;

            slides[i].classList.add('active');
            const d1 = ensureDot(i);
            d1 && d1.classList.add('on');
            d1 && d1.setAttribute('aria-selected','true');

            lazyLoad(i);
          };

          prev && prev.addEventListener('click', (e) => { e.preventDefault(); setActive(i - 1); }, { passive:true });
          next && next.addEventListener('click', (e) => { e.preventDefault(); setActive(i + 1); }, { passive:true });
          dots.forEach((d, idx) => d.addEventListener('click', (e) => { e.preventDefault(); setActive(idx); }, { passive:true }));

          lazyLoad(i);

          let timer = null;
          const start = () => { if (slides.length > 1 && !timer) timer = setInterval(() => setActive(i + 1), 7000); };
          const stop  = () => { if (timer) { clearInterval(timer); timer = null; } };
          slider.addEventListener('mouseenter', stop);
          slider.addEventListener('mouseleave', start);

          let touchX = null;
          slider.addEventListener('touchstart', (e)=>{ touchX = e.touches[0].clientX; stop(); }, { passive:true });
          slider.addEventListener('touchend', (e)=>{
            if (touchX == null) return;
            const dx = e.changedTouches[0].clientX - touchX;
            if (Math.abs(dx) > 40) setActive(i + (dx < 0 ? 1 : -1));
            touchX = null; start();
          }, { passive:true });

          start();
        }
      }

      const toArray = (x) => Array.prototype.slice.call(x);

      const updatePagerState = (sectionEl, pageIdx, total) => {
        const pages = toArray(sectionEl.querySelectorAll('.page'));
        pages.forEach((p, k) => p.classList.toggle('page-active', k === pageIdx));

        const nums = toArray(sectionEl.querySelectorAll('.pg-num'));
        nums.forEach((b, k) => b.classList.toggle('active', k === pageIdx));

        const prevBtn = sectionEl.querySelector('.pg.prev');
        const nextBtn = sectionEl.querySelector('.pg.next');
        if (prevBtn) prevBtn.disabled = (pageIdx === 0);
        if (nextBtn) nextBtn.disabled = (pageIdx === total - 1);
      };

      document.querySelectorAll('.section-block').forEach(sectionEl => {
        const total = parseInt(sectionEl.querySelector('.section-pages')?.dataset.total || '1', 10);
        if (!total || total <= 1) return;

        let current = 0;
        updatePagerState(sectionEl, current, total);

        sectionEl.querySelectorAll('.pg-num').forEach(btn => {
          btn.addEventListener('click', (e) => {
            e.preventDefault();
            const n = parseInt(btn.dataset.page, 10) - 1;
            if (!isNaN(n)) { current = n; updatePagerState(sectionEl, current, total); }
          }, { passive:true });
        });

        sectionEl.querySelector('.pg.prev')?.addEventListener('click', (e) => {
          e.preventDefault();
          if (current > 0) { current -= 1; updatePagerState(sectionEl, current, total); }
        }, { passive:true });

        sectionEl.querySelector('.pg.next')?.addEventListener('click', (e) => {
          e.preventDefault();
          if (current < total - 1) { current += 1; updatePagerState(sectionEl, current, total); }
        }, { passive:true });
      });

      const modal = document.getElementById('contactModal');
      const open  = document.getElementById('openContact');
      const close = document.getElementById('closeContact');
      const cancel= document.getElementById('cancelContact');
      const hide  = ()=> modal?.classList.remove('show');
      const show  = ()=> modal?.classList.add('show');
      open?.addEventListener('click', (e)=>{ e.preventDefault(); show(); }, { passive:true });
      close?.addEventListener('click', (e)=>{ e.preventDefault(); hide(); }, { passive:true });
      cancel?.addEventListener('click', (e)=>{ e.preventDefault(); hide(); }, { passive:true });
      modal?.querySelector('.modal-backdrop')?.addEventListener('click', hide, { passive:true });

      (() => {
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        const animate = (el, target, duration = 1200) => {
          if (prefersReduced) { el.textContent = target.toLocaleString('es-CO'); return; }
          const t0 = performance.now();
          const step = (now) => {
            const p = Math.min((now - t0) / duration, 1);
            const eased = 1 - Math.pow(1 - p, 4); // easeOutQuart
            const val = Math.round(target * eased);
            el.textContent = val.toLocaleString('es-CO');
            if (p < 1) requestAnimationFrame(step);
          };
          requestAnimationFrame(step);
        };

        const kpis = Array.from(document.querySelectorAll('.kpi-value[data-target]'));
        if (!kpis.length) return;

        // Observa visibilidad con tolerancia: 35% visible, y margen para evitar triggers por 1px
        const io = new IntersectionObserver((entries) => {
          entries.forEach((en) => {
            const el = en.target;
            const target = parseInt(el.dataset.target, 10);
            if (!Number.isFinite(target)) return;

            if (en.isIntersecting) {
              if (el.dataset.animState === 'running') return;
              el.dataset.animState = 'running';
              animate(el, target);
            } else {
              el.dataset.animState = '';
              el.textContent = '0';
            }
          });
        }, { threshold: 0.35, rootMargin: '0px 0px -10% 0px' });

        kpis.forEach(el => io.observe(el));

        // Si ya están visibles al cargar, fuerza primer cálculo
        requestAnimationFrame(() => {
          kpis.forEach(el => {
            const r = el.getBoundingClientRect();
            const visible = r.top < window.innerHeight * 0.65 && r.bottom > window.innerHeight * 0.1;
            if (visible && !el.dataset.animState) {
              el.dataset.animState = 'running';
              animate(el, parseInt(el.dataset.target,10));
            }
          });
        });
      })();
    });
  })();

  (function(){
    const els = document.querySelectorAll('.kpi-value[data-target]');
    if (!els.length) return;

    const parseTarget = (el) => {
      const raw = String(el.dataset.target || '').trim();
      if (!raw) return NaN;
      const norm = raw.replace(/\./g,'').replace(',', '.').replace(/[^\d.-]/g,'');
      return parseFloat(norm);
    };

    const animate = (el, target, duration = 1400) => {
      if (el.dataset.animated) return;
      el.dataset.animated = '1';
      const t0 = performance.now();
      const isInt = Number.isInteger(target);
      const step = (now) => {
        const p = Math.min((now - t0) / duration, 1);
        const eased = 1 - Math.pow(1 - p, 3);
        const val = target * eased;
        el.textContent = isInt ? Math.round(val).toString()
                              : (Math.round(val * 10) / 10).toString();
        if (p < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    };

    const inView = (el) => {
      const r = el.getBoundingClientRect();
      return r.top < window.innerHeight * 0.9 && r.bottom > 0;
    };

    if ('IntersectionObserver' in window) {
      const io = new IntersectionObserver((entries) => {
        entries.forEach(en => {
          if (en.isIntersecting) {
            const t = parseTarget(en.target);
            if (!isNaN(t)) animate(en.target, t);
            io.unobserve(en.target);
          }
        });
      }, { threshold: 0, rootMargin: '0px 0px -20% 0px' });

      els.forEach(el => {
        if (inView(el)) {
          const t = parseTarget(el);
          if (!isNaN(t)) animate(el, t);
        } else {
          io.observe(el);
        }
      });
    } else {
      // Fallback sin Observer
      els.forEach(el => {
        const t = parseTarget(el);
        if (!isNaN(t)) animate(el, t);
      });
    }
  })();
</script>