@extends('layouts.app')

@section('content')
<div class="container mt-4 communications-page">
    <h2 class="communications-title">Comunicados Oficiales, Convocatorias de la entidad</h2>
    <p class="communications-intro">
        Inicialmente es preciso mencionar que el acuerdo 060 de 2001 es el que establece las pautas para la
        administración de las comunicaciones oficiales en las entidades públicas y las privadas que cumplen
        funciones públicas. Allí se define que las Comunicaciones Oficiales son todas aquellas recibidas o
        producidas en desarrollo de las funciones asignadas legalmente a una entidad, independientemente del
        medio utilizado.
    </p>

    <div class="search-container">
        <form method="GET" action="{{ route('newscallsjobs') }}" class="search-form">
            <input type="text" name="search" class="search-bar" placeholder="Buscar comunicado..." value="{{ request('search') }}">
            <span class="search-icon">🔍</span>
        </form>
    </div>

    <div class="list-group">
        @foreach($communications as $communication)
            <a href="{{ route('newscallsjobs.show', $communication->id) }}" class="list-group-item list-group-item-action">
                <h5 class="mb-1">{{ $communication->title }}</h5>
                <p class="mb-1">{{ \Illuminate\Support\Str::limit($communication->content, 100) }}</p>
                <small>{{ $communication->created_at->format('d/m/Y') }}</small>
            </a>
        @endforeach
    </div>

    @if ($communications->isEmpty())
        <p class="text-center text-muted">No hay comunicados disponibles en este momento.</p>
    @endif

    <div class="d-flex justify-content-center mt-4">
        {{ $communications->appends(['search' => request('search')])->links() }}
    </div>
</div>

<style>
    .communications-page {
        background-color: #f8f9fa;
        font-family: 'Arial', sans-serif;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        max-width: 90%;
        margin: auto;
    }

    .communications-page .communications-title {
        font-size: 1.8rem;
        font-weight: bold;
        text-align: center;
        color: #0033A0;
    }

    .communications-page .communications-intro {
        text-align: justify;
        font-size: 1rem;
        color: #333;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    /* Contenedor del buscador alineado a la izquierda */
    .communications-page .search-container {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 20px;
        position: relative;
    }

    /* Formulario con posición relativa para alinear correctamente el icono */
    .communications-page .search-form {
        width: 100%;
        max-width: 400px;
        position: relative;
        display: flex;
        align-items: center;
    }

    /* Campo de búsqueda */
    .communications-page .search-bar {
        width: 100%;
        padding: 12px 40px 12px 15px;
        border: 2px solid #0033A0;
        border-radius: 25px;
        font-size: 16px;
        outline: none;
        transition: 0.3s;
        background-color: white;
    }

    .communications-page .search-bar:focus {
        border-color: #0056b3;
        box-shadow: 0 0 5px rgba(0, 83, 179, 0.5);
    }

    /* Icono de búsqueda correctamente alineado */
    .communications-page .search-icon {
        position: absolute;
        right: 15px;
        font-size: 18px;
        color: #0033A0;
        pointer-events: none;
    }

    /* Lista de Comunicados */
    .communications-page .list-group {
        border-radius: 10px;
        overflow: hidden;
    }

    .communications-page .list-group-item {
        background: white;
        padding: 15px;
        border: none;
        border-left: 5px solid #0033A0;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        margin-bottom: 10px;
        border-radius: 5px;
    }

    .communications-page .list-group-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
</style>
@endsection
