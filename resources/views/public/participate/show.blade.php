@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>{{ $participate->title }}</h2>
    <div class="content-flex mb-4">
        <img src="{{ asset($participate->image) }}" alt="{{ $participate->title }}" class="detail-image">
        <div class="text-content">
            <p class="text-justify">{{ $participate->description }}</p>

            <!-- Acordeón de secciones -->
            <div class="accordion mt-4" id="sectionAccordion">
                @foreach ($participate->sections as $section)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $section->id }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $section->id }}" aria-expanded="false" aria-controls="collapse{{ $section->id }}">
                                {{ $section->title }}
                            </button>
                        </h2>
                        <div id="collapse{{ $section->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $section->id }}" data-bs-parent="#sectionAccordion">
                            <div class="accordion-body">
                                <p>{{ $section->content }}</p>
                                
                                <!-- Mostrar enlaces de la sección -->
                                <ul>
                                    @foreach ($section->links as $link)
                                        @php
                                            // Verifica si el enlace es una URL externa
                                            $isExternal = Str::startsWith($link->url, ['http://', 'https://']);
                                        @endphp

                                        <li>
                                            <a href="{{ $isExternal ? $link->url : asset('storage/' . $link->url) }}" target="_blank">
                                                {{ $link->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    .text-justify {
        text-align: justify;
    }

    .content-flex {
        display: flex;
        align-items: flex-start;
        gap: 20px;
    }

    .detail-image {
        width: auto;
        height: 500px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .text-content {
        flex: 1;
    }

    /* Estilos personalizados para el acordeón */
    .accordion-button {
        background-color: #004085; /* Color de fondo */
        color: #ffffff; /* Color del texto */
        border: none; /* Quitar borde */
    }

    .accordion-button:hover {
        background-color: #004884; /* Color de fondo en hover */
        color: #ffffff; /* Color del texto en hover */
    }

    .accordion-button:not(.collapsed) {
        background-color: #0056b3; /* Color de fondo cuando el acordeón está abierto */
        color: #ffffff; /* Color del texto cuando el acordeón está abierto */
    }

    .accordion-body {
        background-color: #e9ecef; /* Fondo del contenido */
        color: #212529; /* Color del texto del contenido */
        border-top: 1px solid #ddd; /* Borde superior del contenido */
    }

    .accordion-item {
        border: 1px solid #ddd; /* Borde del acordeón */
        border-radius: 4px;
        margin-bottom: 10px;
    }

    /* Ajuste responsivo para pantallas pequeñas */
    @media (max-width: 768px) {
        .content-flex {
            flex-direction: column;
            align-items: center;
        }

        .detail-image {
            width: 100%;
        }
    }
</style>
