<section class="govco-container-social" aria-label="Redes sociales">
    <h2 class="section-title-social">Nuestras Redes Sociales</h2>

    <div class="social-grid">
        <div class="social-col">
            <div class="embed-wrapper embed-wrapper--youtube">
                <iframe
                    id="yt-latest"
                    src="https://www.youtube.com/embed/videoseries?list=UULF1z-DVKIR_TJk1fMUDZx7Vw"
                    title="YouTube Gobernación"
                    frameborder="0"
                    allowfullscreen>
                </iframe>
            </div>
        </div>

        <div class="social-col">
            <div class="embed-wrapper embed-wrapper--facebook">
                <iframe
                    src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fgobernaciondelputumayo%2F&tabs=timeline&width=340&small_header=true&adapt_container_width=true&hide_cover=false&show_facepile=true"
                    title="Facebook Gobernación"
                    scrolling="no"
                    frameborder="0"
                    allowfullscreen="true">
                </iframe>
            </div>
        </div>
    </div>
</section>

<style>
.govco-container-social {
    max-width: 1200px;
    margin: 3rem auto;
    padding: 0 1rem;
}

.section-title-social {
    text-align: center;
    font-weight: 800;
    font-size: 2.2rem;
    margin-bottom: 2rem;
    color: var(--govco-secondary-color) !important;
    font-family: var(--govco-font-primary);
}

.social-grid {
    display: flex;
    gap: 2rem;
    align-items: stretch;
}

.social-col {
    display: flex;
    flex-direction: column;
}

.social-col:first-child {
    flex: 2.5;
}

.social-col:last-child {
    flex: 1;
}

.embed-wrapper {
    border-radius: 8px;
    overflow: hidden;
    background-color: var(--govco-white-color);
    border: 1px solid var(--govco-border-color);
    box-shadow: var(--govco-box-shadow);
    height: 100%;
    width: 100%;
}

.embed-wrapper--youtube {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
}

.embed-wrapper--youtube iframe {
    position: absolute;
    top: 0; 
    left: 0; 
    width: 100%; 
    height: 100%;
}

.embed-wrapper--facebook {
    display: flex;
}

.embed-wrapper--facebook iframe {
    width: 100% !important;
    height: 100% !important;
}

body.high-contrast .section-title-social {
    color: #FFF !important;
}

body.high-contrast .embed-wrapper {
    border: 2px solid #FFF !important;
    background: #000 !important;
}

@media (max-width: 992px) {
    .social-grid {
        flex-direction: column;
    }
    
    .embed-wrapper--youtube {
        padding-bottom: 56.25%;
        height: 0;
    }

    .embed-wrapper--facebook {
        height: 450px;
    }
}
</style>