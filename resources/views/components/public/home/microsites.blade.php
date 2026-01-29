@props([
  'items' => [],
  'title' => 'Micro Sitios',
  'intro' => null,
  'variant' => 'grid',
  'columns' => 4,
])

<section class="microsites">
  <div class="ms-container">
    <header class="ms-header">
      <h2 class="ms-title">{{ $title }}</h2>
      @if($intro)<p class="ms-intro">{{ $intro }}</p>@endif
    </header>

    @if($variant === 'grid')
      <ul class="ms-grid ms-cols-{{ $columns }}" role="list">
        @foreach($items as $it)
          <li class="ms-card">
            <a class="ms-card-link" href="{{ $it['url'] ?? '#' }}" target="_blank" rel="noopener">
              <span class="ms-media">
                @if(!empty($it['icon']))
                  <img class="ms-icon" src="{{ $it['icon'] }}" alt="">
                @endif
              </span>
              <span class="ms-label">{{ $it['label'] ?? 'Micrositio' }}</span>
            </a>
          </li>
        @endforeach
      </ul>
    @else
      <div class="ms-carousel" data-ms-carousel>
        <button class="ms-nav ms-prev" type="button" aria-label="Anterior">&#10094;</button>
        <div class="ms-track">
          <ul class="ms-rail" role="list">
            @foreach($items as $it)
              <li class="ms-slide">
                <a class="ms-slide-link" href="{{ $it['url'] ?? '#' }}" target="_blank" rel="noopener">
                  <span class="ms-media">
                    @if(!empty($it['icon']))
                      <img class="ms-icon" src="{{ $it['icon'] }}" alt="">
                    @endif
                  </span>
                  <span class="ms-label">{{ $it['label'] ?? 'Micrositio' }}</span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
        <button class="ms-nav ms-next" type="button" aria-label="Siguiente">&#10095;</button>
      </div>
    @endif
  </div>

  <style>
    .microsites .ms-container { max-width:1200px; margin:auto; padding:1rem 1rem 2rem; }
    .microsites .ms-header { text-align:center; margin-bottom:1.25rem; }
    .microsites .ms-title { font:700 1.75rem/1.2 var(--govco-font-primary); color:var(--govco-secondary-color); }
    .microsites .ms-intro { color:var(--govco-tertiary-color); margin:0 auto 1rem; max-width:60ch; }

    /* GRID */
    .microsites .ms-grid { list-style:none; padding:0; margin:0; display:grid; gap:1rem; }
    .microsites .ms-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}
    .microsites .ms-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}
    .microsites .ms-cols-4{grid-template-columns:repeat(4,minmax(0,1fr))}
    .microsites .ms-cols-5{grid-template-columns:repeat(5,minmax(0,1fr))}
    .microsites .ms-cols-6{grid-template-columns:repeat(6,minmax(0,1fr))}
    @media (max-width:1024px){ .microsites .ms-grid{grid-template-columns:repeat(3,1fr)} }
    @media (max-width:640px){ .microsites .ms-grid{grid-template-columns:repeat(2,1fr)} }

    /* CARD nueva (sin bordes visibles, sombra suave y “surface” sutil) */
    .microsites .ms-card {
      border: none;
      border-radius: 18px;
      background: linear-gradient(180deg, rgba(255,255,255,0.97), rgba(255,255,255,0.92));
      box-shadow: 0 10px 26px rgba(0,0,0,.08), 0 2px 10px rgba(0,0,0,.06);
      transition: transform .22s ease, box-shadow .22s ease;
      padding: 1.5rem 1rem;
      text-align: center;
    }
    .microsites .ms-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 16px 36px rgba(0,0,0,.14), 0 6px 14px rgba(0,0,0,.1);
    }

    .microsites .ms-card-link {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1rem;
      text-decoration: none;
      height: 100%;
    }

    /* fondo del icono: sólido con brillo sutil */
    .microsites .ms-media{
      width: 80px; height: 80px;
      border-radius: 20px;
      display: grid; place-items: center;
      background: var(--govco-secondary-color);
      position: relative;
      box-shadow: 0 6px 18px rgba(0,0,0,.15), inset 0 0 0 2px rgba(255,255,255,.22);
      transition: transform .25s ease, box-shadow .25s ease, filter .25s ease;
    }
    .microsites .ms-media::after{
      content:"";
      position:absolute; inset:0;
      border-radius:20px;
      background: radial-gradient(120% 120% at 30% 20%, rgba(255,255,255,.24) 0%, rgba(255,255,255,0) 55%);
      pointer-events:none;
    }

    .microsites .ms-card:hover .ms-media{
      transform: scale(1.08) rotate(-2deg);
      box-shadow: 0 10px 24px rgba(0,0,0,.18), inset 0 0 0 2px rgba(255,255,255,.28);
    }

    /* icono en blanco (contraste perfecto sobre azul) */
    .microsites .ms-icon{
      width: 60%; height: 60%;
      object-fit: contain;
      filter: brightness(0) invert(1) drop-shadow(0 2px 3px rgba(0,0,0,.15));
    }

    .microsites .ms-label {
      color: var(--govco-secondary-color);
      font-weight: 800;
      font-size: 1rem;
      line-height: 1.3;
      transition: color .2s ease;
    }
    .microsites .ms-card:hover .ms-label {
      color: var(--govco-primary-color);
    }

    /* Accesibilidad: foco visible */
    .microsites .ms-card-link:focus-visible,
    .microsites .ms-slide-link:focus-visible {
      outline: 3px solid color-mix(in srgb, var(--govco-secondary-color) 40%, white);
      outline-offset: 3px;
      border-radius: 14px;
    }

    /* CAROUSEL */
    .microsites .ms-carousel { position:relative; }
    .microsites .ms-track {
      overflow:auto; scroll-snap-type:x mandatory; -webkit-overflow-scrolling:touch; margin:0 2.25rem;
    }
    .microsites .ms-rail { list-style:none; display:flex; gap:1rem; padding:.25rem 0 1.1rem; margin:0; }
    .microsites .ms-slide { min-width:220px; scroll-snap-align:center; }

    /* Slide card (misma estética de card) */
    .microsites .ms-slide-link {
      display:flex; align-items:center; gap:.8rem; text-decoration:none;
      border: none; border-radius:14px;
      padding:1rem 1.05rem;
      background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(255,255,255,0.94));
      box-shadow:
        0 10px 24px rgba(0,0,0,.08),
        0 2px 8px rgba(0,0,0,.05);
      transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
    }
    .microsites .ms-slide-link:hover {
      transform: translateY(-3px);
      box-shadow:
        0 16px 36px rgba(0,0,0,.12),
        0 6px 14px rgba(0,0,0,.08);
      filter: saturate(1.02);
    }

    /* Botones nav del carrusel (sin borde, con sombra y fondo translúcido) */
    .microsites .ms-nav {
      position:absolute; top:50%; transform:translateY(-50%);
      width:44px; height:44px; border-radius:50%;
      border:none; cursor:pointer; display:grid; place-items:center;
      color: var(--govco-white-color);
      background: color-mix(in srgb, var(--govco-secondary-color) 80%, transparent);
      box-shadow: 0 8px 18px rgba(0,0,0,.18);
      transition: transform .15s ease, filter .15s ease, background .15s ease;
    }
    .microsites .ms-nav:hover { transform:translateY(-50%) scale(1.05); filter:brightness(1.05); }
    .microsites .ms-prev { left:0; }
    .microsites .ms-next { right:0; }
  </style>

  <script>
    (() => {
      const root = document.currentScript.closest('.microsites');
      const car = root.querySelector('[data-ms-carousel]');
      if(!car) return;
      const track = car.querySelector('.ms-track');
      const prev = car.querySelector('.ms-prev');
      const next = car.querySelector('.ms-next');

      function slide(dir=1){
        const card = track.querySelector('.ms-slide');
        if(!card) return;
        const delta = card.getBoundingClientRect().width + 12;
        track.scrollBy({left: dir*delta, behavior:'smooth'});
      }
      prev.addEventListener('click',()=>slide(-1));
      next.addEventListener('click',()=>slide(1));
    })();
  </script>
</section>