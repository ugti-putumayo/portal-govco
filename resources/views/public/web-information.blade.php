@extends('public.transparency.shared.sidebar')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 text-center">

            <img 
                src="{{ asset('img/construction/web_construction.svg') }}" 
                alt="Sitio en construcción"
                class="img-fluid" 
                style="max-width: 400px;"
            >

            {{-- Mensaje principal --}}
            <h1 class="mt-4 mb-3" style="font-weight: 700;">
                Sitio en Construcción
            </h1>
            
            {{-- Mensaje secundario --}}
            <p class="lead text-muted">
                Estamos trabajando para mejorar esta sección y ofrecerte la mejor información.
                <br>
                Por favor, vuelve pronto.
            </p>

        </div>
    </div>
</div>
@endsection