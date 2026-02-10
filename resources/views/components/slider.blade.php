@php
    $sortedImages = $images->sortBy('order');
@endphp
<div id="slider-container">
  <div class="slider">
    <div class="slides">
      @foreach ($sortedImages as $image)
        @php $hasLink = !empty($image['link'] ?? null); @endphp
        <div class="slide" style="--bg: url('{{ $image['route'] }}')">
          @if($hasLink)
            <a class="slide-link" href="{{ $image['link'] }}" target="_blank" rel="noopener">
              <img
                src="{{ $image['route'] }}"
                alt="{{ $image['title'] ?? 'Slider Image' }}"
                loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                decoding="async"
                fetchpriority="{{ $loop->first ? 'high' : 'low' }}"
              />
            </a>
          @else
            <img
              src="{{ $image['route'] }}"
              alt="{{ $image['title'] ?? 'Slider Image' }}"
              loading="{{ $loop->first ? 'eager' : 'lazy' }}"
              decoding="async"
              fetchpriority="{{ $loop->first ? 'high' : 'low' }}"
            />
          @endif
          <div class="slide-info">{{ $image['title'] }}</div>
        </div>
      @endforeach
    </div>

    <button class="prev" onclick="prevSlide()">&#10094;</button>
    <button class="next" onclick="nextSlide()">&#10095;</button>
    <button class="play-pause selected" onclick="toggleAutoSlide()">
      <img id="playPauseIcon" src="/icons/pause.svg" alt="Pause">
      <span class='btn__PlayPause' id="playPauseText">Pausar</span>
    </button>
  </div>

  <div class="indicator-container">
    @foreach ($sortedImages as $index => $image)
      <span class="indicator" onclick="selectSlide({{ $index }})"></span>
    @endforeach
  </div>
</div>

<script>
let currentSlide = 0
let autoSlideInterval
let isPlaying = true

function showSlide(index) {
  const slides = document.querySelector('.slides')
  const indicators = document.querySelectorAll('.indicator')
  const totalSlides = document.querySelectorAll('.slide').length

  if (index >= totalSlides) {
    currentSlide = 0
  } else if (index < 0) {
    currentSlide = totalSlides - 1
  } else {
    currentSlide = index
  }

  slides.style.transform = `translateX(-${currentSlide * 100}%)`
  indicators.forEach((indicator, idx) => {
    indicator.classList.toggle('active', idx === currentSlide)
  })
}

function nextSlide() {
  showSlide(currentSlide + 1)
}

function prevSlide() {
  showSlide(currentSlide - 1)
}

function selectSlide(index) {
  showSlide(index)
  resetAutoSlide()
}

function startAutoSlide() {
  autoSlideInterval = setInterval(() => {
    nextSlide()
  }, 5000)
}

function stopAutoSlide() {
  clearInterval(autoSlideInterval)
}

function toggleAutoSlide() {
  const playPauseIcon = document.getElementById('playPauseIcon')
  const playPauseText = document.getElementById('playPauseText')
  const playPauseButton = document.querySelector('.play-pause')

  if (isPlaying) {
    stopAutoSlide()
    playPauseIcon.src = '/icons/play.svg'
    playPauseIcon.alt = 'Play'
    playPauseText.textContent = 'Reproducir'
    playPauseButton.classList.add('selected')
  } else {
    startAutoSlide()
    playPauseIcon.src = '/icons/pause.svg'
    playPauseIcon.alt = 'Pause'
    playPauseText.textContent = 'Pausar'
    playPauseButton.classList.remove('selected')
  }

  isPlaying = !isPlaying
}

function resetAutoSlide() {
  stopAutoSlide()
  startAutoSlide()
}

window.addEventListener('load', () => {
  startAutoSlide()
  showSlide(currentSlide)
})
</script>

<style>
#slider-container {
  --slider-min-h: 620px;
  --slider-max-h: 650px;
  width: 100%;
  margin-left: 0;
  margin-right: 0;
  height: clamp(var(--slider-min-h), 55vh, var(--slider-max-h));
  min-height: var(--slider-min-h);
  position: relative;
  overflow: hidden;
  background: transparent;
}

.slider {
  display: flex;
  width: 100%;
  height: 100%;
}

body.high-contrast .slide img {
    filter: grayscale(1) contrast(1.2) !important;
    opacity: 0.8;
}

.slides {
  display: flex;
  width: 100%;
  height: 100%;
  transition: transform 0.5s ease;
}
.slide {
  min-width: 100%;
  height: 100%;
  box-sizing: border-box;
  position: relative;
  display: grid;
  place-items: center;
  overflow: hidden;
}
.slide::before {
  content: "";
  position: absolute;
  inset: -40px;
  background-image: var(--bg);
  background-size: cover;
  background-position: center;
  filter: blur(24px);
  opacity: 0.6;
  z-index: 0;
}
.slide-link, .slide img {
  position: relative;
  z-index: 1;
  display: block;
}
.slide img {
  max-width: 100%;
  max-height: 100%;
  width: auto;
  height: 100%;
  min-height: var(--slider-min-h);/
  object-fit: contain;
}
.slide-info {
  position: absolute;
  top: 0;
  right: 0;
  background: rgba(255,255,255,0.7);
  backdrop-filter: blur(10px);
  padding: 10px 20px;
  color: var(--govco-black-color, #000);
  font-family: var(--govco-font-secondary, sans-serif);
  font-size: 16px;
  font-weight: bold;
  max-width: min(60%, 520px);
  clip-path: polygon(0 0, 100% 0, 100% 100%, 20px 100%, 0 0);
  z-index: 2;
  pointer-events: none;
}
.prev, .next {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background-color: rgba(0, 0, 0, 0.5);
  color: var(--govco-white-color, white);
  border: none;
  font-size: 24px;
  padding: 10px;
  cursor: pointer;
  z-index: 10;
  border-radius: var(--govco-border-radius, 5px);
}
.prev { left: 10px; }
.next { right: 10px; }

.play-pause {
  position: absolute;
  bottom: 20px;
  left: 20px;
  top: auto;

  background: var(--govco-primary-color);
  border: 2px solid transparent;
  color: var(--govco-white-color);
  padding: 10px 15px;
  border-radius: var(--govco-border-radius, 8px);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: background-color 0.3s ease, border-color 0.3s ease;
  z-index: 11;
}
.play-pause img {
  width: 16px;
  height: 16px;
  filter: brightness(0) invert(1);
}
.play-pause:hover {
  background-color: var(--govco-secondary-color);
}
.play-pause.selected {
  border-color: var(--govco-success-color);
}
.btn__PlayPause {
  color: var(--govco-white-color);
  font-weight: bold;
  font-family: var(--govco-font-primary, sans-serif);
  font-size: 14px;
}
.indicator-container {
  text-align: center;
  position: absolute;
  bottom: 20px;
  width: 100%;
  z-index: 10;
}
.indicator {
  display: inline-block;
  width: 12px;
  height: 12px;
  margin: 0 5px;
  background-color: rgba(255, 255, 255, 0.5);
  border-radius: 50%;
  cursor: pointer;
  transition: background-color 0.3s ease;
  border: 2px solid transparent;
}
.indicator.active {
  background-color: var(--govco-white-color, rgba(255, 255, 255, 1));
  border-color: var(--govco-success-color);
}

@media (max-width: 992px) {
  #slider-container {
    --slider-min-h: auto;
    min-height: auto;
    height: auto;
    aspect-ratio: 16 / 9;
  }
  
  .slide img {
    min-height: auto;
    width: 100%;
    height: 100%;
  }
}

@media (max-width: 768px) {
  .slide-info {
    font-size: 14px;
    padding: 8px 12px;
    max-width: 80%;
    clip-path: polygon(0 0, 100% 0, 100% 100%, 10px 100%, 0 0);
  }
  .prev, .next {
    font-size: 20px;
    padding: 8px;
  }

  .play-pause {
    top: 10px;
    left: 10px;
    bottom: auto;
    padding: 8px 12px;
  }
  
  .indicator-container {
    bottom: 10px;
  }
}

@media (max-width: 576px) {
  #slider-container {
    aspect-ratio: 4 / 3;
  }
  
  .btn__PlayPause {
    display: none;
  }
  .play-pause {
    padding: 8px;
  }
  .indicator {
    width: 10px;
    height: 10px;
    margin: 0 4px;
  }
}
</style>