@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Trámites y Servicios</h1>
        <p class="lead text-muted">Selecciona el trámite o servicio que deseas consultar.</p>
    </div>

    <div class="row g-4 justify-content-center">
        @forelse ($services as $service)
            <div class="col-12 col-sm-6 col-md-4">
                <a href="{{ $service->url ?? '#' }}" class="service-card" target="_blank">
                    <div class="service-card__icon-wrapper">
                        <img src="{{ asset('icon/' . $service->icon) }}" alt="{{ $service->title }}">
                    </div>
                    
                    <h5 class="service-card__title mt-3">
                        {!! nl2br(e($service->title)) !!}
                    </h5>

                    <div class="service-card__cta">
                        Acceder <span class="arrow">&rarr;</span>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center">
                <p class="lead">No hay trámites o servicios disponibles en este momento.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
<style>
.service-card {
    display: block;
    background-color: #fff;
    border-radius: 12px;
    padding: 2rem 1.5rem;
    text-align: center;
    text-decoration: none;
    border: 1px solid #e9ecef;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    height: 100%;
}

.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    border-color: #007bff;
}

.service-card__icon-wrapper {
    width: 70px;
    height: 70px;
    margin: 0 auto;
    background-color: var(--govco-primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s ease;
}

.service-card:hover .service-card__icon-wrapper {
    background-color: #e7f1ff;
}

.service-card__icon-wrapper img {
    width: 35px;
    height: 35px;
}

.service-card__title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #343a40;
    margin-bottom: 2.5rem;
}

.service-card__cta {
    position: absolute;
    bottom: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
    color: #007bff;
    font-weight: 600;
    font-size: 0.9rem;
    opacity: 0;
    transition: opacity 0.3s ease, bottom 0.3s ease;
}

.service-card:hover .service-card__cta {
    opacity: 1;
    bottom: 1.25rem;
}

.service-card__cta .arrow {
    transition: transform 0.3s ease;
    display: inline-block;
}

.service-card:hover .service-card__cta .arrow {
    transform: translateX(4px);
}
</style>