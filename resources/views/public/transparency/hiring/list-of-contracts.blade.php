@extends('public.transparency.shared.sidebar')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection

@section('main-content')
<div class="govco-container-contracts">
    <div class="govco-header-contracts">
        <h1 class="govco-title-contracts">Listado de contratos</h1>
        <p class="govco-subtitle-contracts">
            Consulta los contratos suscritos por la entidad. La información se presenta de forma paginada.
        </p>
    </div>

    {{-- Buscador + botón exportar --}}
    <div class="govco-search-row">
        <form method="GET" class="govco-search-form">
            <div class="govco-search-bar">
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Contratista, número, objeto..."
                    class="govco-search-input"
                >
                <button type="submit" class="govco-search-button">
                    Buscar
                </button>
            </div>
        </form>

        <button type="button" class="govco-export-button" onclick="openModalExportContracts()">
            Exportar Excel
        </button>
    </div>

    <div class="govco-card-contracts">
        @if($contractors->isEmpty())
            <p class="govco-empty-text">
                No se encontraron contratos para los criterios de búsqueda.
            </p>
        @else
            <div class="govco-table-wrapper">
                <table class="govco-table-contracts">
                    <thead>
                        <tr>
                            <th>Año</th>
                            <th>Mes</th>
                            <th>Número de contrato</th>
                            <th>Fecha contrato</th>
                            <th>Código SECOP</th>
                            <th>Clase de contrato</th>
                            <th>Contratista</th>
                            <th>Firma contratista</th>
                            <th>Modalidad del proceso</th>
                            <th>Objeto</th>
                            <th>Plazo</th>
                            <th>Fecha inicio</th>
                            <th>Fecha corte</th>
                            <th>Valor total</th>
                            <th>Dependencia</th>
                            <th>Enlace SECOP</th>
                            <th>Supervisión</th>
                            <th>Clase de gasto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $months = [
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                            ];
                        @endphp

                        @foreach($contractors as $contractor)
                            <tr>
                                <td>{{ $contractor->year_contract }}</td>
                                <td>{{ $months[$contractor->month_contract] ?? $contractor->month_contract }}</td>
                                <td>{{ $contractor->contract_number }}</td>
                                <td>{{ $contractor->date_contract }}</td>
                                <td>{{ $contractor->code_secop }}</td>
                                <td>{{ $contractor->class_contract }}</td>
                                <td>{{ $contractor->contractor }}</td>
                                <td>{{ $contractor->firm_contractor }}</td>
                                <td>{{ $contractor->process_modality }}</td>

                                {{-- Objeto truncado a 50 caracteres con "Ver más" --}}
                                <td class="govco-cell-object">
                                    <span class="object-text short-text">
                                        {{ \Illuminate\Support\Str::limit($contractor->object, 50, '...') }}
                                    </span>
                                    <span class="object-text full-text d-none">
                                        {{ $contractor->object }}
                                    </span>
                                    @if(strlen($contractor->object) > 50)
                                        <button type="button" class="toggle-object-btn">Ver más</button>
                                    @endif
                                </td>

                                <td>{{ $contractor->contract_term }}</td>
                                <td>{{ $contractor->start_date }}</td>
                                <td>{{ $contractor->cutoff_date }}</td>
                                <td>
                                    @if(!is_null($contractor->total_value))
                                        $ {{ number_format($contractor->total_value, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td>{{ $contractor->dependency }}</td>
                                <td>
                                    @if($contractor->link_secop)
                                        <a
                                            href="{{ $contractor->link_secop }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="govco-link-secop"
                                        >
                                            Ver en SECOP
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $contractor->supervision }}</td>
                                <td>{{ $contractor->expense_class }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="govco-pagination-wrapper">
                {{ $contractors->appends(request()->input())->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
</div>

<div id="modalExportContracts" class="govco-modal-overlay">
    <div class="govco-modal-content">
        <span class="govco-close-modal" onclick="closeModalExportContracts()">&times;</span>
        <h2>Exportar archivo</h2>

        <form method="GET" action="{{ route('dashboard.contractual.export') }}">
            @csrf
            <label for="year_contract">Año:</label>
            <select name="year_contract" id="year_contract" required>
                <option value="">-- Selecciona el año --</option>
                @for ($year = 1990; $year <= 2050; $year++)
                    <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endfor
            </select>

            <label for="month_contract">Mes:</label>
            <select name="month_contract">
                <option value="">-- Todos los meses --</option>
                <option value="1">Enero</option>
                <option value="2">Febrero</option>
                <option value="3">Marzo</option>
                <option value="4">Abril</option>
                <option value="5">Mayo</option>
                <option value="6">Junio</option>
                <option value="7">Julio</option>
                <option value="8">Agosto</option>
                <option value="9">Septiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
            </select>

            <button type="submit" class="govco-modal-submit">Exportar Excel</button>
        </form>
    </div>
</div>


<script>
    function openModalExportContracts() {
        document.getElementById('modalExportContracts').style.display = 'flex';
    }

    function closeModalExportContracts() {
        document.getElementById('modalExportContracts').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.toggle-object-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const cell = btn.closest('.govco-cell-object');
                const shortText = cell.querySelector('.short-text');
                const fullText = cell.querySelector('.full-text');

                const isExpanded = !fullText.classList.contains('d-none');
                fullText.classList.toggle('d-none', isExpanded);
                shortText.classList.toggle('d-none', !isExpanded);
                btn.textContent = isExpanded ? 'Ver más' : 'Ver menos';
            });
        });
    });
</script>

<style>
    .govco-container-contracts {
        font-family: var(--govco-font-primary);
        color: var(--govco-tertiary-color);
        padding-top: 1.5rem;
        padding-bottom: 2rem;
    }

    .govco-header-contracts {
        margin-bottom: 1rem;
    }

    .govco-title-contracts {
        font-weight: 700;
        color: var(--govco-secondary-color);
        font-size: 1.7rem;
        margin: 0;
    }

    .govco-subtitle-contracts {
        font-size: .95rem;
        margin-top: .35rem;
        color: var(--govco-tertiary-color);
    }

    .govco-search-row {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 1rem;
    }

    .govco-search-form {
        width: 100%;
        max-width: 420px;
    }

    .govco-search-bar {
        display: flex;
        gap: .5rem;
    }

    .govco-search-input {
        flex: 1;
        border: 1px solid var(--govco-border-color);
        border-radius: var(--govco-border-radius);
        padding: .5rem .75rem;
        font-size: .95rem;
    }

    .govco-search-button {
        background-color: var(--govco-primary-color);
        border: none;
        border-radius: var(--govco-border-radius);
        color: white;
        padding: .5rem 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s ease;
        white-space: nowrap;
    }

    .govco-search-button:hover {
        background-color: var(--govco-secondary-color);
    }

    .govco-card-contracts {
        background: var(--govco-white-color);
        border-radius: var(--govco-border-radius);
        box-shadow: var(--govco-box-shadow);
        padding: 1rem 1.25rem;
    }

    .govco-table-wrapper {
        overflow-x: auto;
    }

    .govco-table-contracts {
        width: 100%;
        border-collapse: collapse;
        font-size: .9rem;
    }

    .govco-table-contracts thead {
        background-color: var(--govco-secondary-color);
        color: var(--govco-white-color);
    }

    .govco-table-contracts th,
    .govco-table-contracts td {
        padding: .6rem .5rem;
        border-bottom: 1px solid var(--govco-gray-color);
    }

    .govco-table-contracts tbody tr:nth-child(even) {
        background-color: var(--govco-gray-color);
    }

    .govco-table-contracts tbody tr:hover {
        background-color: rgba(51, 102, 204, 0.05);
    }

    .govco-link-secop {
        color: var(--govco-primary-color);
        font-weight: 600;
        text-decoration: none;
        font-size: .85rem;
    }

    .govco-link-secop:hover {
        text-decoration: underline;
        color: var(--govco-accent-color);
    }

    /* Objeto truncado */
    .govco-cell-object {
        max-width: 350px;
        position: relative;
    }

    .object-text {
        display: inline-block;
        vertical-align: top;
    }

    .toggle-object-btn {
        background: none;
        border: none;
        color: var(--govco-primary-color);
        font-size: .85rem;
        margin-left: .25rem;
        cursor: pointer;
        text-decoration: underline;
        padding: 0;
    }

    .toggle-object-btn:hover {
        color: var(--govco-secondary-color);
    }

    .govco-pagination-wrapper nav {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
    }

    .govco-pagination-wrapper .pagination {
        margin: 0;
    }

    @media (max-width: 768px) {
        .govco-search-row {
            justify-content: flex-start;
        }

        .govco-search-form {
            max-width: 100%;
        }

        .govco-search-bar {
            flex-direction: column;
        }

        .govco-search-button {
            width: 100%;
        }
    }

    .govco-export-button {
        background-color: var(--govco-success-color, #007E47);
        color: var(--govco-white-color, #fff);
        border: none;
        border-radius: var(--govco-border-radius, 6px);
        padding: .5rem 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s ease;
        height: 42px;
        display: flex;
        align-items: center;
        margin-left: 2px;
        gap: .4rem;
        font-size: .9rem;
        white-space: nowrap;
    }

    .govco-export-button:hover {
        background-color: #056d50;
    }

    .govco-modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.45);
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .govco-modal-content {
        background: var(--govco-white-color, #fff);
        border-radius: var(--govco-border-radius, 6px);
        padding: 1.5rem 2rem;
        width: 90%;
        max-width: 400px;
        box-shadow: var(--govco-box-shadow, 0 3px 10px rgba(0,0,0,.15));
        position: relative;
        animation: fadeIn .3s ease;
    }

    .govco-close-modal {
        position: absolute;
        right: 1rem;
        top: .8rem;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--govco-border-color, #888);
    }

    .govco-modal-content h2 {
        font-size: 1.3rem;
        margin-bottom: 1rem;
        color: var(--govco-secondary-color, #003366);
        text-align: center;
    }

    .govco-modal-content label {
        display: block;
        font-weight: 600;
        margin-top: .75rem;
    }

    .govco-modal-content input,
    .govco-modal-content select {
        width: 100%;
        padding: .4rem .5rem;
        border: 1px solid var(--govco-border-color, #ccc);
        border-radius: var(--govco-border-radius, 6px);
        margin-top: .25rem;
    }

    .govco-modal-submit {
        background-color: var(--govco-primary-color, #3366CC);
        border: none;
        border-radius: var(--govco-border-radius, 6px);
        color: var(--govco-white-color, #fff);
        padding: .6rem 1rem;
        margin-top: 1.2rem;
        font-weight: 600;
        width: 100%;
        cursor: pointer;
    }

    .govco-modal-submit:hover {
        background-color: var(--govco-secondary-color, #002f6c);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection