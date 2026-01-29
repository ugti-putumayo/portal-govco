@extends('dashboard.dashboard') 
<script>
    window.userIsAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
</script>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
function deleteEvent(eventId) {
    if (!confirm('¿Seguro que deseas eliminar este evento?')) return;

    fetch(`/dashboard/events/${eventId}`, {
        method: "DELETE",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(({ status, body }) => {
        if (status === 200) {
            alert(body.message || "Evento eliminado con éxito.");
            location.reload();
        } else {
            console.error("Error al eliminar:", body);
            alert(body.message || "No se pudo eliminar el evento.");
        }
    })
    .catch(error => {
        console.error("Error inesperado:", error);
        alert("Hubo un problema al eliminar el evento.");
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('search-input');
    const filterSelect = document.getElementById('filter-category');
    const searchBtn = document.querySelector('.search-btn');

    function searchEvent() {
        const category = filterSelect.value;
        const search = searchInput.value;
        const url = new URL(window.location.href.split('?')[0]);
        if (search) url.searchParams.set('search', search);
        if (category) url.searchParams.set('category', category);
        window.location.href = url.toString();
    }

    searchInput?.addEventListener("keyup", function () {
        if (filterSelect.value === "") {
            searchEvent();
        }
    });

    searchBtn?.addEventListener("click", function () {
        searchEvent();
    });
});

function toggleSearchInput() {
    const category = document.getElementById('filter-category').value;
    const searchInput = document.getElementById('search-input');
    const dateStart = document.getElementById('date_start');
    const dateEnd = document.getElementById('date_end');

    if (category === 'date') {
        searchInput.style.display = 'none';
        dateStart.style.display = '';
        dateEnd.style.display = '';
    } else {
        searchInput.style.display = '';
        dateStart.style.display = 'none';
        dateEnd.style.display = 'none';
    }
}

const userIsAuthenticated = window.userIsAuthenticated === true || window.userIsAuthenticated === 'true';
document.addEventListener('DOMContentLoaded', function() {
    if (typeof FullCalendar === 'undefined') {
        console.error('FullCalendar no se ha cargado.');
        return;
    }

    var calendarEl = document.getElementById('calendar');
    var events = @json($events);

    var calendarEvents = events.map(event => ({
        id: event.id,
        title: event.title,
        start: event.start ? event.start.replace(' ', 'T') : null,
        end: event.end ? event.end.replace(' ', 'T') : null,
        description: event.description,
        image: event.image,
        location: event.location,
        dependency: event.dependency 
    }));

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay'
        },
        events: calendarEvents,
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día'
        },
        dayHeaderFormat: { weekday: 'short' },
        eventDisplay: 'block',

        dateClick: function(info) {
            if (!userIsAuthenticated) {
                alert('Debes iniciar sesión para crear eventos.');
                return;
            }

            openModalCreateEvent();
            document.getElementById("start").value = info.dateStr + 'T09:00';
        },
        eventClick: function(info) {
            const event = info.event.extendedProps;
            const imageUrl = event.image ? event.image : '/img/default_event.jpg';
            document.getElementById('eventImage').src = imageUrl;
            document.getElementById('eventTitle').textContent = info.event.title;
            document.getElementById('eventDescription').textContent = event.description || '-';
            document.getElementById('eventStart').textContent = info.event.start.toLocaleString();
            document.getElementById('eventEnd').textContent = info.event.end ? info.event.end.toLocaleString() : '-';
            document.getElementById('eventLocation').textContent = event.location || '-';
            document.getElementById('eventDependency').textContent = event.dependency || '-';
            const card = document.getElementById('eventCardFloating');
            const rect = info.jsEvent.target.getBoundingClientRect();
            const cardHeight = 320;
            const offset = 15;
            let topPosition;
            if (rect.bottom + cardHeight + offset < window.innerHeight) {
                topPosition = window.scrollY + rect.bottom + offset;
            } else {
                topPosition = window.scrollY + rect.top - cardHeight - offset;
            }
            card.style.top = topPosition + 'px';
            card.style.left = (window.scrollX + rect.left) + 'px';
            card.style.display = 'block';
            document.getElementById('editEventButton').onclick = function() {
                openEditModal(info.event.id);
            };
        }
    });

    calendar.render();

    const mainContent = document.getElementById('main-content');
    const calendarInstance = calendar;
    window.calendar = calendarInstance;
    if (typeof ResizeObserver !== 'undefined') {
        const resizeObserver = new ResizeObserver(() => {
            calendarInstance.updateSize();
        });
        resizeObserver.observe(mainContent);
    }
});

document.addEventListener('click', function(e) {
    const card = document.getElementById('eventCardFloating');
    if (!card.contains(e.target) && !e.target.closest('.fc-event')) {
        card.style.display = 'none';
    }
});

function closeEventCardModal() {
    document.getElementById('eventCardFloating').style.display = 'none';
}
</script>
@endpush

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css" rel="stylesheet">
<div class="container-modules">
    <div class="navbar">
        <div class="navbar-header-title">
            <img src="{{ asset('icon/event-white.svg') }}" class="submenu-icon-area">
            <h2 class="navbar-title">Calendario de actividades</h2>
        </div>

        <form method="GET" class="navbar-filters" id="eventFilterForm">
            <select id="filter-category" name="category" class="filter-select" onchange="toggleSearchInput()">
                <option value="">Filtrar por...</option>
                <option value="title" {{ request('category') == 'title' ? 'selected' : '' }}>Título</option>
                <option value="description" {{ request('category') == 'description' ? 'selected' : '' }}>Descripción</option>
                <option value="dependency" {{ request('category') == 'dependency' ? 'selected' : '' }}>Dependencia</option>
                <option value="date" {{ request('category') == 'date' ? 'selected' : '' }}>Rango de fechas</option>
            </select>

            {{-- campo de texto --}}
            <input type="text" id="search-input" name="search" class="search-box"
                placeholder="Buscar..." value="{{ request('search') }}" 
                style="{{ request('category') == 'date' ? 'display: none;' : '' }}">

            {{-- campos de fecha --}}
            <input type="date" id="date_start" name="date_start" 
                value="{{ request('date_start') }}" 
                style="{{ request('category') == 'date' ? '' : 'display: none;' }}">

            <input type="date" id="date_end" name="date_end" 
                value="{{ request('date_end') }}" 
                style="{{ request('category') == 'date' ? '' : 'display: none;' }}">

            <button type="submit" class="search-btn">🔍</button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div id="calendar"></div>

    <div id="eventCardFloating" class="event-card-floating" style="display: none;">
        <img id="eventImage" src="" alt="Imagen del evento" class="event-card-image" />
        <div class="event-card-content">
            <h4 id="eventTitle"></h4>
            <p><strong>Descripción:</strong> <span id="eventDescription"></span></p>
            <p><strong>Inicio:</strong> <span id="eventStart"></span></p>
            <p><strong>Fin:</strong> <span id="eventEnd"></span></p>
            <p><strong>Ubicación:</strong> <span id="eventLocation"></span></p>
            <p><strong>Dependencia:</strong> <span id="eventDependency"></span></p>
            <a id="editEventButton" class="btn-icon">
                <img src="{{ asset('icon/edit.svg') }}" alt="Editar">
            </a>
        </div>
    </div>
</div>
@endsection
@include('components.modals.publication.modal-create-event')
@include('components.modals.publication.modal-update-event')
<!-- Estilos -->
<style>
.navbar {
    position: sticky;
    top: 0;
    width: 100%;
    background-color: var(--govco-secondary-color);
    padding: 15px 20px;
    display: flex;
    flex-wrap: wrap; /* si quieres que el buscador baje en pantallas pequeñas */
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
}

.navbar-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.submenu-icon-area {
    width: 30px;
    height: 30px;
    color: white;
}

.navbar-title {
    color: var(--govco-white-color);
    font-family: var(--govco-font-primary);
    font-size: 20px;
    font-weight: bold;
}

.navbar-filters {
    display: flex;
    gap: 10px;
    align-items: center;
}

.filter-select {
    padding: 8px;
    border-radius: var(--govco-border-radius);
    border: 1px solid var(--govco-border-color);
    font-family: var(--govco-font-primary);
}

.search-box {
    padding: 8px;
    border-radius: var(--govco-border-radius);
    border: 1px solid var(--govco-border-color);
    font-family: var(--govco-font-primary);
}

.search-btn {
    padding: 8px 12px;
    border: none;
    background-color: var(--govco-accent-color);
    color: var(--govco-white-color);
    border-radius: var(--govco-border-radius);
    cursor: pointer;
}

.search-btn:hover {
    background-color: var(--govco-primary-color);
}

.title {
    color: var(--govco-primary-color);
    font-family: var(--govco-font-primary);
    margin-bottom: 20px;
}

.alert-success {
    background-color: var(--govco-success-color);
    color: var(--govco-white-color);
    padding: 10px;
    border-radius: var(--govco-border-radius);
    margin-bottom: 15px;
}

#calendar {
    flex: 1;
    margin-top: 20px;
    width: 100%;
    height: calc(100vh - 120px);
    padding: 20px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
}

.fc-header-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 10px;
}

.fc-button-primary {
    background-color: white !important;
    color: #004884 !important;
    border: 1px solid #004884 !important;
    box-shadow: none;
}

.fc-button-primary:hover {
    background-color: #f0f0f0 !important;
    color: #002d5a !important;
}

.fc .fc-button {
    border-radius: 6px !important;
    padding: 6px 12px;
}

.fc-toolbar-title {
    font-size: 1.7rem;
    font-weight: bold;
    color: #004884;
}

.fc-daygrid-day-number {
    font-weight: 600;
    color: #004884;
}

.fc-day-header {
    background-color: #f0f3f6;
    color: #004884;
    padding: 6px 0;
    border-bottom: 1px solid #e0e0e0;
}

.fc-daygrid-day-number {
    color: #333;
    font-weight: bold;
}
.fc-event-title {
    color: #fff;
}

.event-card-floating {
    position: absolute;
    z-index: 9999;
    background: white;
    box-shadow: 0px 0px 10px rgba(0,0,0,0.25);
    border-radius: 8px;
    padding: 15px;
    width: 300px;
    max-width: 90vw;
    font-family: 'Montserrat', sans-serif;
    transition: all 0.2s ease-in-out;
}

.event-card-image {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 10px;
}

.event-card-content h4 {
    margin-top: 0;
    font-weight: bold;
    color: #004884;
}

.btn-icon img {
    width: 24px;
    height: 24px;
    cursor: pointer;
    transition: transform 0.2s ease-in-out;
}

.btn-icon img:hover {
    transform: scale(1.1);
}
</style>