<div id="custom-carousel">
    <h2>Enlaces de Interés</h2>
    <p>Ingresa a los siguientes enlaces de interés de nuestro municipio</p>

    <div class="carousel-container">
        <button id="carousel-prev">&#10094;</button>
        <div class="carousel-slide-container">
            <div class="carousel-slide-track">
                <!-- Carousel Items -->
                <div class="carousel-slide">
                    <a href="https://www.postal.gov.co" target="_blank">
                        <img src="/img/interest-links/0ea90-logopostal.png" alt="Postal">
                    </a>
                </div>
                <div class="carousel-slide">
                    <a href="https://www.colombiacompra.gov.co" target="_blank">
                        <img src="/img/interest-links/1fbca-colombia_compra_eficiente.png" alt="Colombia Compra Eficiente">
                    </a>
                </div>
                <div class="carousel-slide">
                    <a href="https://www.mintic.gov.co" target="_blank">
                        <img src="/img/interest-links/3c7bd-logo_del_ministerio_de_tecnologias_de_la_informacion_y_las_comunicaciones_de_colombia_2022-2026.png" alt="Ministerio de las Tecnologías">
                    </a>
                </div>
                <div class="carousel-slide">
                    <a href="https://www.presidencia.gov.co" target="_blank">
                        <img src="/img/interest-links/8b3ef-presi.jpeg" alt="Presidencia de la República">
                    </a>
                </div>
                <div class="carousel-slide">
                    <a href="https://www.serviciodeempleo.gov.co" target="_blank">
                        <img src="/img/interest-links/29fa9-logos_spe2024-40.png" alt="Servicio Público de Empleo">
                    </a>
                </div>
                <div class="carousel-slide">
                    <a href="https://www.colombia.co" target="_blank">
                        <img src="/img/interest-links/60e60-asa07f5012f32278cd5c.png" alt="CO">
                    </a>
                </div>
                <div class="carousel-slide">
                    <a href="https://www.sisben.gov.co" target="_blank">
                        <img src="/img/interest-links/24107-sisben.png" alt="Sisbén">
                    </a>
                </div>
                <div class="carousel-slide">
                    <a href="https://www.gov.co" target="_blank">
                        <img src="/img/interest-links/a60d0-gov.png" alt="Gov.CO">
                    </a>
                </div>
                <div class="carousel-slide">
                    <a href="https://www.urnadecristal.gov.co" target="_blank">
                        <img src="/img/interest-links/ae7ba-logo-urna.png" alt="Urna de Cristal">
                    </a>
                </div>
                <div class="carousel-slide">
                    <a href="https://www.funcionpublica.gov.co" target="_blank">
                        <img src="/img/interest-links/af0d9-funcion-publica.png" alt="Función Pública">
                    </a>
                </div>
                <div class="carousel-slide">
                    <a href="https://www.metrologia.gov.co" target="_blank">
                        <img src="/img/interest-links/ea995-hora.png" alt="Instituto Nacional de Metrología">
                    </a>
                </div>
                <div class="carousel-slide">
                    <a href="https://www.portalterritorial.gov.co" target="_blank">
                        <img src="/img/interest-links/ee6cd-logoportal4.png" alt="Portal Territorial de Colombia">
                    </a>
                </div>
                <!-- Fin de Carousel Items -->
            </div>
        </div>
        <button id="carousel-next">&#10095;</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let currentSlide = 0;
    const itemsToShow = 3; // Número de elementos visibles
    const items = document.querySelectorAll('.carousel-slide');
    const totalItems = items.length;
    const track = document.querySelector('.carousel-slide-track');
    
    function updateSlidePosition() {
        const slideWidth = items[0].offsetWidth;
        track.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
    }

    function nextSlide() {
        if (currentSlide < totalItems - itemsToShow) {
            currentSlide++;
        } else {
            currentSlide = 0; // Reinicia si llegamos al final
        }
        updateSlidePosition();
    }

    function prevSlide() {
        if (currentSlide > 0) {
            currentSlide--;
        } else {
            currentSlide = totalItems - itemsToShow;
        }
        updateSlidePosition();
    }

    // Asignar eventos a los botones
    document.querySelector('#carousel-prev').addEventListener('click', prevSlide);
    document.querySelector('#carousel-next').addEventListener('click', nextSlide);

    // Deslizamiento automático cada 5 segundos
    let autoSlideInterval = setInterval(nextSlide, 5000);

    // Pausar el deslizamiento automático al pasar el ratón sobre el carrusel
    track.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));

    // Reanudar el deslizamiento automático cuando se quita el ratón
    track.addEventListener('mouseleave', () => {
        autoSlideInterval = setInterval(nextSlide, 5000);
    });

    // Ajustar el tamaño de la ventana y redimensionar
    window.addEventListener('resize', updateSlidePosition);
});
</script>

<style>
#custom-carousel {
    text-align: center;
    padding: 20px;
}

#custom-carousel h2 {
    font-size: 2rem;
    margin-bottom: 10px;
}

#custom-carousel p {
    font-size: 1.2rem;
    margin-bottom: 20px;
}

.carousel-container {
    max-width: 1200px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    width: 90%;
    margin: 0 auto;
    margin-bottom: 2rem;
}

.carousel-slide-container {
    overflow: hidden;
    width: 100%;
    height: auto;
}

.carousel-slide-track {
    display: flex;
    transition: transform 0.5s ease;
    justify-content: space-between; /* Ajusta el espaciado entre los elementos */
}

.carousel-slide {
    flex: 0 0 auto; /* Evitar que los elementos se encogan o crezcan */
    width: 160px; /* Tamaño fijo para las imágenes */
    padding: 0 15px; /* Añadir espacio alrededor de cada imagen */
    box-sizing: border-box;
}

.carousel-slide img {
    width: 100%;
    height: auto;
    object-fit: contain;
}

#carousel-prev, #carousel-next {
    background-color: transparent;
    border: none;
    cursor: pointer;
    font-size: 2rem;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1;
    padding: 0 20px;
}

#carousel-prev {
    left: 0;
    margin-left: 10px;
    color: var(--govco-secondary-color);
}

#carousel-next {
    right: 0;
    margin-right: 10px;
    color: var(--govco-secondary-color);
}
</style>
