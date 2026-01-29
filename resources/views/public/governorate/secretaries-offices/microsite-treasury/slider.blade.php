<div class="container-fluid slider-wrapper">
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        
        <!-- Indicadores -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="2"></button>
        </div>

        <!-- Contenido del Slider -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('img/slider/slider_1.jpg') }}" class="d-block w-100" alt="Slide 1">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('img/slider/slider_2.jpg') }}" class="d-block w-100" alt="Slide 2">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('img/slider/slider_3.jpg') }}" class="d-block w-100" alt="Slide 3">
            </div>
        </div>

        <!-- Controles de navegación -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>

        <!-- Botón de Pausa/Reproducir -->
        <div class="slider-controls">
            <button id="pausePlayBtn" class="btn btn-primary">⏸ Pausar</button>
        </div>
    </div>
</div>

<style>
    /* CONTENEDOR PRINCIPAL */
    .slider-wrapper {
        width: 100%;
        max-width: 1600px; /* Ajuste para que cubra toda la pantalla */
        margin: auto;
        position: relative;
        overflow: hidden;
    }

    .carousel {
        width: 100%;
        border-radius: 10px;
        overflow: hidden;
    }

    .carousel img {
        width: 100%;
        max-height: 550px;
        object-fit: cover;
        border-radius: 5px;
    }

    /* INDICADORES */
    .carousel-indicators button {
        background-color: #fff;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin: 5px;
    }

    /* BOTONES DE NAVEGACIÓN */
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color: rgba(0, 0, 0, 0.5);
        padding: 15px;
        border-radius: 50%;
    }

    /* BOTÓN DE PAUSA / REPRODUCIR */
    .slider-controls {
        position: absolute;
        bottom: 15px;
        left: 20px;
    }

    #pausePlayBtn {
        background: #0056b3;
        border: none;
        padding: 8px 15px;
        font-size: 16px;
        border-radius: 5px;
        color: white;
        cursor: pointer;
        transition: 0.3s;
    }

    #pausePlayBtn:hover {
        background: #003a7b;
    }

    /* RESPONSIVO */
    @media (max-width: 1200px) {
        .carousel img {
            max-height: 450px;
        }
    }

    @media (max-width: 768px) {
        .carousel img {
            max-height: 350px;
        }

        .carousel-indicators button {
            width: 10px;
            height: 10px;
        }

        #pausePlayBtn {
            font-size: 14px;
            padding: 6px 12px;
        }
    }

    @media (max-width: 576px) {
        .carousel img {
            max-height: 280px;
        }

        .slider-controls {
            bottom: 10px;
            left: 10px;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let carousel = document.querySelector("#carouselExample");
        let pausePlayBtn = document.getElementById("pausePlayBtn");
        let carouselInstance = new bootstrap.Carousel(carousel, { interval: 4000 });

        pausePlayBtn.addEventListener("click", function () {
            if (pausePlayBtn.innerHTML.includes("⏸")) {
                carouselInstance.pause();
                pausePlayBtn.innerHTML = "▶ Reproducir";
            } else {
                carouselInstance.cycle();
                pausePlayBtn.innerHTML = "⏸ Pausar";
            }
        });
    });
</script>

