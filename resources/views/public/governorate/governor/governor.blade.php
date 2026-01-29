@extends('layouts.app')

@push('scripts')
<script>
function closeDetails() {
    document.getElementById('cabinet-details-panel').classList.add('d-none');
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cabinet-show-details-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const po = JSON.parse(this.dataset.po);
            const panel = document.getElementById('cabinet-details-panel');

            document.getElementById('detail-name').innerText = po.fullname;
            document.getElementById('detail-charge').innerText = po.charge ?? 'N/A';
            document.getElementById('detail-dependency').innerText = po.dependency ?? 'N/A';
            document.getElementById('detail-subdependencie').innerText = po.subdependencie ?? 'N/A';
            document.getElementById('detail-email').innerText = po.email ?? 'N/A';
            document.getElementById('detail-init_date').innerText = po.init_date ?? 'N/A';

            panel.classList.remove('d-none');
        });
    });
});
</script>
@endpush

@section('content')
<div class="cabinet-container">
    <h2 class="text-center mb-5">Gabinete Departamental - {{ $typeCharge }}</h2>

    @if(isset($message))
        <p class="text-center">{{ $message }}</p>
    @elseif($governor->isEmpty())
        <p class="text-center">No se encontraron funcionarios para el cargo {{ $typeCharge }}.</p>
    @else
        <div class="cabinet-grid">
        @foreach($governor as $po)
            @if($po->charge === 'Gobernador')
                <div class="cabinet-center">
                    <div class="cabinet-card">
                        @if($po->image)
                            <img src="{{ asset('storage/' . $po->image) }}" class="cabinet-card-img" alt="Imagen de {{ $po->fullname }}">
                        @endif
                        <div class="cabinet-card-body">
                            <p class="cabinet-text">{{ $po->subdependencie ?? 'Sin área' }}</p>
                            <h5 class="cabinet-title">{{ $po->fullname }}</h5>
                            <p class="cabinet-text">{{ $po->charge ?? 'Cargo no asignado' }}</p>
                            <button class="cabinet-btn cabinet-show-details-btn" data-po='@json($po)'>
                                Ver detalles
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="cabinet-column">
                    <div class="cabinet-card">
                        @if($po->image)
                            <img src="{{ asset('storage/' . $po->image) }}" class="cabinet-card-img" alt="Imagen de {{ $po->fullname }}">
                        @endif
                        <div class="cabinet-card-body">
                            <p class="cabinet-text">{{ $po->subdependencie ?? 'Sin área' }}</p>
                            <h5 class="cabinet-title">{{ $po->fullname }}</h5>
                            <p class="cabinet-text">{{ $po->charge ?? 'Cargo no asignado' }}</p>
                            <button class="cabinet-btn cabinet-show-details-btn" data-po='@json($po)'>
                                Ver detalles
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
        </div>

        <div class="cabinet-details-panel d-none" id="cabinet-details-panel">
            <button class="cabinet-close-btn" onclick="closeDetails()" aria-label="Cerrar">×</button>
            <div class="p-4">
                <h4 id="detail-name" class="mb-3"></h4>
                <p><strong>Cargo:</strong> <span id="detail-charge"></span></p>
                <p><strong>Dependencia:</strong> <span id="detail-dependency"></span></p>
                <p><strong>Subdependencia:</strong> <span id="detail-subdependencie"></span></p>
                <p><strong>Correo:</strong> <span id="detail-email"></span></p>
                <p><strong>Fecha de ingreso:</strong> <span id="detail-init_date"></span></p>
            </div>
        </div>
    @endif
</div>
@endsection

<style>
.cabinet-container {
    max-width: 100%;
    padding: 0 15px;
    margin: 2rem;
}

.cabinet-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.cabinet-center {
    display: flex;
    justify-content: center;
    width: 100%;
}

.cabinet-column {
    width: 100%;
}

.cabinet-card {
    border-radius: 20px;
    overflow: hidden;
    border: none;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    background-color: #fff;
    max-width: 350px;
    margin: auto;
}

.cabinet-card-img {
    width: 100%;
    height: 280px;
    object-fit: cover;
    border: none;
    border-radius: 20px 20px 0 0;
    margin: 0;
    padding: 0;
    display: block;
    background-color: transparent;
}

.cabinet-card-body {
    padding: 1.25rem;
    text-align: center;
}

.cabinet-title {
    font-size: 1.25rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.cabinet-text {
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
    color: #555;
}

.cabinet-btn {
    border: 1px solid #007bff;
    background-color: transparent;
    color: #007bff;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.cabinet-btn:hover {
    background-color: #007bff;
    color: #fff;
}

.cabinet-card:hover {
    transform: translateY(-5px);
}

.cabinet-details-panel {
    position: fixed;
    top: 0;
    right: 0;
    height: 100%;
    width: 400px;
    background-color: #fff;
    box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1050;
    overflow-y: auto;
    border-left: 1px solid #dee2e6;
    transition: transform 0.3s ease-in-out;
    border-top-left-radius: 15px;
    border-bottom-left-radius: 15px;
}

.cabinet-details-panel.d-none {
    transform: translateX(100%);
}

.cabinet-details-panel:not(.d-none) {
    transform: translateX(0);
}

.cabinet-close-btn {
    background: none;
    border: none;
    font-size: 2rem;
    position: absolute;
    color: var(--govco-secondary-color);
    top: 0;
    right: 0;
    margin: 1rem;
    cursor: pointer;
}

.cabinet-close-btn:hover {
    background-color: var(--govco-fourth-color);
}
</style>