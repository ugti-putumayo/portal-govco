@extends('layouts.app')

@section('content')

{{-- 
    1. LÓGICA DE PREPARACIÓN DE DATOS (VIEW MODEL)
    -----------------------------------------------------
    Procesamos la colección $micrositeData que viene del controlador.
--}}
@php
    // A. Detectar Categoría Activa
    // Si hay ?category= en la URL lo usa, sino toma la primera llave de la colección principal
    $activeCategory = request('category', $micrositeData->keys()->first());

    // B. Obtener los años disponibles para esa categoría
    // Si la categoría no existe (url manipulada), devuelve colección vacía
    $yearsGroup = $micrositeData->get($activeCategory) ?? collect();

    // C. Detectar Año Activo
    // Si hay ?year= en la URL lo usa, sino toma el primer año (el más reciente por tu sortKeysDesc)
    $activeYear = request('year', $yearsGroup->keys()->first());

    // D. Obtener los documentos finales
    $documents = $yearsGroup->get($activeYear) ?? collect();
@endphp
<div class="planning-layout">
    
    {{-- SIDEBAR: ITERAMOS LAS CATEGORÍAS (Nivel 1 de $micrositeData) --}}
    <aside>
        <div class="cat-menu-title">Menú de Gestión</div>
        <nav>
            @foreach($micrositeData as $categoryName => $yearsData)
                {{-- Al cambiar categoría, reseteamos el año (year=null) para que tome el default --}}
                <a href="{{ request()->fullUrlWithQuery(['category' => $categoryName, 'year' => null]) }}" 
                   class="cat-link {{ $categoryName === $activeCategory ? 'active' : '' }}">
                   {{ $categoryName }}
                </a>
            @endforeach
        </nav>
    </aside>

    {{-- CONTENIDO PRINCIPAL --}}
    <main>
        <div class="content-header">
            <h1>{{ $activeCategory }}</h1>
            <p class="content-desc">
                Documentación oficial y archivos relacionados. Seleccione la vigencia para filtrar.
            </p>
        </div>

        {{-- TABS: ITERAMOS LOS AÑOS (Nivel 2 de $micrositeData) --}}
        <div class="year-tabs">
            @if($yearsGroup->isNotEmpty())
                @foreach($yearsGroup as $year => $items)
                    <a href="{{ request()->fullUrlWithQuery(['category' => $activeCategory, 'year' => $year]) }}" 
                       class="year-tab {{ (string)$year === (string)$activeYear ? 'active' : '' }}">
                       {{ $year }}
                    </a>
                @endforeach
            @else
                <span class="year-tab">Sin vigencias</span>
            @endif
        </div>

        {{-- LISTA DE DOCUMENTOS (Nivel 3: Items) --}}
        <div class="doc-grid">
            @forelse($documents as $item)
                <div class="doc-card">
                    <div class="doc-meta">
                        <div class="icon-box">PDF</div>
                        <div class="doc-title">{{ $item->title }}</div>
                    </div>
                    
                    {{-- 
                       NOTA: Aquí asumo que usas 'link' o 'image' en tu ContentPageItem para el archivo.
                       Ajusta 'items_file_path' si usas un helper o 'storage/'.
                    --}}
                    @php
                        // Intento detectar dónde está el archivo. Ajusta según tu DB.
                        $filePath = $item->document ?? $item->image ?? '#';
                        $url = Str::startsWith($filePath, 'http') ? $filePath : asset('storage/' . $filePath);
                    @endphp

                    <a href="{{ $url }}" target="_blank" class="btn-download">
                        Ver Documento
                    </a>
                </div>
            @empty
                <div style="padding: 40px; text-align: center; color: #9CA3AF; background: #F9FAFB; border-radius: 8px;">
                    No hay documentos cargados para la vigencia {{ $activeYear }}.
                </div>
            @endforelse
        </div>
    </main>
</div>

@endsection

<style>
    /* ESTILOS MINIMALISTAS (Scoped) */
    .planning-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 50px;
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #374151;
    }

    /* Sidebar - Categorías */
    .cat-menu-title {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #9CA3AF;
        margin-bottom: 15px;
        font-weight: 700;
    }
    .cat-link {
        display: block;
        padding: 12px 16px;
        text-decoration: none;
        color: #4B5563;
        border-radius: 8px;
        margin-bottom: 6px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    .cat-link:hover { background-color: #F9FAFB; color: #111827; }
    .cat-link.active {
        background-color: #EFF6FF;
        color: #1E40AF; /* Azul Institucional */
        font-weight: 600;
    }

    /* Cabecera Contenido */
    .content-header h1 {
        font-size: 2rem;
        color: #111827;
        margin: 0 0 10px 0;
        font-weight: 700;
        letter-spacing: -0.5px;
    }
    .content-desc { color: #6B7280; margin-bottom: 30px; font-size: 1rem; }

    /* Tabs de Años */
    .year-tabs {
        display: flex;
        gap: 10px;
        border-bottom: 1px solid #E5E7EB;
        padding-bottom: 1px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    .year-tab {
        text-decoration: none;
        padding: 8px 16px;
        color: #6B7280;
        border-bottom: 2px solid transparent;
        font-weight: 500;
        transition: all 0.2s;
        font-size: 0.95rem;
    }
    .year-tab:hover { color: #374151; }
    .year-tab.active {
        color: #1E40AF;
        border-bottom-color: #1E40AF;
    }

    /* Tarjetas de Documentos */
    .doc-grid { display: flex; flex-direction: column; gap: 15px; }
    
    .doc-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 10px;
        padding: 20px 24px;
        transition: box-shadow 0.2s, transform 0.1s;
    }
    .doc-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border-color: #D1D5DB;
    }
    .doc-meta { display: flex; align-items: center; gap: 16px; }
    .icon-box {
        width: 40px; height: 40px;
        background: #FEF2F2; /* Rojo muy claro */
        color: #DC2626; /* Rojo PDF */
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 0.75rem;
    }
    .doc-title { font-weight: 600; color: #1F2937; font-size: 1rem; }
    
    .btn-download {
        padding: 8px 16px;
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        background: white;
        color: #374151;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .btn-download:hover {
        border-color: #1E40AF;
        color: #1E40AF;
        background-color: #EFF6FF;
    }

    @media (max-width: 768px) {
        .planning-layout { grid-template-columns: 1fr; gap: 30px; }
        .year-tabs { overflow-x: auto; white-space: nowrap; }
    }
</style>
