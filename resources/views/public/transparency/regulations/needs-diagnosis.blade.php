@extends('public.transparency.shared.sidebar')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container py-4">
    <h1 class="mb-4">Diagnóstico de Necesidades e Identificación de Problemas</h1>

    <p>En esta sección se presenta la metodología, herramientas y resultados relacionados con el diagnóstico institucional realizado por la entidad.</p>

    <h3 class="mt-4">Metodología Utilizada</h3>
    <ul>
        <li>Entrevistas con grupos de interés</li>
        <li>Análisis FODA institucional</li>
        <li>Encuestas ciudadanas</li>
        <li>Revisión documental</li>
    </ul>

    <h3 class="mt-4">Herramientas de Evaluación</h3>
    <ul>
        <li>Matriz de diagnóstico estratégico</li>
        <li>Formato de priorización de problemas</li>
        <li>Encuestas de satisfacción y percepción</li>
    </ul>

    <h3 class="mt-4">Documentos Disponibles</h3>
    <ul>
        <li><a href="{{ asset('documents/diagnostico_2024.pdf') }}" target="_blank">Diagnóstico Institucional 2024 (PDF)</a></li>
        <li><a href="{{ asset('documents/encuesta_necesidades.pdf') }}" target="_blank">Informe de Encuesta de Necesidades (PDF)</a></li>
    </ul>
</div>
@endsection
