@extends('public.transparency.shared.sidebar')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container mt-4">
    <h2 class="text-center">{{ $communication->title }}</h2>
    <p class="text-center text-muted">
        Publicado el: {{ $communication->created_at->format('d F Y') }}
    </p>
    
    <p class="text-justify">{{ $communication->description }}</p>

    @if($communication->content)
        <div class="content-section mt-3">
            <h4>Información adicional</h4>
            <p class="text-justify">{{ $communication->content }}</p>
        </div>
    @endif

    @if($communication->attachment)
        <div class="pdf-viewer mt-4">
            <iframe src="{{ asset('storage/' . $communication->attachment) }}" width="100%" height="600px" style="border: none;">
                Este navegador no soporta la visualización de PDF. Por favor, descarga el archivo para verlo: 
                <a href="{{ asset('storage/' . $communication->attachment) }}" target="_blank">Descargar PDF</a>
            </iframe>
        </div>
    @else
        <p>No hay documento adjunto disponible.</p>
    @endif
</div>
@endsection

<style>
    .text-justify {
        text-align: justify;
    }

    .content-section {
        margin-top: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
        border-radius: 4px;
    }

    .pdf-viewer {
        margin-top: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }
</style>
