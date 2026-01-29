@extends('layouts.app')
@section('content')
<div class="container my-4">
    <div class="directory-header">
        <h1>Directorio Institucional</h1>
        <p class="lead">Encuentra la información de contacto de nuestros colaboradores de manera rápida y sencilla.</p>
        
        {{-- Formulario de búsqueda mejorado --}}
        <div class="row mt-4 justify-content-center">
            <div class="col-md-8 col-lg-6">
                <form method="GET" action="{{ route('directory') }}" class="search-form">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-lg" placeholder="Buscar por nombre, correo o extensión..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Lista de contactos en tarjetas --}}
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            @forelse($contacts as $contact)
                @php
                    // Genera las iniciales del nombre para el avatar
                    $words = explode(" ", $contact->name);
                    $initials = "";
                    foreach ($words as $word) {
                        $initials .= strtoupper(substr($word, 0, 1));
                        if(strlen($initials) >= 2) break;
                    }
                @endphp
                <div class="contact-card">
                    <div class="contact-avatar">{{ $initials }}</div>
                    <div class="contact-info">
                        <h5>{{ $contact->name }}</h5>
                        <div class="contact-details">
                            @if($contact->phone)
                                <span><i class="bi bi-telephone-fill"></i> {{ $contact->phone }}</span>
                            @endif
                            @if($contact->ext)
                                <span><i class="bi bi-headset"></i> Ext. {{ $contact->ext }}</span>
                            @endif
                            @if($contact->email)
                                <span><i class="bi bi-envelope-fill"></i> {{ $contact->email }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                {{-- Estado vacío mejorado --}}
                <div class="empty-state">
                    <i class="bi bi-person-x-fill"></i>
                    <p>No se encontraron resultados para tu búsqueda.</p>
                </div>
            @endforelse

            {{-- Controles de Paginación --}}
            @if ($contacts->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $contacts->appends(['search' => request('search')])->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
<style>
    :root {
        --primary-color: #0d6efd; /* Azul de Bootstrap */
        --primary-color-dark: #0a58ca;
        --light-gray: #f8f9fa;
        --text-color: #495057;
        --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        --card-shadow-hover: 0 6px 20px rgba(0, 0, 0, 0.1);
    }
    body {
        background-color: var(--light-gray);
        font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
    }

    /* Encabezado con el título y la búsqueda */
    .directory-header {
        text-align: center;
        padding: 2.5rem 1rem;
        margin-bottom: 2rem;
    }
    .directory-header h1 {
        font-weight: 700;
        color: #212529;
    }
    .directory-header .lead {
        color: #6c757d;
        max-width: 600px;
        margin: 0.5rem auto 0;
    }

    /* Estilo del campo de búsqueda */
    .search-form .form-control {
        border-right: 0;
        border-radius: 50px 0 0 50px;
    }
    .search-form .btn {
        border-radius: 0 50px 50px 0;
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .search-form .btn:hover {
        background-color: var(--primary-color-dark);
        border-color: var(--primary-color-dark);
    }
    .search-form .form-control:focus {
        box-shadow: none;
        border-color: var(--primary-color);
    }

    /* Tarjeta de contacto individual */
    .contact-card {
        display: flex;
        align-items: center;
        background-color: #fff;
        padding: 1rem 1.5rem;
        margin-bottom: 1rem;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-left: 5px solid transparent;
    }
    .contact-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--card-shadow-hover);
        border-left-color: var(--primary-color);
    }

    /* Avatar con iniciales */
    .contact-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: var(--primary-color);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.2rem;
        margin-right: 1.5rem;
    }

    /* Información del contacto */
    .contact-info h5 {
        margin-bottom: 0.25rem;
        font-weight: 600;
        color: #343a40;
    }
    .contact-details {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem 1.5rem;
        color: var(--text-color);
        font-size: 0.9rem;
    }
    .contact-details span {
        display: flex;
        align-items: center;
    }
    .contact-details i {
        margin-right: 0.5rem;
        color: #adb5bd;
    }

    /* Estado vacío */
    .empty-state {
        text-align: center;
        padding: 3rem;
    }
    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
    }
    .empty-state p {
        margin-top: 1rem;
        color: #6c757d;
        font-size: 1.1rem;
    }

    /* Paginación */
    .pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .pagination .page-link {
        color: var(--primary-color);
    }
</style>