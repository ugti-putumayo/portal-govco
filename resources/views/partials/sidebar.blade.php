<div class="accordion-govco" id="accordionSidebar">
    @if ($secciones->isNotEmpty())
        @foreach ($secciones as $seccion)
            <div class="item-accordion-govco">
                <h2 class="accordion-header" id="heading{{ $seccion->id }}">
                    <button class="button-accordion-govco collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $seccion->id }}" aria-expanded="false" aria-controls="collapse{{ $seccion->id }}">
                        <span class="text-button-accordion-govco">{{ $seccion->titulo }}</span>
                    </button>
                </h2>

                <div id="collapse{{ $seccion->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $seccion->id }}" data-bs-parent="#accordionSidebar">
                    <div class="body-accordion-govco">
                        @php
                            $subElementos = DB::table('transparencia')->where('tipo', 'subelemento')->where('id_padre', $seccion->id)->orderBy('orden')->get();
                        @endphp

                        @if ($subElementos->isNotEmpty())
                            <ol class="list-group-numbered">
                                @foreach ($subElementos as $subElemento)
                                    <li>
                                        <a href="{{ $subElemento->enlace }}" class="text-primary sub-element">{{ $subElemento->titulo }}</a>
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <p>No hay subelementos disponibles.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <p>No hay datos disponibles.</p>
    @endif
</div>

<style scoped>
.body-accordion-govco{
    margin: 1rem;
}

.accordion-govco .button-accordion-govco {
    width: 100%;
    text-align: left;
    background-color: var(--govco-white-color);
    border: 0;
    padding: 1rem;
    border-bottom: 1px solid #ddd;
    display: flex;
    align-items: center;
    position: relative;
    transition: background-color 0.3s ease;
}

.accordion-govco .button-accordion-govco::after {
    content: '';
    display: inline-block;
    width: 24px;
    height: 24px;
    background: url('/icons/angle-up.svg') no-repeat center;
    background-size: contain;
    margin-left: auto;
    transition: transform 0.3s ease;
}

.accordion-govco .button-accordion-govco.collapsed::after {
    transform: rotate(180deg);
}

.accordion-govco .button-accordion-govco.active, .accordion-govco .button-accordion-govco:focus {
    background-color: #e7f3ff;
    border-left: 4px solid #004884;
    outline: none;
}

.list-group-numbered {
    padding-left: 1rem;
    margin-top: 10px;
    margin-bottom: 0;
    color: #000 !important;
}

.list-group-numbered li {
    margin-bottom: 8px;
    font-size: 16px;
}

.list-group-numbered li {
    color: var(--gov-black-color) !important;
    text-decoration: none;
    font-weight: normal;
}

.list-group-numbered li:hover {
    color: var(--govco-secondary-color) !important;
    text-decoration: underline;
    font-weight: 500;
}

.list-group-numbered li a {
    color: var(--gov-black-color) !important;
    text-decoration: none;
    font-weight: normal;
    letter-spacing: normal;
}

.list-group-numbered li a:hover {
    color: var(--govco-secondary-color) !important;
    text-decoration: underline;
    font-weight: 500;
}

.list-group-numbered li a.active {
    font-weight: bold;
    text-decoration: underline;
    color: #b03535;
}
</style>