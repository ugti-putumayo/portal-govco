<div class="events-section govco-container">
    <h2 class="events-title">Noticias Anticontrabando</h2>

    @php
        use Illuminate\Support\Str;
        $events = $publications->where('type_id', 7);
    @endphp

    @if($events->isNotEmpty())
        <div class="events-container">
            <div class="featured-event">
                <img src="{{ asset('storage/' . $events->first()->image) }}" alt="{{ $events->first()->title }}" class="featured-image">
                <div class="featured-content">
                    <h3 class="featured-title">{{ $events->first()->title }}</h3>
                    <p class="featured-date">
                        <strong>Publicación:</strong> {{ \Carbon\Carbon::parse($events->first()->date)->format('d/m/Y') }}
                    </p>
                    <p class="featured-description">{{ Str::limit(strip_tags($events->first()->description), 200) }}</p>
                    <a href="{{ route('publications.show', ['id' => $events->first()->id, 'type' => 7]) }}" class="event-btn">Leer más</a>
                </div>
            </div>

            <div class="events-list">
                @foreach($events->slice(1, 3) as $event)
                    <div class="event-item">
                        <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="event-thumbnail">
                        <div class="event-item-content">
                            <a href="{{ route('publications.show', ['id' => $event->id, 'type' => 7]) }}" class="event-item-title">
                                {{ $event->title }}
                            </a>
                            <p class="event-item-date">
                                {{ \Carbon\Carbon::parse($event->date)->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="no-events">No hay noticias anticontrabando disponibles en este momento.</p>
    @endif

    <div class="events-more">
        <a href="{{ route('publicationsAll') }}?type=7" class="events-more-btn">Ver más noticias anticontrabando</a>
    </div>
</div>

<style>
.events-section {
    padding: 2rem 1rem;
    margin: auto;
}

.events-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--govco-secondary-color);
    text-align: center;
    margin-bottom: 2rem;
}

.events-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    align-items: flex-start;
}

.featured-event {
    background-color: var(--govco-white-color);
    border-radius: var(--govco-border-radius);
    box-shadow: var(--govco-box-shadow);
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
    color: var(--govco-secondary-color);
    margin-bottom: 0.5rem;
}

.featured-date {
    font-size: 0.9rem;
    color: var(--govco-tertiary-color);
    margin-bottom: 0.8rem;
}

.featured-description {
    font-size: 0.95rem;
    color: var(--govco-black-color);
    flex-grow: 1;
}

.event-btn {
    margin-top: 1rem;
    align-self: flex-start;
    background-color: var(--govco-secondary-color);
    color: var(--govco-white-color);
    font-weight: 600;
    padding: 0.5rem 1.2rem;
    border-radius: var(--govco-border-radius);
    text-decoration: none;
    transition: background 0.3s ease;
}

.event-btn:hover {
    background-color: var(--govco-primary-color);
}

.events-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.event-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    background: var(--govco-white-color);
    padding: 0.8rem;
    border-radius: var(--govco-border-radius);
    box-shadow: var(--govco-box-shadow);
}

.event-thumbnail {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: var(--govco-border-radius);
}

.event-item-content {
    flex: 1;
}

.event-item-title {
    font-size: 1rem;
    font-weight: bold;
    color: var(--govco-secondary-color);
    text-decoration: none;
}

.event-item-title:hover {
    color: var(--govco-primary-color);
}

.event-item-date {
    font-size: 0.85rem;
    color: var(--govco-tertiary-color);
    margin-top: 0.25rem;
}

.events-more {
    text-align: center;
    margin-top: 2rem;
}

.events-more-btn {
    background-color: var(--govco-secondary-color);
    color: var(--govco-white-color);
    padding: 0.7rem 1.5rem;
    font-weight: bold;
    border-radius: var(--govco-border-radius);
    text-decoration: none;
    transition: background 0.3s;
}

.events-more-btn:hover {
    background-color: var(--govco-primary-color);
}
</style>