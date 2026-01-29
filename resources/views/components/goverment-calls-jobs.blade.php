<div class="convocatorias-wrapper">
    <div class="container">
        <h2 class="convocatorias-title">
        <a href="{{ route('goverment-calls') }}">CONVOCATORIAS RECIENTES</a>
        </h2>
        @if(isset($convocatorias) && $convocatorias->count() > 0)
            <div class="swiper convocatorias-swiper">
                <div class="swiper-wrapper">
                    @foreach($convocatorias as $convocatoria)
                        <div class="swiper-slide">
                            <a href="{{ asset('storage/' . $convocatoria->attachment) }}" target="_blank" class="convocatoria-card">
                                <h3 class="convocatoria-titulo">{{ $convocatoria->title }}</h3>
                                <p class="convocatoria-descripcion">
                                    <span class="fecha">
                                        <strong>{{ \Carbon\Carbon::parse($convocatoria->publication_date)->format('d M Y') }}</strong>
                                    </span>
                                    – {{ Str::limit($convocatoria->content, 200) }}
                                </p>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        @else
            <p class="text-center">No hay convocatorias disponibles.</p>
        @endif
    </div>
</div>

{{-- SwiperJS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        new Swiper('.convocatorias-swiper', {
            direction: 'vertical',
            slidesPerView: 1,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            }
        });
    });
</script>

<style>
    .convocatorias-wrapper {
        background-color: transparent;
        padding: 2rem 1rem;
    }

    .convocatorias-title {
        text-align: center;
        font-size: 2rem; /* ⬆️ Aumentado para mejor visibilidad */
        font-weight: bold;
        color: #004884;
        margin-bottom: 2rem;
    }

    .convocatorias-title a {
        text-decoration: none;
        color: inherit;
    }

    .convocatorias-title a:hover {
        text-decoration: underline;
    }

    .convocatorias-swiper {
        height: 160px;
        overflow: hidden;
    }

    .swiper-slide {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .convocatoria-card {
        display: block;
        background: #fff;
        border-radius: 10px;
        padding: 1.2rem;
        width: 100%;
        max-width: 1000px; /* ⬅️ Ampliado para dar más espacio al contenido */
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        text-decoration: none;
        color: #333;
        transition: 0.2s ease;
    }

    .convocatoria-card:hover {
        background-color: #f2f2f2;
        transform: scale(1.01);
    }

    .convocatoria-titulo {
        font-size: 1.1rem;
        font-weight: bold;
        color: #004884;
        margin-bottom: 0.5rem;
    }

    .convocatoria-descripcion {
        font-size: 0.95rem;
        color: #333;
        text-align: justify;
    }

    .fecha {
        color: #004884;
    }

    .swiper-pagination-bullet {
        background: #004884;
    }
</style>
