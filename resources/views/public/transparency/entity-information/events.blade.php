@extends('public.transparency.shared.sidebar')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
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

@section('main-content')
<div class="container">
    <h1>Calendario de Actividades y Eventos</h1>
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
        </div>
    </div>
</div>
@endsection

<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css" rel="stylesheet">
<style>
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