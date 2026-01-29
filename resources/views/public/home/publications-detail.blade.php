@extends('layouts.app')

@section('title', 'Noticias')

@section('content')
<section class="news-section">
  <div class="container">
    <h1 class="section-title">Noticias</h1>

    <div class="news-grid">
      @foreach ($allNews as $news)
        @php
          $embed = \App\Support\VideoEmbed::parse($news->link ?? null, $news->title ?? 'Video');

          $path = $news->image;
          $imgSrc = $path
              ? asset('storage/' . $path)
              : asset('images/placeholder-news.jpg');
        @endphp

        <div class="news-card">
          <div class="news-media">
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
              <img src="{{ $imgSrc }}" alt="{{ $news->title }}">
            @endif
          </div>

          <div class="news-content">
            <h2 class="news-title">{{ $news->title }}</h2>
            <p class="news-date">{{ \Carbon\Carbon::parse($news->date)->format('d M Y') }}</p>
            <p class="news-summary">{{ Str::limit(strip_tags($news->description), 120) }}</p>
            <a href="{{ route('publications.show', ['id' => $news->id, 'type' => $news->type_id]) }}" class="news-btn">Ver más</a>
          </div>
        </div>
      @endforeach
    </div>

    <div class="pagination-wrapper">
      {{ $allNews->appends(request()->query())->links() }}
    </div>
  </div>
</section>
@endsection

<style>
.news-section{padding:2rem 1rem;background-color:#f5f7fa}
.section-title{font-size:2rem;margin-bottom:2rem;text-align:center;color:#164194}
.news-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:2rem}
.news-card{background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 0 10px rgba(0,0,0,0.05);display:flex;flex-direction:column;transition:transform .2s}
.news-card:hover{transform:translateY(-5px)}
.news-media{width:100%;position:relative;background:#000}
.news-media img{width:100%;height:180px;object-fit:cover;display:block}
.video-embed{position:relative;width:100%;padding-top:56.25%;overflow:hidden}
.video-embed iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
.news-content{padding:1rem;display:flex;flex-direction:column;flex-grow:1}
.news-title{font-size:1.2rem;color:#0c3c84;margin-bottom:.5rem}
.news-date{font-size:.9rem;color:#777;margin-bottom:.5rem}
.news-summary{flex-grow:1;font-size:1rem;margin-bottom:1rem}
.news-btn{align-self:flex-start;background-color:#0c3c84;color:#fff;padding:.5rem 1rem;border-radius:5px;text-decoration:none;transition:background-color .3s}
.news-btn:hover{background-color:#0a2d63}
.pagination-wrapper{margin-top:2rem;text-align:center}
</style>