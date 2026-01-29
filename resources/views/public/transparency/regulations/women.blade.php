@extends('public.transparency.shared.sidebar')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container py-4">
    <h1 class="mb-4">Programas y Servicios para Mujeres</h1>

    <p>
        En cumplimiento de la Ley 1257 de 2008 y otras disposiciones nacionales que promueven la equidad de género, la Gobernación del Putumayo ha caracterizado a las mujeres como un grupo de especial interés y priorización. Por ello, se dispone de esta sección con información y servicios orientados a promover sus derechos, bienestar y participación activa en la vida pública.
    </p>

    <h4 class="mt-4">🔹 Rutas de atención a mujeres víctimas de violencia</h4>
    <ul>
        <li><a href="{{ asset('documents/ruta_atencion_mujeres.pdf') }}" target="_blank">Descargar ruta de atención (PDF)</a></li>
        <li>Línea de atención: 155 (Violencia contra la mujer)</li>
        <li>Centro de Atención Integral – CAIVAS Putumayo</li>
    </ul>

    <h4 class="mt-4">🔹 Programas activos</h4>
    <ul>
        <li>Empoderamiento económico y emprendimiento para mujeres rurales</li>
        <li>Escuela de liderazgo femenino</li>
        <li>Salud sexual y reproductiva con enfoque de género</li>
    </ul>

    <h4 class="mt-4">🔹 Documentos y normativa</h4>
    <ul>
        <li><a href="{{ asset('documents/politica_equidad_genero.pdf') }}" target="_blank">Política pública de equidad de género (PDF)</a></li>
        <li><a href="https://www.funcionpublica.gov.co/eva/gestornormativo/norma.php?i=34416" target="_blank">Ley 1257 de 2008</a></li>
    </ul>

    <h4 class="mt-4">🔹 Enlace de género institucional</h4>
    <p>
        Nombre: [Nombre del enlace]<br>
        Correo: <a href="mailto:genero@putumayo.gov.co">genero@putumayo.gov.co</a><br>
        Teléfono: +57 (608) 4201515 Ext. [XXXX]
    </p>
</div>
@endsection
