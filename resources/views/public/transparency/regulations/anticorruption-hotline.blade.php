@extends('public.transparency.shared.sidebar')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container py-4">
    <h1 class="mb-4">Línea Anticorrupción</h1>

    <p>
        En cumplimiento de lo establecido en el artículo 73 de la <strong>Ley 1474 de 2011</strong> – Estatuto Anticorrupción, la Gobernación del Putumayo pone a disposición de la ciudadanía su <strong>Línea Anticorrupción</strong> como un canal oficial para recibir denuncias sobre posibles actos de corrupción.
    </p>

    <h4 class="mt-4">¿Qué es la Línea Anticorrupción?</h4>
    <p>
        Es un mecanismo de atención directa para que ciudadanos, contratistas, funcionarios y cualquier persona pueda reportar hechos que atenten contra la integridad pública, la transparencia y la legalidad en la gestión institucional.
    </p>

    <h4 class="mt-4">¿Qué tipo de denuncias se pueden presentar?</h4>
    <ul>
        <li>Presuntos actos de corrupción administrativa o contractual</li>
        <li>Irregularidades en trámites, servicios o atención al ciudadano</li>
        <li>Mal manejo de recursos públicos</li>
        <li>Conflictos de interés o tráfico de influencias</li>
    </ul>

    <h4 class="mt-4">Canales disponibles</h4>
    <div class="row">
        <div class="col-md-6">
            <p><strong>📞 Línea telefónica:</strong></p>
            <p>Teléfono: <strong>+57 (608) 4201515 Ext. 1101</strong></p>
            <p>Horario de atención: Lunes a Viernes de 8:00 a.m. a 5:00 p.m.</p>
        </div>
        <div class="col-md-6">
            <p><strong>📧 Correo electrónico:</strong></p>
            <p><a href="mailto:anticorrupcion@putumayo.gov.co">anticorrupcion@putumayo.gov.co</a></p>
            <p>Disponible 24/7 para recepción de denuncias.</p>
        </div>
    </div>

    <h4 class="mt-4">Formulario web (en construcción)</h4>
    <p>
        Próximamente se habilitará un formulario en línea que permitirá radicar denuncias de forma anónima y segura.
    </p>

    <div class="alert alert-info mt-4">
        <strong>Confidencialidad:</strong> Toda la información recibida será tratada de manera confidencial conforme a la Ley 1581 de 2012 sobre protección de datos personales.
    </div>

    <p class="mt-5">
        Para mayor información sobre mecanismos de control social y derechos del ciudadano, visite la sección de <a href="{{ url('/transparencia') }}">Transparencia y acceso a la información pública</a>.
    </p>
</div>
@endsection
