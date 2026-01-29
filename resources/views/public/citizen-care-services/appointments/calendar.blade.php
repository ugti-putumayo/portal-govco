@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Solicitud de Citas - Agendar Tu Cita</h1>
    
    <div id="calendar"></div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.4/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.4/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.4/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.4/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/list@6.1.4/index.global.min.js"></script>

<script>
var holidays = @json($holidays);
console.log('Días festivos:', holidays);

function isHoliday(date) {
    const dateString = date.toISOString().split('T')[0];
    return holidays.includes(dateString);
}

function formatAMPM(hourString) {
    const [hour, minute] = hourString.split(':');
    let hours = parseInt(hour);
    const suffix = hours >= 12 ? "PM" : "AM";
    hours = hours % 12 || 12;
    return `${hours}:${minute} ${suffix}`;
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var appointments = @json($appointments); // Citas asignadas

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        locale: 'es',
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día'
        },
        businessHours: { // Horarios de citas
            daysOfWeek: [1, 2, 3, 4, 5], // De lunes a viernes
            startTime: '08:00',
            endTime: '16:15'
        },
        dateClick: function(info) {
            const selectedDate = new Date(info.date);
            if (selectedDate.getDay() === 6 || selectedDate.getDay() === 0 || isHoliday(selectedDate)) {
                alert("No se puede agendar cita en fines de semana o festivos");
            } else {
                // Redirigir al formulario con la fecha seleccionada
                window.location.href = `/form?date=${info.dateStr}`;
            }
        },
        events: appointments.map(function(appointment) {
            const formattedHour = formatAMPM(appointment.hour);
            return {
                title: `● ${formattedHour} ${appointment.name}`,
                start: appointment.date,
                backgroundColor: '#068460', // Verde oscuro
                borderColor: '#90EE90',
                extendedProps: {
                    document_number: appointment.document_number,
                    name: appointment.name,
                    email: appointment.email,
                    phone: appointment.phone,
                    date: appointment.date,
                    hour: formattedHour
                }
            };
        })
    });

    calendar.render();
});
</script>
@endpush

<style>
    .custom-tooltip {
        background-color: #38b000; /* Fondo verde */
        color: white;
        padding: 10px;
        border-radius: 4px;
        font-size: 12px;
        position: fixed;
        z-index: 1000;
        display: none;
        box-shadow: 0 2px 10px rgba(6, 132, 96, 1);
    }

    .fc-event-title {
        font-weight: bold;
        font-size: 14px;
    }

    #calendar {
        box-shadow: 0px 4px 12px rgba(0, 72, 132, 0.1);
        border-radius: 8px;
        padding: 10px;
        background-color: white;
    }

    .fc-daygrid-event {
        border-radius: 8px;
        padding: 2px 4px;
        font-size: 12px;
        font-weight: bold;
        background-color: #9ef01a !important; /* Verde claro */
        border: none;
        color: white !important;
    }

    .fc-toolbar-title {
        font-size: 18px;
        font-weight: bold;
        text-transform: capitalize;
    }

    .fc-button {
        background-color: #0056b3 !important;
        border: none !important;
        color: white !important;
    }

    .fc-button:hover {
        background-color: #003f7f !important;
    }

    .disabled {
        background-color: #e0e0e0 !important;
        color: #a0a0a0 !important;
        cursor: not-allowed;
    }
</style>
