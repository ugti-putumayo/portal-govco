<div class="news-section govco-container">
    <h2 class="news-title">Noticias</h2>

    @if($publications->isNotEmpty())
        @php
            $first = $publications->first();
            // Solo será distinto de null si es YouTube o Facebook:
            $embed = \App\Support\VideoEmbed::parse($first->link ?? null, $first->title ?? 'Video');
        @endphp

        <div class="news-container">
            <div class="featured-news">
                @if($embed)
                    <div class="video-embed">
                        <iframe
                            src="{{ $embed['src'] }}"
                            title="{{ e($embed['title']) }}"
                            loading="lazy"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen
                        ></iframe>
                    </div>
                @else
                    <img src="{{ asset('storage/' . $first->image) }}" alt="{{ $first->title }}" class="featured-image">
                @endif

                <div class="featured-content">
                    <h3 class="featured-title">{{ $first->title }}</h3>
                    <p class="featured-date">
                        <strong>Publicación:</strong> {{ \Carbon\Carbon::parse($first->date)->format('d/m/Y') }}
                    </p>
                    <p class="featured-description">{{ Str::limit(strip_tags($first->description), 200) }}</p>

                    <div>
                        <a href="{{ route('publications.show', ['id' => $first->id, 'type' => 2]) }}" class="news-btn">Leer más</a>

                        @if(!empty($first->link))
                            <a href="{{ $first->link }}" class="news-btn" target="_blank" rel="noopener">Más información…</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="news-list">
                @foreach($publications->slice(1, 3) as $publication)
                    <div class="news-item">
                        <img src="{{ asset('storage/' . $publication->image) }}" alt="{{ $publication->title }}" class="news-thumbnail">
                        <div class="news-item-content">
                            <a href="{{ route('publications.show', $publication->id) }}" class="news-item-title">{{ $publication->title }}</a>
                            <p class="news-item-date">{{ \Carbon\Carbon::parse($publication->date)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="no-news">No hay noticias disponibles en este momento.</p>
    @endif

    <div class="news-more">
        <a href="{{ route('publicationsAll') }}?type=2" class="news-more-btn">Ver más noticias</a>
    </div>
</div>

<style>
.govco-container {
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
    padding-left: 1rem;
    padding-right: 1rem;
}

.news-section {
    padding: 2rem 1rem;
    margin: auto;
    font-family: var(--govco-font-primary, sans-serif);
}

.news-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--govco-secondary-color, #004884);
    text-align: center;
    margin-bottom: 2rem;
}

.news-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    align-items: flex-start;
}

.featured-news {
    background-color: var(--govco-white-color, #fff);
    border-radius: var(--govco-border-radius, 8px);
    box-shadow: var(--govco-box-shadow, 0 2px 6px rgba(0,0,0,0.05));
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.featured-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.featured-content {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}

.featured-title {
    font-size: 1.4rem;
    font-weight: bold;
    color: var(--govco-secondary-color, #004884);
    margin-bottom: 0.5rem;
}

.featured-date {
    font-size: 0.9rem;
    color: var(--govco-tertiary-color, #666);
    margin-bottom: 0.8rem;
}

.featured-description {
    font-size: 0.95rem;
    color: var(--govco-tertiary-color, #333);
    flex-grow: 1;
}

.news-btn {
    margin-top: 1rem;
    align-self: flex-start;
    background-color: var(--govco-secondary-color, #004884);
    color: var(--govco-white-color, #fff);
    font-weight: 600;
    padding: 0.5rem 1.2rem;
    border-radius: var(--govco-border-radius, 4px);
    text-decoration: none;
    transition: background 0.3s ease;
}

.news-btn:hover {
    background-color: var(--govco-primary-color, #007bff);
}

.news-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.news-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    background: var(--govco-white-color, #fff);
    padding: 0.8rem;
    border-radius: var(--govco-border-radius, 6px);
    box-shadow: var(--govco-box-shadow, 0 1px 4px rgba(0,0,0,0.05));
}

.news-thumbnail {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: var(--govco-border-radius, 6px);
}

.news-item-content {
    flex: 1;
}

.news-item-title {
    font-size: 1rem;
    font-weight: bold;
    color: var(--govco-secondary-color, #004884);
    text-decoration: none;
}

.news-item-title:hover {
    color: var(--govco-primary-color, #007bff);
}

.news-item-date {
    font-size: 0.85rem;
    color: #888;
    margin-top: 0.25rem;
}

.news-more {
    text-align: center;
    margin-top: 2rem;
}

.news-more-btn {
    background-color: var(--govco-secondary-color, #004884);
    color: var(--govco-white-color, white);
    padding: 0.7rem 1.5rem;
    font-weight: bold;
    border-radius: var(--govco-border-radius, 5px);
    text-decoration: none;
    transition: background 0.3s;
}

.news-more-btn:hover {
    background-color: var(--govco-primary-color, #007bff);
}


@media (max-width: 992px) {
    .news-container {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .news-section {
        padding: 1.5rem 1rem;
    }
    
    .news-title {
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
    }

    .featured-image {
        height: 200px;
    }

    .featured-title {
        font-size: 1.2rem;
    }

    .featured-description {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .govco-container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    .news-section {
        padding: 1rem 0;
    }

    .featured-image {
        height: 180px;
    }
}
</style>
