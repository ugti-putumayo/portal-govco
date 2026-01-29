@extends('public.transparency.shared.sidebar')
@section('title', 'Misión')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container-ms">
    <div class="container my-5">
        <h1 class="titulo-personalizado">Misión</h1>
        <p>Promover un auténtico desarrollo económico sostenible, a través de la armonización de las estrategias del Departamento, con las estrategias locales, nacionales e internacionales, bajo los principios de transparencia, equidad, justicia social, conservación y aprovechamiento de la riqueza natural del departamento del Putumayo. Acompañar a las entidades territoriales y pueblos étnicos del Departamento, en la promoción del desarrollo y bienestar social, partiendo de las visiones propias y bajo los principios de coordinación, complementariedad, concurrencia y subsidiariedad.</p>
    </div>

    <div class="container my-5">
        <h1 class="titulo-personalizado">Visión</h1>
        <p>En el año 2026 el departamento del Putumayo, en el propósito de ser un territorio de paz y apoyado en la educación y la salud como motores de transformación, cuenta con las bases para ser el centro de desarrollo económico sostenible del sur del país; con las capacidades suficientes para que los 13 municipios del Departamento, en forma autónoma y articulada, puedan conservar sus ecosistemas, generar riqueza para todos, bajo los principios de igualdad y equidad, así como aprovechar y conservar la condición Andino Amazónica, alcanzando el buen vivir de sus habitantes.</p>
    </div>

    <div class="container my-5">
        <h1 class="titulo-personalizado">Objetivo</h1>
        <p>En su artículo 298 de la Constitución Política de Colombia de 1991 el Departamento de Putumayo tiene autonomía para la administración de los asuntos seccionales y la planificación y promoción del desarrollo económico y social dentro de su territorio en los términos establecidos por la Constitución.</p>
    </div>

    <div class="container my-5">
        <h1 class="titulo-personalizado">Funciones</h1>
        <p>El Departamento ejerce funciones administrativas, de coordinación, de complementariedad de la acción municipal, de intermediación entre la Nación y los Municipios y de prestación de los servicios que determinen la Constitución y las leyes. La ley reglamentará lo relacionado con el ejercicio de las atribuciones que la Constitución les otorga.</p>
    </div>
</div>
@endsection