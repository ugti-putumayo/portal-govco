<section class="about-hero">
    <div class="container text-center">
        <h2 class="section-title">SOBRE NOSOTROS</h2>
        <p class="section-subtitle">Conoce más acerca del propósito, misión y valores de nuestro equipo.</p>
    </div>
</section>

<section class="info-section">
    <div class="container-fluid px-5">
        <div class="row justify-content-center">
            <div class="col-lg-3 col-md-6">
                <div class="info-box">
                    <div class="icon-wrapper">
                        <i class="fas fa-bullseye icon"></i>
                    </div>
                    <h3>Nuestra Misión</h3>
                    <p>Comprometidos con ofrecer acceso a información confiable, brindando valor y apoyo a los ciudadanos.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="info-box">
                    <div class="icon-wrapper">
                        <i class="fas fa-eye icon"></i>
                    </div>
                    <h3>Nuestra Visión</h3>
                    <p>Aspiramos a ser referencia en transparencia e innovación para la comunidad.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="info-box">
                    <div class="icon-wrapper">
                        <i class="fas fa-users icon"></i>
                    </div>
                    <h3>Conoce a Nuestro Equipo</h3>
                    <p>Un equipo comprometido con la gestión pública y la transparencia.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .container-fluid {
        max-width: 1350px;
    }
    
    .about-hero {
        padding: 30px 0;
        text-align: center;
        margin-bottom: 20px;
        width: 100%;
        background: transparent !important; /* Fondo transparente */
    }

    .section-title {
        font-size: 2.2rem;
        font-weight: bold;
        text-transform: uppercase;
        color: #002F6C;
        margin-bottom: 10px;
    }

    .section-subtitle {
        font-size: 1.2rem;
        font-weight: 400;
        color: #444;
        max-width: 700px;
        margin: auto;
    }

    /* Sección Info */
    .info-section {
        padding: 40px 0;
        background: #F4F4F9;
    }

    .info-box {
        padding: 30px;
        border-radius: 10px;
        background-color: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-in-out;
        text-align: center;
        max-width: 400px;
        margin: auto;
    }

    .info-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .info-box h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 15px;
        color: #002F6C;
    }

    .info-box p {
        text-align: center;
        font-size: 1rem;
        color: #444;
    }

    /* ICONOS */
    .icon-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #002F6C;
        margin: 0 auto;
    }

    .icon {
        font-size: 35px;
        color: white;
    }

    /* Responsivo */
    @media (max-width: 992px) {
        .info-box {
            max-width: 100%;
        }
    }
</style>
