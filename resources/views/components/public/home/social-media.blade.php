<section class="govco-container-social" aria-label="Redes sociales">
    <h2 class="section-title">Nuestras Redes Sociales</h2>

    <div class="social-grid">
        <div class="social-col social-col--main">
            <div class="embed-wrapper embed-wrapper--youtube">
                <iframe
                    id="yt-latest"
                    src="https://www.youtube.com/embed/videoseries?list=UULF1z-DVKIR_TJk1fMUDZx7Vw"
                    title="Últimos videos Gobernación del Putumayo"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
                </iframe>
            </div>
            <div class="social-footer">
                <p class="social-text">No te pierdas nuestros últimos informes y noticias en video.</p>
                <a class="social-btn social-btn--yt" href="https://www.youtube.com/@gobernacionputumayo" target="_blank" rel="noopener">
                    Suscribirse al Canal
                </a>
            </div>
        </div>

        <div class="social-col social-col--sidebar">
            <div class="embed-wrapper embed-wrapper--facebook">
                <iframe
                    src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fgobernaciondelputumayo%2F%3Flocale%3Des_LA&tabs=timeline&width=340&small_header=true&adapt_container_width=true&hide_cover=false&show_facepile=true&appId"
                    title="Facebook Gobernación"
                    width="500"
                    scrolling="no"
                    frameborder="0"
                    allowfullscreen="true"
                    allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share">
                </iframe>
            </div>
            <div class="social-footer">
                <a class="social-btn social-btn--fb" href="https://www.facebook.com/gobernaciondelputumayo/" target="_blank" rel="noopener">
                    Seguir en Facebook
                </a>
            </div>
        </div>
    </div>
</section>

@once
<script>
(async function () {
    const iframe = document.getElementById('yt-latest');
    if (!iframe) return;
    try {
        const res = await fetch('/api/youtube/latest-embeddable');
        if (!res.ok) return;
        const embedUrl = await res.text();
        if (embedUrl && embedUrl.length > 5) {
            iframe.src = embedUrl;
        }
    } catch (e) {
        console.warn('YouTube fallback activo (Lista de reproducción)');
    }
})();
</script>

<style>
.govco-container-social {
    width: 100%;
    max-width: 1200px;
    margin: 3rem auto;
    padding: 0 1rem;
    box-sizing: border-box;
}

.section-title {
    text-align: center;
    font-weight: 800;
    font-size: 2.2rem;
    margin-bottom: 2rem;
    color: var(--govco-secondary-color, #004884);
}

.social-grid {
    display: grid;
    grid-template-columns: 2.5fr 1fr;
    gap: 2rem;
    align-items: stretch;
}

.social-col {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    height: 100%;
}

.embed-wrapper {
    border-radius: 8px;
    overflow: hidden;
    width: 100%;
    flex: 1; 
    display: flex;
}

.embed-wrapper--youtube {
    background: #000;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    position: relative;
    height: 100%;
    min-height: 400px;
}

.embed-wrapper--youtube iframe {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: 0;
}

.embed-wrapper--facebook {
    background: #fff;
    border: 1px solid #e1e1e1;
    height: 100%;
    justify-content: center;
    align-items: flex-start;
}

.embed-wrapper--facebook iframe {
    width: 100%;
    height: 100% !important;
}

.social-footer {
    text-align: center;
    padding: 0.5rem 0;
    flex-shrink: 0;
}

.social-text {
    color: #666;
    font-size: 0.95rem;
    margin-bottom: 0.8rem;
}

.social-btn {
    display: inline-block;
    padding: 0.7rem 1.5rem;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 700;
    transition: transform 0.2s, box-shadow 0.2s;
    font-size: 0.9rem;
}

.social-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.social-btn--yt { background-color: #FF0000; color: #fff; }
.social-btn--yt:hover { background-color: #cc0000; color: #fff; }
.social-btn--fb { background-color: #1877F2; color: #fff; }
.social-btn--fb:hover { background-color: #166fe5; color: #fff; }

@media (max-width: 992px) {
    .social-grid {
        grid-template-columns: 1fr;
        gap: 3rem;
        align-items: start;
    }

    .social-col {
        height: auto;
    }

    .embed-wrapper--youtube {
        aspect-ratio: 16 / 9;
        height: auto;
        min-height: auto;
        flex: none;
    }
    
    .embed-wrapper--facebook {
        max-width: 500px;
        margin: 0 auto;
        height: 500px;
        flex: none;
    }
}
</style>
@endonce