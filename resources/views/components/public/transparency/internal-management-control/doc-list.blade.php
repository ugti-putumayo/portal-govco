<section class="doclist">
  <div class="govco-container">
    <header class="doclist-head">
      <h2 class="doclist-title">{{ $title ?? 'Documentos' }}</h2>
    </header>

    @php
      use Illuminate\Support\Str;
      use Illuminate\Support\Carbon;

      /** @var \Illuminate\Support\Collection $records */
      $list = ($records ?? collect())->values();

      // Resolver año (prioridad: date -> date_start -> created_at)
      $yearOf = function ($r) {
          $d = $r->date ?? $r->date_start ?? $r->created_at ?? null;
          return $d ? Carbon::parse($d)->year : 'Sin fecha';
      };

      // Agrupar por año y ordenar años DESC
      $grouped = $list->groupBy($yearOf)->sortKeysDesc();
      $openCount = 0; // para abrir los primeros grupos por defecto (opcional)
    @endphp

    @if($grouped->isEmpty())
      <div class="doclist-empty">No hay documentos publicados.</div>
    @else
      @foreach($grouped as $year => $items)
        <details class="docyear" {{ $openCount++ < 1 ? 'open' : '' }}>
          <summary class="docyear-head">
            <span class="docyear-title">{{ $year }}</span>
            <span class="docyear-count">{{ $items->count() }} {{ Str::plural('documento', $items->count()) }}</span>
          </summary>

          @foreach($items as $r)
            @php
              // Construir href: si viene url directa úsala, si viene path relativo lo pasamos por asset()
              $href = null;
              if (!empty($r->url)) {
                  $href = $r->url;
              } elseif (!empty($r->document)) {
                  $href = Str::startsWith($r->document, ['http://','https://','/'])
                          ? $r->document
                          : asset($r->document); // ej: 'app/public/...'
              }
            @endphp

            <article class="docrow">
              <div class="docrow-left">
                <span class="pdf-chip" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18" class="pdf-ico" focusable="false">
                    <path d="M6 2h8l4 4v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" fill="currentColor" opacity=".25"/>
                    <path d="M14 2v4h4" fill="none" stroke="currentColor" stroke-width="1.6"/>
                    <path d="M7.6 16h1.6c1 0 1.8-.8 1.8-1.8S10.2 12.5 9.2 12.5H7.6V16zm0-2.9h1.4c.6 0 1 .4 1 1s-.4 1-1 1H7.6v-2zM12.4 16h2.1c.9 0 1.6-.7 1.6-1.6s-.7-1.6-1.6-1.6h-1.3v-1.3h-0.8V16zm0.8-2.4h1.3c.5 0 .9.4.9.9s-.4.9-.9.9h-1.3v-1.8zM18.1 16h.8v-3.5h1.3v-.7h-2.1V16z" fill="currentColor"/>
                  </svg>
                  PDF
                </span>

                <div class="docrow-meta">
                  <h3 class="docrow-title">{{ $r->title }}</h3>
                  @if(!empty($r->description))
                    <p class="docrow-desc">{{ \Illuminate\Support\Str::limit(strip_tags($r->description), 180) }}</p>
                  @endif
                </div>
              </div>

              <div class="docrow-actions">
                @if($href)
                  <a href="{{ $href }}" target="_blank" rel="noopener" class="docrow-btn">Descargar</a>
                @endif
              </div>
            </article>
          @endforeach
        </details>
      @endforeach
    @endif
  </div>

  <style>
    .doclist { padding: 1rem 0 1.25rem; }
    .doclist-head { margin-bottom: .75rem; }
    .doclist-title {
      font: 800 1.6rem/1.2 var(--govco-font-primary);
      color: var(--govco-secondary-color);
      text-align: left;
      margin: 0 0 .25rem;
    }

    /* Grupo por año */
    .docyear { margin: .8rem 0; border-radius: 12px; background: #fff; border: 1px solid rgba(0,0,0,.06); overflow: hidden; }
    .docyear[open] { border-color: color-mix(in srgb, var(--govco-secondary-color) 14%, #ffffff); box-shadow: 0 6px 18px rgba(0,0,0,.06); }

    .docyear-head {
      list-style: none;
      cursor: pointer;
      display: flex; align-items: center; justify-content: space-between;
      gap: .8rem; padding: .9rem 1rem;
      font: 800 1rem/1 var(--govco-font-primary);
      color: var(--govco-secondary-color);
      background: color-mix(in srgb, var(--govco-secondary-color) 7%, #ffffff);
      user-select: none;
    }
    .docyear-head::-webkit-details-marker { display: none; }
    .docyear-title { font-size: 1.05rem; }
    .docyear-count {
      font-weight: 700; font-size: .85rem; color: color-mix(in srgb, var(--govco-tertiary-color) 75%, #000);
      background: color-mix(in srgb, var(--govco-secondary-color) 6%, #fff);
      padding: .25rem .55rem; border-radius: 999px; border: 1px solid rgba(0,0,0,.05);
    }

    .docrow{
      display:flex; align-items:center; justify-content:space-between; gap:1rem;
      background:#fff; border-top:1px solid rgba(0,0,0,.06);
      padding:.9rem 1rem;
      transition:background .12s ease, transform .12s ease;
    }
    .docyear:not([open]) .docrow { display:none; }
    .docrow:hover{ background: color-mix(in srgb, var(--govco-gray-color) 35%, #ffffff); transform: translateY(-1px); }

    .docrow-left{ display:flex; align-items:center; gap:.9rem; min-width:0; }
    .pdf-chip{
      display:inline-flex; align-items:center; gap:.4rem;
      font-weight:800; font-size:.85rem;
      color:var(--govco-secondary-color);
      background:color-mix(in srgb, var(--govco-secondary-color) 8%, #ffffff);
      padding:.45rem .65rem; border-radius:999px;
      border:1px solid color-mix(in srgb, var(--govco-secondary-color) 18%, transparent);
      white-space: nowrap;
    }
    .pdf-ico{ color:var(--govco-secondary-color); }

    .docrow-meta { min-width:0; }
    .docrow-title{
      margin:0; color:var(--govco-secondary-color);
      font:800 1rem/1.25 var(--govco-font-primary);
      overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
      max-width:clamp(240px, 60vw, 820px);
    }
    .docrow-desc{
      margin:.25rem 0 0;
      color:color-mix(in srgb, var(--govco-tertiary-color) 85%, #000);
      font:500 .93rem/1.35 var(--govco-font-secondary);
    }

    .docrow-actions{ flex-shrink:0; display:flex; align-items:center; gap:.5rem; }
    .docrow-btn{
      text-decoration:none; background:var(--govco-secondary-color); color:var(--govco-white-color);
      font-weight:800; font-size:.9rem; padding:.55rem 1rem; border-radius:999px;
      box-shadow:0 6px 16px rgba(0,0,0,.12);
      transition:transform .12s ease, filter .12s ease, box-shadow .12s ease;
      white-space: nowrap;
    }
    .docrow-btn:hover{ transform:translateY(-1px); filter:brightness(1.05); box-shadow:0 10px 22px rgba(0,0,0,.16); }

    .doclist-empty{
      padding:.9rem 1rem; border-radius:10px; background:var(--govco-gray-color);
      color:var(--govco-tertiary-color); font-weight:600; text-align:center; margin-top:.5rem;
    }

    @media (max-width:700px){
      .docrow{ align-items:flex-start; gap:.8rem; }
      .docrow-title{ white-space:normal; }
      .docrow-actions{ margin-left:auto; }
    }
  </style>
</section>