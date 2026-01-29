@extends('layouts.app')

@section('title', 'Sistema Integrado de Gestión')

@section('content')
<div class="container py-5 sig-wrapper">
    <h2 class="sig-page-title text-center mb-4">
        {{ $page->title }}
    </h2>

    @if($items->isEmpty())
        <p class="text-center">No hay contenido del Sistema Integrado de Gestión.</p>
    @else
        <div class="sig-layout">
            {{-- MENÚ LATERAL --}}
            <aside class="sig-sidebar">
                <h3 class="sig-sidebar-title">Contenido</h3>
                <ul class="sig-sidebar-list">
                    @foreach($items as $item)
                        <li class="sig-sidebar-item {{ $loop->first ? 'is-active' : '' }}">
                            <button
                                type="button"
                                class="sig-sidebar-button"
                                data-target="sig-item-{{ $item->id }}"
                            >
                                {{ $item->title }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </aside>

            {{-- CONTENIDO PRINCIPAL --}}
            <section class="sig-content-area">
                @foreach($items as $item)
                    <article
                        id="sig-item-{{ $item->id }}"
                        class="sig-content-panel {{ $loop->first ? 'is-active' : '' }}"
                    >
                        <h3 class="sig-content-title">{{ $item->title }}</h3>

                        {{-- Imagen (Mapa de procesos / Estructura Organizacional) --}}
                        @if($item->image)
                            <div class="sig-media sig-media-image">
                                <img
                                    src="{{ asset('storage/' . $item->image) }}"
                                    alt="{{ $item->title }}"
                                    class="img-fluid"
                                >
                            </div>
                        @endif

                        {{-- Documento PDF (Portafolio, Manuales, etc.) --}}
                        @if($item->document)
                            <div class="sig-media sig-media-document">
                                <p class="sig-document-text">
                                    Este contenido se encuentra disponible en formato PDF.
                                </p>
                                <a
                                    href="{{ asset('storage/' . $item->document) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="sig-document-link"
                                >
                                    Ver documento PDF
                                </a>
                            </div>
                        @endif

                        {{-- Texto (Misión, Visión, etc.) --}}
                        @if($item->description)
                            <div class="sig-description">
                                {!! nl2br(e($item->description)) !!}
                            </div>
                        @endif
                    </article>
                @endforeach
            </section>
        </div>
    @endif
</div>

{{-- ESTILOS ESPECÍFICOS SIG --}}
<style>
    .sig-wrapper {
        font-family: var(--govco-font-primary);
    }

    .sig-page-title {
        font-weight: 700;
        color: var(--govco-secondary-color);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .sig-layout {
        display: grid;
        grid-template-columns: 280px minmax(0, 1fr);
        gap: 2rem;
        align-items: flex-start;
    }

    /* SIDEBAR */
    .sig-sidebar {
        background-color: var(--govco-gray-menu);
        border-radius: var(--govco-border-radius);
        box-shadow: var(--govco-box-shadow);
        padding: 1.5rem 1.25rem;
    }

    .sig-sidebar-title {
        margin: 0 0 1rem;
        font-size: 1rem;
        font-weight: 700;
        color: var(--govco-tertiary-color);
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .sig-sidebar-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .sig-sidebar-item {
        border-radius: var(--govco-border-radius);
        overflow: hidden;
    }

    .sig-sidebar-button {
        width: 100%;
        text-align: left;
        border: none;
        background: transparent;
        padding: 0.6rem 0.75rem;
        font-size: 0.95rem;
        font-family: var(--govco-font-secondary);
        color: var(--govco-tertiary-color);
        cursor: pointer;
        transition: background-color 0.18s ease, color 0.18s ease, padding-left 0.18s ease;
    }

    .sig-sidebar-button:hover {
        background-color: rgba(51, 102, 204, 0.08);
        padding-left: 1rem;
    }

    .sig-sidebar-item.is-active .sig-sidebar-button {
        background-color: var(--govco-primary-color);
        color: var(--govco-white-color);
        font-weight: 600;
        padding-left: 1rem;
    }

    /* CONTENIDO */
    .sig-content-area {
        background-color: var(--govco-white-color);
        border-radius: var(--govco-border-radius);
        box-shadow: var(--govco-box-shadow);
        padding: 1.75rem;
        min-height: 320px;
    }

    .sig-content-panel {
        display: none;
        animation: sig-fade-in 0.2s ease-out;
    }

    .sig-content-panel.is-active {
        display: block;
    }

    .sig-content-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--govco-secondary-color);
        margin-bottom: 1.25rem;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .sig-content-title::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        width: 60px;
        height: 3px;
        border-radius: 999px;
        background: linear-gradient(
            to right,
            var(--govco-primary-color),
            var(--govco-fourth-color)
        );
    }

    .sig-media {
        margin-bottom: 1.25rem;
        border-radius: var(--govco-border-radius);
        overflow: hidden;
        background-color: var(--govco-gray-color);
    }

    .sig-media-image img {
        display: block;
        width: 100%;
        height: auto;
    }

    .sig-media-document {
        padding: 1.25rem 1.5rem;
        border-left: 4px solid var(--govco-primary-color);
        background-color: #f8fafc;
    }

    .sig-document-text {
        margin: 0 0 0.75rem;
        color: var(--govco-tertiary-color);
        font-size: 0.95rem;
    }

    .sig-document-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 999px;
        background-color: var(--govco-primary-color);
        color: var(--govco-white-color);
        font-size: 0.9rem;
        text-decoration: none;
        font-weight: 600;
    }

    .sig-document-link::before {
        content: "📄";
        font-size: 1rem;
    }

    .sig-document-link:hover {
        background-color: var(--govco-secondary-color);
    }

    .sig-description {
        font-family: var(--govco-font-secondary);
        font-size: 0.98rem;
        line-height: 1.6;
        color: var(--govco-tertiary-color);
    }

    @keyframes sig-fade-in {
        from { opacity: 0; transform: translateY(4px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* RESPONSIVE */
    @media (max-width: 991.98px) {
        .sig-layout {
            grid-template-columns: 1fr;
        }

        .sig-sidebar {
            order: 2;
        }

        .sig-content-area {
            order: 1;
        }
    }

    @media (max-width: 575.98px) {
        .sig-content-area {
            padding: 1.25rem;
        }

        .sig-sidebar-button {
            font-size: 0.9rem;
        }
    }
</style>

{{-- SCRIPT PARA CAMBIAR PANELES --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.sig-sidebar-button');
        const panels  = document.querySelectorAll('.sig-content-panel');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');

                // marcar activo en el menú
                document
                    .querySelectorAll('.sig-sidebar-item')
                    .forEach(li => li.classList.remove('is-active'));
                button.closest('.sig-sidebar-item').classList.add('is-active');

                // mostrar panel correspondiente
                panels.forEach(panel => {
                    panel.classList.toggle('is-active', panel.id === targetId);
                });
            });
        });
    });
</script>
@endsection