@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            @php
                $embed = \App\Support\VideoEmbed::parse($publication->link ?? null, $publication->title ?? 'Video');
            @endphp

            <div class="card mb-4">
                @if($embed)
                    <div class="ratio ratio-16x9 video-embed">
                        <iframe
                            src="{{ $embed['src'] }}"
                            title="{{ e($embed['title']) }}"
                            loading="lazy"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen
                        ></iframe>
                    </div>
                @elseif($publication->image)
                    <img src="{{ asset('storage/' . $publication->image) }}" class="card-img-top" alt="Imagen de la publicación">
                @endif

                <div class="card-body">
                    <h2 class="card-title">{{ $publication->title }}</h2>

                    <p class="text-muted mb-1">
                        <strong>Tipo:</strong> {{ optional($publication->type)->name ?? 'Sin tipo' }} |
                        <strong>Estado:</strong> {{ $publication->state ? 'Publicado' : 'Borrador' }} |
                        <strong>Vistas:</strong> {{ $publication->views ?? 0 }}
                    </p>

                    <div class="card-text mb-3">
                        {!! nl2br(e($publication->description)) !!}
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        @if($publication->document)
                            <a href="{{ asset('storage/' . $publication->document) }}" target="_blank" class="btn btn-primary">
                                Ver documento adjunto
                            </a>
                        @endif

                        @if(!empty($publication->link))
                            <a href="{{ $publication->link }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
                                Más información…
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between my-4">
                @if($previous)
                    <a href="{{ route('publications.show', ['id' => $previous->id, 'type' => $typeId]) }}"
                       class="btn btn-outline-primary">
                        ← Noticia anterior
                    </a>
                @else
                    <span></span>
                @endif

                @if($next)
                    <a href="{{ route('publications.show', ['id' => $next->id, 'type' => $typeId]) }}"
                       class="btn btn-outline-primary">
                        Siguiente noticia →
                    </a>
                @endif
            </div>

            <a href="{{ url()->previous() }}" class="btn btn-secondary">← Volver</a>
        </div>

        <!-- COLUMNA LATERAL -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 p-0 other-news-card">
                {{-- Header pegado arriba y a los lados --}}
                <div class="bg-primary text-white fw-semibold px-3 py-2 m-0">
                    Otras noticias
                </div>

                <ul class="list-group list-group-flush mb-0">
                    @foreach($otherPublications as $other)
                        <li class="list-group-item list-group-item-action py-2">
                            <a href="{{ route('publications.show', ['id' => $other->id, 'type' => $typeId]) }}"
                            class="text-decoration-none d-flex flex-column">
                                <span class="fw-semibold mb-1">
                                    {{ Str::limit($other->title, 60) }}
                                </span>
                                @if($other->date)
                                    <span class="text-muted small">
                                        {{ $other->date->format('d/m/Y') }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
.other-news-card > *:first-child {
    margin-top: 0 !important;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}
</style>