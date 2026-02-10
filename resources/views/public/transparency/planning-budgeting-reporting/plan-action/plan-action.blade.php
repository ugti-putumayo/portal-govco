@extends('public.transparency.shared.sidebar')

@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="container py-4 govco-override-container">
    <h1 class="mb-2">{{ $page->title ?? 'Plan de Acción' }}</h1>

    @if(!empty($page->description))
        <p class="mb-4">{!! $page->description !!}</p>
    @else
        <p>
            El <strong>Plan de Acción</strong> integra los objetivos y estrategias institucionales programados para la vigencia actual.
            Su publicación permite a la ciudadanía realizar seguimiento al cumplimiento de las metas propuestas por la entidad,
            garantizando el principio de publicidad y transparencia.
        </p>
    @endif

    @php
        $grouped = ($items ?? collect())
            ->filter(fn ($i) => !empty($i->title))
            ->groupBy(fn ($i) => optional($i->created_at)->year ?? 'Sin fecha')
            ->sortKeysDesc();
    @endphp

    @foreach($grouped as $year => $yearItems)
        <h2 class="mt-4 mb-2" style="border-bottom: 2px solid var(--govco-secondary-color); padding-bottom: 10px;">
            {{ $year }}
        </h2>

        <table class="table table-bordered mt-3 align-middle">
            <thead class="govco-table-header">
                <tr>
                    <th>Título</th>
                    <th class="text-center">Documento</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($yearItems as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td class="text-center">
                            @if(!empty($item->document))
                                <a href="{{ asset('storage/'.$item->document) }}"
                                   target="_blank"
                                   class="btn-ver-pdf">
                                   Ver
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if(!empty(trim(strip_tags((string)$item->description))))
                                {!! $item->description !!}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="mt-5 mb-5 govco-plan-list">
        <h2 style="border-bottom: 2px solid var(--govco-secondary-color); padding-bottom: 10px; margin-bottom: 20px;">
            Planes Institucionales
        </h2>

        <ol>
            <li><a href="{{ route('plan_action.institutional_archives') }}" class="plan-link-item">Plan Institucional de Archivos de la Entidad PINAR</a></li>
            <li><a href="{{ route('plan_action.transparency_plan') }}" class="plan-link-item">Plan de Transparencia</a></li>
            <li><a href="{{ route('plan_action.annual_acquisitions') }}" class="plan-link-item">Plan Anual de Adquisiciones</a></li>
            <li><a href="{{ route('plan_action.annual_vacancies') }}" class="plan-link-item">Plan Anual de Vacantes</a></li>
            <li><a href="{{ route('plan_action.hr_forecasting') }}" class="plan-link-item">Plan de Previsión de Recursos Humanos</a></li>
            <li><a href="{{ route('plan_action.strategic_human_talent') }}" class="plan-link-item">Plan Estratégico de Talento Humano</a></li>
            <li><a href="{{ route('plan_action.institutional_training') }}" class="plan-link-item">Plan Institucional de Capacitación</a></li>
            <li><a href="{{ route('plan_action.institutional_incentives') }}" class="plan-link-item">Plan de Incentivos Institucionales</a></li>
            <li><a href="{{ route('plan_action.occupational_health_safety') }}" class="plan-link-item">Plan de Trabajo Anual en Seguridad y Salud en el Trabajo</a></li>
            <li><a href="{{ route('plan_action.anti_corruption') }}" class="plan-link-item">Plan Anticorrupción y de Atención al Ciudadano</a></li>
            <li><a href="{{ route('plan_action.it_strategic') }}" class="plan-link-item">Plan Estratégico de Tecnologías de la Información y las Comunicaciones - PETI</a></li>
            <li><a href="{{ route('plan_action.risk_treatment') }}" class="plan-link-item">Plan de Tratamiento de Riesgos de Seguridad y Privacidad de la Información</a></li>
            <li><a href="{{ route('plan_action.security_privacy') }}" class="plan-link-item">Plan de Seguridad y Privacidad de la Información</a></li>
            <li><a href="{{ route('plan_action.declaration_ptep') }}" class="plan-link-item">Declaración de la Gobernación del Putumayo. Construyendo un futuro de Transparencia y Ética en el Putumayo.</a></li>
        </ol>
    </div>

    <p class="mt-4 border-top pt-3">
        Si tiene observaciones sobre nuestro Plan de Acción, puede enviarlas al correo institucional:
        <a href="mailto:contactenos@putumayo.gov.co" style="color: var(--govco-secondary-color); font-weight: bold;">contactenos@putumayo.gov.co</a>
    </p>
</div>

<style>
.govco-override-container {
    font-family: 'Work Sans', sans-serif !important;
    color: var(--govco-tertiary-color) !important;
}

.govco-override-container h1,
.govco-override-container h2 {
    font-family: 'Montserrat', sans-serif !important;
    color: var(--govco-secondary-color) !important;
    font-weight: 700 !important;
}

.govco-plan-list ol {
    padding-left: 20px;
}

.govco-plan-list li {
    margin-bottom: 8px;
    color: var(--govco-secondary-color) !important;
}

.plan-link-item {
    color: var(--govco-secondary-color) !important;
    text-decoration: none !important;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
}

.plan-link-item:hover {
    color: var(--govco-accent-color) !important;
    text-decoration: underline !important;
    cursor: pointer;
}

.govco-table-header {
    background-color: var(--govco-secondary-color) !important;
    color: #FFFFFF !important;
}

.btn-govco-custom {
    border: 1px solid var(--govco-secondary-color) !important;
    color: var(--govco-secondary-color) !important;
    background: transparent;
}

.btn-govco-custom:hover {
    background-color: var(--govco-secondary-color) !important;
    color: #FFFFFF !important;
}
</style>
@endsection