@extends('layouts.app')
@section('content')
<div class="container-fluid my-4">
    <div class="locator-wrapper">
        <aside class="locator-sidebar">
            <div class="sidebar-header">
                <h4>Sedes - Gobernación Putumayo</h4>
                <input type="text" id="searchSede" class="form-control" placeholder="Buscar por nombre o dirección...">
            </div>
            <ul id="sedeList">
                @forelse($locations as $location)
                    <li class="sede-item" data-lat="{{ $location->latitude }}" data-lng="{{ $location->longitude }}">
                        <strong>{{ $location->name }}</strong>
                        <span>{{ $location->address }}</span>
                    </li>
                @empty
                    <li class="sede-item text-muted">No hay sedes disponibles.</li>
                @endforelse
            </ul>
        </aside>

        <main class="locator-map-container">
            <div id="map"></div>
            <button id="resetMap" title="Centrar Mapa"><i class="bi bi-arrow-counterclockwise"></i></button>
        </main>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Coordenadas iniciales para Mocoa, Putumayo
        const initialCoords = [1.1495, -76.6455];
        const initialZoom = 14;

        var map = L.map('map').setView(initialCoords, initialZoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var locations = @json($locations);
        var sedeItems = document.querySelectorAll(".sede-item");

        // Crear marcadores
        locations.forEach(function(location, index) {
            var marker = L.marker([location.latitude, location.longitude]).addTo(map)
                .bindPopup(`<b>${location.name}</b><br>${location.address}`);
            
            // Asociar marcador con el item de la lista
            if (sedeItems[index]) {
                sedeItems[index].marker = marker;
            }
        });

        // Función para manejar el estado activo
        function setActive(selectedItem) {
            sedeItems.forEach(item => item.classList.remove('active'));
            if (selectedItem) {
                selectedItem.classList.add('active');
            }
        }

        // Evento de clic en la lista de sedes
        sedeItems.forEach(function(item) {
            item.addEventListener("click", function() {
                var lat = this.getAttribute("data-lat");
                var lng = this.getAttribute("data-lng");
                
                if (lat && lng) {
                    map.setView([lat, lng], 17);
                    this.marker.openPopup();
                    setActive(this);
                }
            });
        });

        // Botón para centrar el mapa
        document.getElementById("resetMap").addEventListener("click", function() {
            map.setView(initialCoords, initialZoom);
            setActive(null); // Quitar selección activa
        });

        // Filtro de búsqueda
        document.getElementById("searchSede").addEventListener("input", function() {
            var searchTerm = this.value.toLowerCase();
            sedeItems.forEach(function(item) {
                var text = item.innerText.toLowerCase();
                item.style.display = text.includes(searchTerm) ? "" : "none";
            });
        });
    });
</script>
@endsection

<style>
    :root {
        --primary-color: #0d6efd;
        --light-gray: #f8f9fa;
        --border-color: #dee2e6;
        --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    body {
        background-color: var(--light-gray);
        font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
    }

    /* Contenedor principal del localizador */
    .locator-wrapper {
        display: flex;
        height: 85vh; /* Altura de la ventana visible */
        background: #fff;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    /* Barra lateral con la lista de sedes */
    .locator-sidebar {
        width: 380px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--border-color);
    }
    .sidebar-header {
        padding: 1.25rem;
        border-bottom: 1px solid var(--border-color);
    }
    .sidebar-header h4 {
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
    .sidebar-header .form-control {
        border-radius: 20px;
    }

    /* Lista de sedes scrollable */
    #sedeList {
        list-style: none;
        padding: 0;
        margin: 0;
        overflow-y: auto;
        flex-grow: 1; /* Ocupa el espacio restante */
    }
    .sede-item {
        padding: 1rem 1.25rem;
        cursor: pointer;
        border-bottom: 1px solid #f1f3f5;
        transition: background-color 0.2s ease;
    }
    .sede-item strong {
        display: block;
        font-weight: 600;
        color: #343a40;
    }
    .sede-item span {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .sede-item:hover {
        background-color: #f8f9fa;
    }
    /* Estado activo para el item seleccionado */
    .sede-item.active {
        background-color: #e7f1ff;
        border-left: 4px solid var(--primary-color);
        padding-left: calc(1.25rem - 4px);
    }

    /* Contenedor del mapa */
    .locator-map-container {
        flex-grow: 1;
        position: relative;
    }
    #map {
        height: 100%;
        width: 100%;
    }

    /* Botón de centrar flotante sobre el mapa */
    #resetMap {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 1000;
        background-color: #fff;
        color: #333;
        border: 2px solid #ccc;
        border-radius: 4px;
        padding: 8px 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        cursor: pointer;
        font-weight: 600;
    }
    #resetMap:hover {
        background-color: #f4f4f4;
    }
    
    /* Diseño responsivo para móviles */
    @media (max-width: 992px) {
        .locator-wrapper {
            flex-direction: column;
            height: auto;
        }
        .locator-sidebar {
            width: 100%;
            height: 45vh; /* Altura fija para la lista */
        }
        .locator-map-container {
            height: 55vh; /* Altura fija para el mapa */
        }
    }
</style>