@extends('layouts.app')

@section('content')
<div class="container mt-4 judicial-notices-page">
    <h2 class="notices-title">Notificaciones, Autos y Edictos</h2>

    <p class="notices-intro">
        De conformidad con el artículo 197 de la Ley 1473 de 2011, la Gobernación del Putumayo ha creado el buzón de correo electrónico 
        <a href="mailto:notificaciones.judiciales@putumayo.gov.co" class="notices-link">notificaciones.judiciales@putumayo.gov.co</a> 
        exclusivamente para recibir notificaciones judiciales. Este correo estará a cargo del Departamento Administrativo Jurídico de la Gobernación.
    </p>

    <p class="notices-intro">
        Si la hora de recibido, de acuerdo con el servidor de correo de la Gobernación del Putumayo, corresponde a un horario no hábil, 
        automáticamente quedará recibido con fecha del siguiente día hábil a las 08:00 a.m.
    </p>

    <div class="search-container">
        <form method="GET" action="{{ route('judicial_notices.index') }}" class="search-form">
            <input type="search" name="search" placeholder="Buscar por título o descripción..." value="{{ request('search') }}" class="search-bar">
            <button type="submit" class="search-button">Buscar</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table notices-table">
            <thead>
                <tr>
                    {{-- CAMPOS CORREGIDOS --}}
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Fecha de Publicación</th>
                    <th>Enlace</th>
                </tr>
            </thead>
            <tbody>
                @forelse($judicial_notices as $notice)
                    <tr>
                        <td>{{ $notice->title }}</td>
                        <td>{{ Str::limit($notice->description, 100) }}</td>
                        <td>{{ $notice->date ? $notice->date->format('d/m/Y') : 'N/A' }}</td>      
                        <td>
                            @if(!empty($notice->document))
                                <a href="{{ asset('storage/' . $notice->document) }}" class="document-link" target="_blank">Ver Documento</a>
                            @elseif(!empty($notice->link))
                                <a href="{{ $notice->link }}" class="document-link" target="_blank">Ver Enlace</a>
                            @else
                                <span class="text-muted">No disponible</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No se encontraron resultados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $judicial_notices->appends(request()->input())->links('vendor.pagination.custom') }}
    </div>
</div>

<style>
    .judicial-notices-page {
        background-color: var(--govco-background-color, #f8f9fa);
        font-family: var(--govco-font-primary, 'Arial', sans-serif);
        padding: 20px;
        max-width: 90%;
        margin: auto;
        border-radius: calc(var(--govco-border-radius, 5px) * 2);
    }

    .judicial-notices-page .notices-title {
        font-size: 1.8rem;
        font-weight: bold;
        text-align: center;
        color: var(--govco-secondary-color, #004884);
        margin-bottom: 15px;
    }

    .judicial-notices-page .notices-intro {
        text-align: justify;
        font-size: 1rem;
        color: var(--govco-tertiary-color, #333);
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .judicial-notices-page .notices-link {
        color: var(--govco-secondary-color, #004884);
        font-weight: bold;
        text-decoration: none;
    }

    .judicial-notices-page .notices-link:hover {
        text-decoration: underline;
    }

    .judicial-notices-page .search-container {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 20px;
    }

    .judicial-notices-page .search-form {
        display: flex;
        width: 100%;
        max-width: 400px;
    }

    .judicial-notices-page .search-bar {
        flex-grow: 1;
        padding: 10px;
        border: 2px solid var(--govco-secondary-color, #004884);
        border-radius: var(--govco-border-radius, 5px) 0 0 var(--govco-border-radius, 5px);
        font-size: 16px;
        outline: none;
        transition: 0.3s;
    }

    .judicial-notices-page .search-bar:focus {
        border-color: var(--govco-secondary-color, #004884);
        box-shadow: 0 0 5px rgba(0, 72, 132, 1);
    }

    .judicial-notices-page .search-button {
        background-color: var(--govco-secondary-color, #004884);
        color: var(--govco-white-color, white);
        border: 2px solid var(--govco-secondary-color, #004884);
        padding: 10px 15px;
        border-radius: 0 var(--govco-border-radius, 5px) var(--govco-border-radius, 5px) 0;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }

    .judicial-notices-page .search-button:hover {
        background-color: var(--govco-secondary-color, #004884);
        border-color: var(--govco-secondary-color, #004884);
    }

    .judicial-notices-page .notices-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .judicial-notices-page .notices-table th {
        background-color: var(--govco-secondary-color, #004884);
        color: var(--govco-white-color, white);
        padding: 12px;
        text-align: left;
    }

    .judicial-notices-page .notices-table td {
        padding: 12px;
        border-bottom: 1px solid #ddd; 
    }

    .judicial-notices-page .notices-table tr:hover {
        background-color: var(--govco-gray-color, #f1f1f1);
    }

    /* Enlace de documento */
    .judicial-notices-page .document-link {
        color: var(--govco-secondary-color, #004884);
        text-decoration: none;
        font-weight: bold;
    }

    .judicial-notices-page .document-link:hover {
        text-decoration: underline;
    }
</style>
@endsection