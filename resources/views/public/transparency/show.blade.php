@extends('layouts.sidebar')

@section('content')
    <div class="container-ms my-5">
        <h1 class="titulo-personalizado">{{ $seccionData->titulo }}</h1>
        <p>{!! nl2br(e($seccionData->descripcion)) !!}</p>

        <!-- Mostrar los subelementos de la sección actual -->
        @if ($subElementos->isNotEmpty())
            <ol>
                @foreach ($subElementos as $subElemento)
                    <li><a href="{{ $subElemento->enlace }}" class="text-primary">{{ $subElemento->titulo }}</a></li>
                @endforeach
            </ol>
        @else
            <p>No hay subelementos disponibles para esta sección.</p>
        @endif
    </div>
@endsection

<!-- Incluir el sidebar con subElementos -->
@section('sidebar')
    @include('partials.sidebar', ['subElementos' => $subElementos])
@endsection
