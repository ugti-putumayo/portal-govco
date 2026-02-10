@extends('layouts.app')
@section('title', 'Inicio')
@section('content')
    <div class="full-width-section">
        <x-slider :images="$images" />
        <x-public.home.care-services-entity :tramiteServices="$tramiteServices" :citizenServices="$citizenServices"/>
    </div>
    @php
        $microsites = [
            ['label' => 'Rentas Departamental', 'url' => 'https://historico.ticputumayo.gov.co/index.php/gestion-de-gobierno/rentas-departamental', 'icon' => '/img/microsites/taxes.svg'],
            ['label' => 'Gestión de Información Estadistica', 'url' => 'https://www.putumayo.gov.co/statistical-information-management', 'icon' => '/img/microsites/statistic.svg'],
            ['label' => 'ICETEX', 'url' => 'https://historico.ticputumayo.gov.co/index.php/component/sppagebuilder/page/423', 'icon' => '/img/microsites/student.svg'],
        ];
    @endphp
    <x-public.home.apps/>
    <x-public.home.microsites 
        :items="$microsites"
        title="Micro Sitios"
        intro="Conoce aquí la información complementaria de todos nuestros procesos y programas."
        variant="grid"
        :columns="4"
    />

    <x-public.home.social-media/>

    <x-public.home.calls-job 
        :publications="$callsjob"
    />

    <section class="news-tabs govco-container" aria-label="Sección de noticias">
        <div class="tabs tabs--news" role="tablist">
            <button class="tab is-active"
                    role="tab"
                    aria-selected="true"
                    aria-controls="tab-news"
                    data-target="tab-news">
            Noticias
            </button>
            <button class="tab"
                    role="tab"
                    aria-selected="false"
                    aria-controls="tab-anti"
                    data-target="tab-anti">
            Anticontrabando
            </button>
        </div>

        <div id="tab-news" class="tab-pane is-active" role="tabpanel">
            <x-public.home.news :publications="$publications" />
        </div>

        <div id="tab-anti" class="tab-pane" role="tabpanel" hidden>
            <x-public.home.news-antismuggling :publications="$publicationsAnti" />
        </div>
    </section>
    <x-public.home.interest-links />
@endsection
<script>
function showTab(tabName, el) {
    const tabs = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tramites-section .tabs .tab');

    tabs.forEach(tab => tab.classList.remove('active'));
    buttons.forEach(btn => btn.classList.remove('active'));

    document.getElementById(`tab-${tabName}`).classList.add('active');
    el.classList.add('active');
}

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.tabs--news .tab');
  if (!btn) return;

  const tabs = btn.parentElement.querySelectorAll('.tab');
  const panes = btn.closest('.news-tabs').querySelectorAll('.tab-pane');

  tabs.forEach(t => { t.classList.remove('is-active'); t.setAttribute('aria-selected','false'); });
  panes.forEach(p => { p.classList.remove('is-active'); p.hidden = true; });

  btn.classList.add('is-active');
  btn.setAttribute('aria-selected','true');

  const id = btn.dataset.target;
  const pane = document.getElementById(id);
  if (pane) { pane.classList.add('is-active'); pane.hidden = false; }
});

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.tabs--social .tab');
  if (!btn) return;

  const root = btn.closest('.social-tabs');
  const tabs = root.querySelectorAll('.tabs--social .tab');
  const panes = root.querySelectorAll('.tab-pane');

  tabs.forEach(t => { t.classList.remove('is-active'); t.setAttribute('aria-selected','false'); });
  panes.forEach(p => { p.classList.remove('is-active'); p.hidden = true; });

  btn.classList.add('is-active');
  btn.setAttribute('aria-selected','true');

  const id = btn.dataset.target;
  const pane = root.querySelector('#' + id);
  if (pane) { pane.classList.add('is-active'); pane.hidden = false; }
});
</script>

<style>
.full-width-section {
    width: 99.7vw;
    margin-left: calc(-50vw + 50%);
    overflow: hidden;
    position: relative;
}
</style>