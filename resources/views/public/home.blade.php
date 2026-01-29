@extends('layouts.app')
@section('title', 'Inicio')
@section('content')
    <div class="full-width-section">
        <x-slider :images="$images" />
        <section class="tramites-section">
            <div class="tramites-content text-center">
                <div class="tabs">
                    <button class="tab active" onclick="showTab('tramites', this)">Trámites y Servicios</button>
                    <button class="tab" onclick="showTab('atencion', this)">Atención al Ciudadano</button>
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
            <div class="curve-background"></div>
        </section>
    </div>
    @php
        $microsites = [
            ['label' => 'Rentas Departamental', 'url' => 'https://historico.ticputumayo.gov.co/index.php/gestion-de-gobierno/rentas-departamental', 'icon' => '/img/microsites/taxes.svg'],
            ['label' => 'Gestión de Información Estadistica', 'url' => 'https://www.putumayo.gov.co/statistical-information-management', 'icon' => '/img/microsites/statistic.svg'],
            ['label' => 'ICETEX', 'url' => 'https://historico.ticputumayo.gov.co/index.php/component/sppagebuilder/page/423', 'icon' => '/img/microsites/student.svg'],
        ];
    @endphp
    <x-public.home.microsites 
        :items="$microsites"
        title="Micro Sitios"
        intro="Conoce aquí la información complementaria de todos nuestros procesos y programas."
        variant="grid"
        :columns="4"
    />

    <x-public.home.social-media/>

    <x-public.home.calls-job 
        :publications="$callsjob"
    />

    <section class="news-tabs govco-container" aria-label="Sección de noticias">
        <div class="tabs tabs--news" role="tablist">
            <button class="tab is-active"
                    role="tab"
                    aria-selected="true"
                    aria-controls="tab-news"
                    data-target="tab-news">
            Noticias
            </button>
            <button class="tab"
                    role="tab"
                    aria-selected="false"
                    aria-controls="tab-anti"
                    data-target="tab-anti">
            Anticontrabando
            </button>
        </div>

        <div id="tab-news" class="tab-pane is-active" role="tabpanel">
            <x-public.home.news :publications="$publications" />
        </div>

        <div id="tab-anti" class="tab-pane" role="tabpanel" hidden>
            <x-public.home.news-antismuggling :publications="$publicationsAnti" />
        </div>
    </section>
    <x-public.home.interest-links />
@endsection
<script>
function showTab(tabName, el) {
    const tabs = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tramites-section .tabs .tab');

    tabs.forEach(tab => tab.classList.remove('active'));
    buttons.forEach(btn => btn.classList.remove('active'));

    document.getElementById(`tab-${tabName}`).classList.add('active');
    el.classList.add('active');
}

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.tabs--news .tab');
  if (!btn) return;

  const tabs = btn.parentElement.querySelectorAll('.tab');
  const panes = btn.closest('.news-tabs').querySelectorAll('.tab-pane');

  tabs.forEach(t => { t.classList.remove('is-active'); t.setAttribute('aria-selected','false'); });
  panes.forEach(p => { p.classList.remove('is-active'); p.hidden = true; });

  btn.classList.add('is-active');
  btn.setAttribute('aria-selected','true');

  const id = btn.dataset.target;
  const pane = document.getElementById(id);
  if (pane) { pane.classList.add('is-active'); pane.hidden = false; }
});

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.tabs--social .tab');
  if (!btn) return;

  const root = btn.closest('.social-tabs');
  const tabs = root.querySelectorAll('.tabs--social .tab');
  const panes = root.querySelectorAll('.tab-pane');

  tabs.forEach(t => { t.classList.remove('is-active'); t.setAttribute('aria-selected','false'); });
  panes.forEach(p => { p.classList.remove('is-active'); p.hidden = true; });

  btn.classList.add('is-active');
  btn.setAttribute('aria-selected','true');

  const id = btn.dataset.target;
  const pane = root.querySelector('#' + id);
  if (pane) { pane.classList.add('is-active'); pane.hidden = false; }
});
</script>

<style>
.full-width-section {
    width: 99.7vw;
    margin-left: calc(-50vw + 50%);
    overflow: hidden;
    position: relative;
}

.tramites-section {
    background-color: #164194;
    position: relative;
    overflow: hidden;
}

.tramites-section,
.tramites-section * {
    color: white !important;
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
    border-radius: 0 0 4px 4px;
    border: none;
    transition: all 0.3s ease;
}

.tab.active {
    background-color: white;
    color: #0c3c84 !important;
    border-bottom: 4px solid var(--govco-fourth-color);
    cursor: default;
}

.tab.inactive {
    background-color: transparent;
    color: white;
    border-bottom: 4px solid transparent;
    cursor: pointer;
}

.tab.inactive:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-bottom: 4px solid var(--govco-fourth-color);
}

.tramites-subtitle {
    font-size: 1.1rem;
    margin-bottom: 2.5rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
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
    color: white;
    max-width: 160px;
    transition: transform 0.3s ease, background-color 0.3s ease;
}

.tramite-item:hover {
    transform: translateY(-8px);
}

.tramite-icon {
    background-color: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    padding: 1.3rem;
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    transition: background-color 0.3s ease, border 0.3s ease;
    border: 2px solid transparent;
}

.tramite-item:hover .tramite-icon {
    background-color: rgba(255, 255, 255, 0.3);
    border-color: var(--govco-fourth-color);
}

.tramite-icon img {
    width: 150px;
    height: auto;
    filter: brightness(0) invert(1);
}

.tramite-title {
    font-weight: bold;
    font-size: 0.95rem;
    white-space: pre-line;
}

.tramites-button {
    display: inline-block;
    padding: 0.6rem 1.5rem;
    border: 2px solid var(--govco-fourth-color);
    color: white;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.3s ease;
    margin-bottom: 4rem;
}

.tramites-button:hover {
    background-color: white;
    color: #0c3c84 !important;
}

.curve-background {
    position: absolute;
    bottom: 0;
    background-color: white !important;
    left: 0;
    width: 100%;
    height: 180px;
    background: url('/design/curve.svg') no-repeat bottom center;
    background-size: cover;
    pointer-events: none;
    z-index: 1;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: flex;
}

.news-tabs { margin: 2rem auto; }
.tabs--news {
  display: flex; gap: .5rem; justify-content: center; margin-bottom: 1rem;
}
.tabs--news .tab{
  border: 1px solid var(--govco-secondary-color);
  background: var(--govco-white-color);
  color: var(--govco-secondary-color);
  padding: .55rem 1rem;
  border-radius: var(--govco-border-radius);
  font-weight: 700; cursor: pointer;
  transition: background .2s ease, color .2s ease, box-shadow .2s ease;
}
.tabs--news .tab.is-active{
  background: var(--govco-secondary-color);
  color: var(--govco-white-color);
  box-shadow: var(--govco-box-shadow);
}
.tab-pane { display: block; }
.tab-pane:not(.is-active) { display: none; }

@media (max-width: 992px) {
    .full-width-section {
        width: 100%;
        margin-left: 0;
    }

    .tramites-content {
        padding: 3rem 1rem 10rem;/
    }

    .tramites-grid {
        gap: 2rem 1.5rem;/
    }

    .tramite-item {
        max-width: 100px;
    }
    .tramite-icon {
        width: 80px;
        height: 80px;
        padding: 1rem;
    }
    .tramite-icon img {
        width: 100px;
    }
    .tramite-title {
        font-size: 0.85rem;
    }

    .curve-background {
        height: 100px; 
    }

    .tab {
        font-size: 0.9rem;
        padding: 0.5rem 0.8rem;
    }
    
    .tabs--news .tab {
        font-size: 0.9rem;
        padding: .45rem .8rem;
    }
}

@media (max-width: 576px) {
    .tramites-content {
        padding: 2rem 0.5rem 8rem;
    }
    .tramites-grid {
        gap: 1.5rem 1rem;
    }
    .tramite-item {
        max-width: 80px;
    }
    .tramite-icon {
        width: 65px;
        height: 65px;
        padding: 0.8rem;
    }
    .tramite-icon img {
        width: 80px;
    }
    .tramite-title {
        font-size: 0.8rem;
    }
    .curve-background {
        height: 80px;
    }

    .tabs {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .tab {
        margin: 0;
    }
}
</style>