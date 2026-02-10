<section class="apps-section">
    <div class="apps-container">
        <h2 class="section-title-apps">Aplicativos Institucionales</h2>
        
        <div class="apps-grid">
            <a href="https://gesdoc.putumayo.gov.co/#!/page/login" class="app-card" target="_blank" rel="noopener noreferrer">
                <div class="app-icon-container">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <span class="app-name">Gestión Documental</span>
            </a>

            <a href="https://soporte.putumayo.gov.co/" class="app-card" target="_blank" rel="noopener noreferrer">
                <div class="app-icon-container">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                </div>
                <span class="app-name">Soporte Técnico</span>
            </a>

            <a href="https://pasaporte.putumayo.gov.co/" class="app-card" target="_blank" rel="noopener noreferrer">
                <div class="app-icon-container">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                </div>
                <span class="app-name">Pasaportes</span>
            </a>

            <a href="https://pqrds.putumayo.gov.co/" class="app-card" target="_blank" rel="noopener noreferrer">
                <div class="app-icon-container">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <span class="app-name">Radicar PQRDS</span>
            </a>

            <a href="https://www.gacetaputumayo.gov.co/" class="app-card" target="_blank" rel="noopener noreferrer">
                <div class="app-icon-container">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                </div>
                <span class="app-name">Gaceta</span>
            </a>
        </div>
    </div>
</section>

<style>
.apps-section {
    width: 100%;
    background-color: var(--govco-white-color);
    padding: 4rem 0;
}

.apps-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.section-title-apps {
    text-align: center;
    color: var(--govco-secondary-color);
    font-family: var(--govco-font-primary);
    font-weight: 800;
    font-size: 2.2rem;
    margin-bottom: 3.5rem;
}

.apps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
}

.app-card {
    background-color: var(--govco-white-color);
    border-radius: 16px;
    padding: 2.5rem 1.5rem;
    text-align: center;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1.2rem;
    /* Sombreado mejorado similar a microsites */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.03);
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.app-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 72, 132, 0.1);
    border-color: var(--govco-primary-color);
}

.app-icon-container {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--govco-primary-color);
    transition: transform 0.3s ease;
}

.app-icon-container svg {
    width: 40px;
    height: 40px;
}

.app-name {
    color: var(--govco-secondary-color);
    font-family: var(--govco-font-primary);
    font-weight: 700;
    font-size: 1rem;
    line-height: 1.2;
}

.app-card:hover .app-icon-container {
    transform: scale(1.1);
}

/* --- ALTO CONTRASTE --- */
body.high-contrast .apps-section {
    background-color: #000 !important;
}

body.high-contrast .section-title-apps {
    color: #fff !important;
}

body.high-contrast .app-card {
    background-color: #000 !important;
    border: 2px solid #fff !important;
    box-shadow: none !important;
}

body.high-contrast .app-name,
body.high-contrast .app-icon-container {
    color: #fff !important;
}

body.high-contrast .app-card:hover {
    background-color: #fff !important;
}

body.high-contrast .app-card:hover .app-name,
body.high-contrast .app-card:hover .app-icon-container {
    color: #000 !important;
}

@media (max-width: 992px) {
    .apps-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .apps-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .apps-grid {
        grid-template-columns: 1fr;
    }
}
</style>