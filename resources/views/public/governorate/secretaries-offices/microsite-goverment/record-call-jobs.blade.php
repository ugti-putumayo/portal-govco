@extends('layouts.app')

@section('title', 'Historial de Convocatorias')

@section('content')
<style>
    .historial-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .buscador-wrapper {
        position: relative;
        margin: 0 auto 2rem auto;
        max-width: 500px;
    }

    .buscador-wrapper input {
        width: 100%;
        border-radius: 25px;
        padding: 10px 20px 10px 20px;
        border: 1px solid #ccc;
        transition: box-shadow 0.2s;
    }

    .buscador-wrapper input:focus {
        outline: none;
        box-shadow: 0 0 5px rgba(0, 72, 132, 1);
        border-color: #004884;
    }

    .buscador-wrapper .icon {
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        color: #004884;
        font-size: 1.1rem;
        pointer-events: none;
    }

    .convocatoria-card {
        background: #fff;
        border-left: 5px solid #004884;
        padding: 1rem 1.5rem;
        margin-bottom: 1rem;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        transition: background 0.2s ease;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .convocatoria-card:hover {
        background-color: #f8f9fa;
    }

    .convocatoria-title {
        font-weight: bold;
        color: #004884;
        margin-bottom: 5px;
    }

    .convocatoria-date {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 8px;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }
</style>

<div class="historial-wrapper">
    <h2 class="text-center text-primary fw-bold mb-4">Historial de Convocatorias</h2>

    <form method="GET" action="{{ route('historial.convocatorias') }}" class="buscador-wrapper">
        <input type="text" name="search" placeholder="Buscar comunicado..." value="{{ request('search') }}">
        <span class="icon"><i class="fas fa-search"></i></span>
    </form>

    @forelse ($convocatorias as $convocatoria)
        <a href="{{ asset('storage/' . $convocatoria->attachment) }}" target="_blank" class="convocatoria-card">
            <div class="convocatoria-title">{{ $convocatoria->title }}</div>
            <div class="convocatoria-date">{{ \Carbon\Carbon::parse($convocatoria->publication_date)->format('d/m/Y') }}</div>
            <div>{{ Str::limit($convocatoria->content, 150) }}</div>
        </a>
    @empty
        <p class="text-center text-muted">No hay convocatorias disponibles.</p>
    @endforelse

    <div class="pagination-wrapper">
        {{ $convocatorias->appends(['search' => request('search')])->links() }}
    </div>
</div>

<!-- Font Awesome para el icono de lupa -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
