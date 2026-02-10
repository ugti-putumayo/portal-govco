@extends('layouts.app')
@section('title', 'Control Interno de Gestión')

@section('content')
<div class="icg-wrapper">
    {{-- HERO / INTRO --}}
    <section class="icg-hero">
        <h1 class="icg-title">Control Interno de Gestión</h1>
        <div class="icg-hero-text">
            <p>
                Se entiende por control interno el sistema integrado por el esquema de organización y el conjunto de los planes,
                métodos, principios, normas, procedimientos y mecanismos de verificación y evaluación adoptados por una entidad,
                con el fin de procurar que todas las actividades, operaciones y actuaciones, así como la administración de la
                información y los recursos, se realicen de acuerdo con las normas constitucionales y legales vigentes.
            </p>
        </div>
    </section>

    {{-- LAYOUT PRINCIPAL (Tabs Izq + Contenido Der) --}}
    <div class="icg-layout">
        
        {{-- ASIDE: MENU DE TABS --}}
        <aside class="icg-sidebar">
            <nav class="icg-tablist" role="tablist" aria-label="Secciones de Control Interno">
                <button class="icg-tab is-active" role="tab" data-panel="paa" aria-controls="panel-paa" aria-selected="true">
                    Plan Anual de Auditorías
                </button>
                <button class="icg-tab" role="tab" data-panel="audits" aria-controls="panel-audits" aria-selected="true">
                    Auditorías
                </button>
                <button class="icg-tab" role="tab" data-panel="plans" aria-controls="panel-plans" aria-selected="false">
                    Planes de Mejoramiento
                </button>
                <button class="icg-tab" role="tab" data-panel="monitorings" aria-controls="panel-monitorings" aria-selected="false">
                    Informes de Seguimiento
                </button>
                <button class="icg-tab" role="tab" data-panel="culture" aria-controls="panel-culture" aria-selected="false">
                    Fomento de Cultura
                </button>
                <button class="icg-tab" role="tab" data-panel="bulletin" aria-controls="panel-bulletin" aria-selected="false">
                    Boletines
                </button>
                <button class="icg-tab" role="tab" data-panel="evaluation" aria-controls="panel-evaluation" aria-selected="false">
                    Informe de Evaluación Independiente
                </button>
            </nav>
        </aside>

        {{-- MAIN: PANELES DE CONTENIDO --}}
        <main class="icg-content-area">
            {{-- Panel: Plan Anual de Auditorías --}}
            <section id="panel-paa" class="icg-panel" role="tabpanel" data-slug="internal-management-control-paa" data-loaded="0">
                <div class="icg-panel-body"></div>
            </section>
            
            {{-- Panel: Auditorías --}}
            <section id="panel-audits" class="icg-panel" role="tabpanel" hidden data-slug="internal-management-control-audits" data-loaded="0">
                <div class="icg-panel-body"></div>
            </section>

            {{-- Panel: Planes --}}
            <section id="panel-plans" class="icg-panel" role="tabpanel" hidden data-slug="internal-management-control-improvement-plans" data-loaded="0">
                <div class="icg-panel-body"></div>
            </section>

            {{-- Panel: Informes --}}
            <section id="panel-monitorings" class="icg-panel" role="tabpanel" hidden data-slug="internal-management-control-monitoring-reports" data-loaded="0">
                <div class="icg-panel-body"></div>
            </section>

            {{-- Panel: Cultura --}}
            <section id="panel-culture" class="icg-panel" role="tabpanel" hidden data-slug="internal-management-control-promoting-culture-selfcontrol" data-loaded="0">
                <div class="icg-panel-body"></div>
            </section>

            {{-- Panel: Boletines --}}
            <section id="panel-bulletin" class="icg-panel" role="tabpanel" hidden data-slug="internal-management-control-bulletin" data-loaded="0">
                <div class="icg-panel-body"></div>
            </section>
            
            {{-- Panel: Evaluación --}}
            <section id="panel-evaluation" class="icg-panel" role="tabpanel" hidden data-slug="internal-management-control-independent-evaluation-report" data-loaded="0">
                <div class="icg-panel-body"></div>
            </section>
        </main>
    </div>
</div>

{{-- JS Tabs + Lazy load (Sin cambios lógicos, solo optimización visual) --}}
<script>
    const slugToId = @json($slugToId ?? []);
    document.addEventListener('DOMContentLoaded', () => {
        const tabs   = document.querySelectorAll('.icg-tab');
        const panels = document.querySelectorAll('.icg-panel');

        async function loadPanel(panelEl) {
            if (!panelEl || panelEl.dataset.loaded === '1') return;

            const slug = panelEl.dataset.slug;
            const key  = (slugToId && slugToId[slug]) ? slugToId[slug] : slug;
            const body = panelEl.querySelector('.icg-panel-body');

            // Spinner mejorado
            body.innerHTML = `
                <div class="icg-loader">
                    <span class="icg-spinner" aria-hidden="true"></span>
                    <span>Cargando información...</span>
                </div>`;

            try {
                // Asegúrate que esta ruta exista en tu backend
                const url = `{{ route('internal-management-control.tab', ['slug' => 'SLUG']) }}`.replace('SLUG', encodeURIComponent(key));
                
                const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const json = await res.json();

                body.innerHTML = (json && json.html) ? json.html : '<div class="icg-empty">Sin contenido disponible.</div>';
                panelEl.dataset.loaded = '1';
            } catch (err) {
                body.innerHTML = '<div class="icg-error">No se pudo cargar el contenido. Intente nuevamente.</div>';
                console.error(err);
            }
        }

        function show(panelId) {
            // Ocultar paneles
            panels.forEach(p => {
                p.hidden = true;
                p.classList.remove('fade-in');
            });

            // Activar panel
            const activePanel = document.getElementById(`panel-${panelId}`);
            if(activePanel) {
                activePanel.hidden = false;
                // Pequeño timeout para permitir que el navegador procese el hidden=false antes de animar
                setTimeout(() => activePanel.classList.add('fade-in'), 10);
            }

            // Actualizar estado botones
            tabs.forEach(t => {
                const isActive = t.dataset.panel === panelId;
                t.classList.toggle('is-active', isActive);
                t.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            if(activePanel) loadPanel(activePanel);
        }

        tabs.forEach(btn => btn.addEventListener('click', () => show(btn.dataset.panel)));

        // Cargar inicial
        show('paa');
    });
</script>

<style>
    /* Variables Scope Local */
    .icg-wrapper {
        --icg-primary: var(--govco-secondary-color, #3366CC); /* Azul Gobierno */
        --icg-bg-gray: #f4f6f9;
        --icg-border: #e2e8f0;
        --icg-text: #4a5568;
        --icg-radius: 8px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
        font-family: var(--govco-font-primary, sans-serif);
    }

    /* Hero */
    .icg-hero {
        width: 100%; 
        box-sizing: border-box; /* Asegura que el padding no rompa el ancho */
        margin-bottom: 2.5rem;
        text-align: center;
    }
    .icg-title { 
        color: var(--icg-primary); 
        font-weight: 800; 
        font-size: 2rem; 
        margin-bottom: 1rem; 
    }
    .icg-hero-text {
        max-width: 100%;
        margin: 0 auto;
        color: var(--icg-text);
        line-height: 1.6;
        text-align: justify;
    }

    /* --- LAYOUT GRID/FLEX --- */
    .icg-layout {
        display: flex;
        gap: 2rem;
        align-items: flex-start; /* Alineación superior */
    }

    /* Sidebar (Tabs Verticales) */
    .icg-sidebar {
        flex: 0 0 260px; /* Ancho fijo para el menú */
        position: sticky;
        top: 20px; /* Si hay scroll largo, el menú acompaña */
    }

    .icg-tablist {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .icg-tab {
        text-align: left;
        padding: 1rem 1.2rem;
        background: transparent;
        border: none;
        border-left: 4px solid transparent;
        color: var(--icg-text);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border-radius: 0 var(--icg-radius) var(--icg-radius) 0;
    }

    .icg-tab:hover {
        background-color: color-mix(in srgb, var(--icg-primary) 5%, transparent);
        color: var(--icg-primary);
    }

    .icg-tab.is-active {
        background-color: white;
        color: var(--icg-primary);
        border-left-color: var(--icg-primary);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        font-weight: 700;
    }

    /* Content Area */
    .icg-content-area {
        flex: 1; /* Toma el resto del espacio */
        min-width: 0; /* Evita desbordamiento en flex */
    }

    .icg-panel {
        background: #fff;
        border: 1px solid var(--icg-border);
        border-radius: var(--icg-radius);
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        opacity: 0; 
        transition: opacity 0.3s ease-in;
    }
    
    .icg-panel.fade-in { opacity: 1; }

    .icg-panel-header {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--icg-primary);
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--icg-bg-gray);
        padding-bottom: 0.5rem;
    }

    /* Utility */
    .icg-loader { display: flex; align-items: center; gap: 10px; color: var(--icg-text); }
    .icg-spinner {
        width: 20px; height: 20px; border-radius: 50%;
        border: 2px solid #ccc; border-top-color: var(--icg-primary);
        animation: spin .8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .icg-empty, .icg-error { padding: 1rem; background: var(--icg-bg-gray); border-radius: 4px; color: #666; }

    /* --- RESPONSIVE (Móvil) --- */
    @media (max-width: 768px) {
        .icg-layout {
            flex-direction: column; /* Apilar uno sobre otro */
            gap: 1.5rem;
        }
        
        .icg-sidebar {
            width: 100%;
            flex: none;
            position: static;
        }

        /* Convertir tabs verticales a scroll horizontal en móvil */
        .icg-tablist {
            flex-direction: row;
            overflow-x: auto;
            padding-bottom: 5px; /* Espacio para scrollbar */
            border-bottom: 1px solid var(--icg-border);
        }

        .icg-tab {
            white-space: nowrap;
            border-left: none;
            border-bottom: 3px solid transparent;
            border-radius: 4px 4px 0 0;
            padding: 0.8rem 1rem;
        }

        .icg-tab.is-active {
            border-left-color: transparent;
            border-bottom-color: var(--icg-primary);
            background: color-mix(in srgb, var(--icg-primary) 5%, transparent);
            box-shadow: none;
        }
    }
</style>
@endsection