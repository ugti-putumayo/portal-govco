@extends('layouts.app')
@section('content')
<div class="dashboard-container">
    <h2 class="dashboard-title">Resumen general</h2>
    <div class="dashboard-metrics">
        <div class="metric-card">
            <img class="metric-icon" src="{{ asset('icon/offices.svg') }}" alt="Secretarías">
            <h3>Secretarías</h3>
            <p>{{ $totalDependencies ?? 0 }}</p>
        </div>

        <div class="metric-card">
            <img class="metric-icon" src="{{ asset('icon/users-dash.svg') }}" alt="Usuarios">
            <h3>Usuarios</h3>
            <p>{{ $totalUsers ?? 0 }}</p>
        </div>

        <div class="metric-card">
            <img class="metric-icon" src="{{ asset('icon/contracts-dash.svg') }}" alt="Contratos">
            <h3>Contratos</h3>
            <p>Total: <strong>{{ $totalContracts ?? 0 }}</strong></p>
            <small>En ejecución: {{ $contractsInExecution ?? 0}}</small><br>
            <small>Finalizados: {{ $completedContracts ?? 0 }}</small>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

</script>
@endpush
@push('styles')
<style>
.dashboard-container {
    padding: 30px 40px;
    margin-left: 20px;
}

.dashboard-title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 25px;
    color: #004884;
}

.dashboard-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.metric-card {
    background-color: #ffffff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    text-align: center;
    border-left: 6px solid #004884;
    transition: transform 0.2s ease;
}

.metric-card:hover {
    transform: translateY(-4px);
}

.metric-icon {
    width: 40px;
    height: 40px;
    margin-bottom: 10px;
}

.metric-card h3 {
    font-size: 18px;
    margin: 10px 0 5px;
    color: #333;
}

.metric-card p {
    font-size: 26px;
    font-weight: bold;
    color: #004884;
    margin: 0 0 5px;
}

.metric-card small {
    color: #555;
    font-size: 13px;
}
</style>
@endpush