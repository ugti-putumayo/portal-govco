<footer class="footer-govco"> 
    <div class="footer-top">
        <div class="footer-column footer-column-logos">
            <img src="/logos/logo_govco.png" alt="GOV.CO" class="footer-logo">
            <img src="/logos/logo_vida.png" alt="Colombia Potencia de la Vida" class="footer-logo">
            <img src="/logos/logo_co.svg" alt="CO Colombia" class="footer-logo-small">
        </div>

        <div class="footer-column">
            <h4>Portal Único del Estado Colombiano</h4>
            <p><strong>Dirección:</strong> Calle 7 # 8 - 40 Barrio Centro</p>
            <p><strong>Horarios de Atención:</strong></p>
            <p>&bull; Atención Presencial: Lunes a Viernes de 8:00am a 12:00pm / 2:00pm a 6:00pm</p>
            <p>&bull; Atención Canales Virtuales: Lunes a Viernes de 8:30 am a 4:30 pm.</p>
            <p><strong>Código Postal:</strong> 860001</p>
        </div>

        <div class="footer-column">
            <h4>Contacto</h4>
            <p><strong>Teléfono:</strong></p>
            <p>&bull; Nacional: 01 8000 95 2525</p>
            <p>&bull; Bogotá: +57 601 390 7950</p>
            <p>&bull; Línea Anticorrupción: 01 8000 91 2667</p>
            <p><strong>Correo Institucional:</strong> contactenos@putumayo.gov.co</p>
            <a href="/contact">Solicita una llamada</a> | <a href="/web-call">Llamada web</a> | <a href="/chat">Hablemos en línea</a>
        </div>

        <div class="footer-column">
            <h4>Acerca del sitio</h4>
            <a href="/sitemap">Mapa del sitio</a>
            <a href="/privacy">Políticas de privacidad</a>
            <a href="/copyright">Políticas de derechos de autor</a>
            <a href="/terms">Términos y condiciones</a>
            <h4>Ayudas de accesibilidad</h4>
            <a href="/accessibility">Centro de relevo</a>
            <div class="social-icons">
                <a href="https://twitter.com" target="_blank" rel="noopener noreferrer">Síguenos en Twitter</a>
                <a href="https://facebook.com" target="_blank" rel="noopener noreferrer">Síguenos en Facebook</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p id="local-time"></p>
        
        <p>@2024 - Gobernacion de Putumayo</p>
        <p>Unidad de Gestion de Tecnologia</p>
    </div>
</footer>

<script>
    function updateTime() {
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'America/Bogota' };
        const timeNow = new Intl.DateTimeFormat('es-CO', options).format(new Date());
        document.getElementById('local-time').textContent = timeNow;
    }

    setInterval(updateTime, 1000);
    updateTime();
</script>

<style scoped>
.footer-govco {
    background-color: #004884; /* Azul fuerte */
    color: #FFFFFF;
    padding: 30px 0;
    font-family: Arial, sans-serif;
}

.footer-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0 40px;
    border-bottom: 4px solid #003F72; /* Línea de separación */
}

.footer-column {
    flex: 1;
    max-width: 250px;
    margin: 20px;
}

.footer-column h4 {
    font-size: 16px;
    margin-bottom: 10px;
    color: #FFFFFF;
    font-weight: bold;
}

.footer-column p,
.footer-column a {
    color: #FFFFFF;
    font-size: 14px;
    margin-bottom: 5px;
}

.footer-column a {
    text-decoration: none;
    color: #FFFFFF;
}

.footer-column a:hover {
    color: #FFCC00; /* Color de resalto amarillo */
}

/* Estilos para la columna de los logos */
.footer-column-logos {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.footer-logo {
    width: 120px;
    height: auto;
    margin-bottom: 15px;
}

.footer-logo-small {
    width: 80px;
    height: auto;
    margin-bottom: 0;
}

.social-icons a {
    display: block;
    margin-bottom: 5px;
}

.footer-bottom {
    text-align: center;
    padding: 10px 0;
    background-color: #004080; /* Azul oscuro */
}

.footer-bottom p {
    font-size: 14px;
    color: #FFFFFF;
    margin: 0;
}

/* Media Queries para el footer en pantallas pequeñas */
@media (max-width: 768px) {
    .footer-top {
        flex-direction: column;
        padding: 0 20px;
    }

    .footer-column {
        max-width: 100%;
        margin: 10px 0;
        text-align: center;
    }

    .footer-column-logos {
        flex-direction: row;
        justify-content: center;
    }

    .footer-logo {
        width: 80px;
        margin-right: 10px;
    }

    .social-icons {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }

    .social-icons a {
        margin: 0 10px;
    }

    .footer-bottom {
        font-size: 12px;
        padding: 10px 20px;
    }
}

/* Estilos para el modo de alto contraste */
body.high-contrast .footer-govco {
    background-color: #000; /* Fondo negro */
    color: #fff;            /* Texto blanco */
}

body.high-contrast .footer-govco a {
    color: #0ff;            /* Enlaces en cian */
}

body.high-contrast .footer-govco a:hover {
    color: #fff;            /* Enlaces en blanco al pasar el cursor */
}

body.high-contrast .footer-column h4 {
    color: #fff;            /* Encabezados en blanco */
}

body.high-contrast .footer-column p {
    color: #fff;            /* Párrafos en blanco */
}

body.high-contrast .footer-logo {
    filter: invert(1);      /* Invertir colores de los logos */
}

body.high-contrast .footer-bottom {
    border-top: 1px solid #fff; /* Borde superior en blanco */
}

body.high-contrast .footer-bottom p {
    color: #fff;            /* Texto en blanco */
}

body.high-contrast .social-icons a {
    color: #0ff;            /* Iconos de redes sociales en cian */
}

body.high-contrast .social-icons a:hover {
    color: #fff;            /* Iconos en blanco al pasar el cursor */
}

body.high-contrast .social-icons a img {
    filter: invert(1);      /* Invertir colores de los iconos si son imágenes */
}

/* Media Queries para el modo de alto contraste en pantallas pequeñas */
@media (max-width: 768px) {
    body.high-contrast .footer-top {
        /* Mantiene los ajustes responsivos */
    }
}
</style>
