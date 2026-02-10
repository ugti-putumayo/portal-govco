<footer class="footer-govco">
    
    <div class="footer-split-background">
        
        <div class="footer-card">
            
            <div class="card-header">
                <div class="header-info">
                    <h2 class="gov-title">Gobernación del Putumayo</h2>
                    <h3 class="gov-subtitle">Palacio Departamental</h3>
                </div>
                <div class="header-logo">
                    <img src="/logos/logo_gobernacion_ptyo.png" alt="Logo Gobernación" />
                </div>
            </div>

            <div class="card-body">
                <p>Dirección: Calle 7 # 8 - 40 Barrio Centro, Mocoa, Putumayo, Colombia.</p>
                <p>Código Postal: 860001</p>
                <p>Lunes a Viernes de 8:00 a.m. a 12:00 m y 2:00 p.m. a 6:00 p.m.</p>
                <p>Tel Palacio Departamental: (608) 4201515</p>
                
                <div class="contact-links">
                    <p>Correo electrónico PQRSD: <a href="mailto:contactenos@putumayo.gov.co">contactenos@putumayo.gov.co</a></p>
                    <p>Notificaciones Judiciales: <a href="mailto:notificaciones.judiciales@putumayo.gov.co">notificaciones.judiciales@putumayo.gov.co</a></p>
                </div>

                <div class="secondary-links">
                    <p>Línea anticorrupción: <a href="mailto:transparencia@putumayo.gov.co">transparencia@putumayo.gov.co</a></p>
                    <p>Canal: <a href="/denuncias" class="text-underline">Denuncias Anticorrupción</a></p>
                </div>

                <a href="/headquarters" class="view-more">Ver más sedes</a>

                <div class="stats-row">
                    <span class="chart-icon">📊</span>
                    <a href="/statistical-information-management" class="stats-link">Estadísticas de la Sede Electrónica</a>
                </div>
            </div>

            <div class="card-socials">
                <a href="https://www.facebook.com/gobernaciondelputumayo/?locale=es_LA" class="social-btn" target="_blank" rel="noopener noreferrer">
                    <img src="/icons/facebook.svg" alt="FB"> Gobernación de Putumayo
                </a>
                <a href="https://www.youtube.com/playlist?list=UU1z-DVKIR_TJk1fMUDZx7Vw" class="social-btn" target="_blank" rel="noopener noreferrer">
                    <img src="/icons/youtube.svg" alt="YT"> @gobernacionputumayo
                </a>
            </div>

            <div class="card-footer-menu">
                <a href="/contactenos">CONTÁCTENOS</a>
                <a href="/pqrsd">PQRSD</a>
                <a href="/mapa">MAPA DEL SITIO</a>
                <a href="/encuesta">ENCUESTA DE USABILIDAD</a>
                <a href="/politicas">POLÍTICAS</a>
                <a href="/ayudanos">AYÚDANOS A MEJORAR</a>
            </div>
        </div>
    </div>

    <div class="footer-gov-bar">
        <div class="gov-bar-content">
            <div class="logos-container">
                <img src="/logos/logo_govco.png" alt="GOV.CO" class="logo-white">
                <div class="divider"></div>
                <img src="/logos/logo_co.svg" alt="CO Colombia" class="logo-color">
            </div>
            
            <div class="footer-info-right">
                <p class="copyright-text">@2024 - Gobernación de Putumayo</p>
                <p class="copyright-text">Unidad de Gestión de Tecnología</p>
                <p id="local-time" class="time-text"></p>
            </div>
        </div>
    </div>

</footer>

<script>
    function updateTime() {
        const options = { hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true, timeZone: 'America/Bogota' };
        const timeNow = new Intl.DateTimeFormat('es-CO', options).format(new Date());
        document.getElementById('local-time').textContent = "Hora legal: " + timeNow;
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>

<style scoped>
.footer-govco, .footer-govco * {
    box-sizing: border-box;
    font-family: var(--govco-font-primary);
}

.footer-govco {
    width: 100%;
    display: flex;
    flex-direction: column;
}

.footer-split-background {
    width: 100%;
    background: linear-gradient(
        180deg, 
        var(--govco-white-color) 120px, 
        var(--govco-primary-color) 120px
    );
    padding: 0 20px 60px 20px;
    display: flex;
    justify-content: center;
}

.footer-card {
    background-color: var(--govco-white-color);
    width: 100%;
    max-width: 1200px;
    border-radius: 20px;
    padding: 40px 50px;
    box-shadow: 0 10px 40px rgba(0, 72, 132, 0.25);
    position: relative;
    z-index: 10;
    margin-top: 20px;
}

.card-header {
    background-color: transparent !important; 
    border-bottom: none !important;
    padding: 0 !important;
    
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 25px;
}

.gov-title {
    color: var(--govco-secondary-color);
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 5px 0;
}

.gov-subtitle {
    color: var(--govco-secondary-color);
    font-size: 18px;
    font-weight: 600;
    margin: 0;
}

.header-logo img {
    height: 70px;
    width: auto;
}

.card-body p, .card-body a {
    color: var(--govco-tertiary-color);
    font-size: 14px;
    line-height: 1.6;
    margin: 5px 0;
    text-decoration: none;
}

.card-body a {
    color: var(--govco-secondary-color);
    text-decoration: underline;
}

.contact-links, .secondary-links { 
    margin: 15px 0; 
}

.text-underline { 
    text-decoration: underline; 
}

.view-more {
    display: inline-block;
    margin-top: 10px;
    font-weight: 600;
}

.stats-row {
    margin-top: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-icon { 
    font-size: 20px; 
}

.stats-link {
    font-weight: 700;
    color: var(--govco-secondary-color) !important;
    text-decoration: none !important;
}

.card-socials {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #E5E5E5;
    display: flex;
    flex-wrap: wrap;
    gap: 25px;
}

.social-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--govco-secondary-color);
    font-weight: 700;
    font-size: 13px;
    text-decoration: none;
}

.social-btn img { 
    width: 20px; 
    height: 20px; 
}

.card-footer-menu {
    margin-top: 25px;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.card-footer-menu a {
    font-size: 11px;
    font-weight: 700;
    color: var(--govco-secondary-color);
    text-decoration: underline;
    text-transform: uppercase;
}

.footer-gov-bar {
    color: var(--govco-white-color);
    background-color: var(--govco-secondary-color);
    padding: 15px 0;
    width: 100%;
    margin-top: auto;
}

.gov-bar-content {
    max-width: 1050px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--govco-white-color) !important;
}

.logos-container { 
    display: flex; 
    align-items: center; 
    gap: 15px; 
}

.logo-white { 
    height: 28px; 
    filter: brightness(0) invert(1); 
}

.divider { 
    width: 1px; 
    height: 25px; 
    background-color: var(--govco-white-color); 
    opacity: 0.6; 
}

.logo-color { 
    height: 28px; 
}

.footer-info-right {
    text-align: right;
    color: var(--govco-white-color) !important;
}

.copyright-text {
    font-size: 12px;
    margin: 2px 0;
    color: var(--govco-white-color) !important;
}

.time-text {
    font-size: 12px;
    margin: 2px 0;
    font-weight: 600;
    color: var(--govco-white-color) !important;
}

@media (max-width: 768px) {
    .footer-split-background {
        background: linear-gradient(180deg, var(--govco-white-color) 60px, var(--govco-primary-color) 60px);
        padding: 0 15px 40px 15px;
    }
    
    .footer-card { 
        padding: 30px 20px; 
        text-align: center; 
    }
    
    .card-header { 
        flex-direction: column-reverse; 
        align-items: center; 
        gap: 20px; 
    }
    
    .card-body { 
        text-align: center; 
    }
    
    .card-socials, .card-footer-menu { 
        justify-content: center; 
    }
    
    .gov-bar-content { 
        flex-direction: column; 
        gap: 15px; 
        text-align: center;
    }

    .footer-info-right {
        text-align: center;
    }
}
</style>