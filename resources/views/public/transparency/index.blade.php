@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-primary">Transparencia y acceso a información pública</h1>
    <p>De acuerdo a la Ley 1712 de 2014, y resolución 3564 de 2015 de MinTIC se pone a disposición de la ciudadanía la sección de Transparencia y Acceso a la Información Pública Nacional, donde podrán conocer de primera mano toda la información.</p>
    
    <div class="my-4">
        <label for="search-input" class="form-label">Buscar</label>
        <div class="input-group">
            <input type="text" class="form-control" id="search-input" placeholder="Escribe lo que buscas" aria-label="Buscar">
            <button class="btn btn-outline-secondary" type="button">
              <img src="/icons/search.svg" width="16" height="16">
            </button>
        </div>
    </div>

    <div class="accordion-govco" id="accordionExampleTwo">
        @if ($secciones->isNotEmpty())
            @foreach ($secciones as $seccion)
                <div class="item-accordion-govco">
                    <h2 class="accordion-header" id="heading{{ $seccion->id }}">
                        <button class="button-accordion-govco collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $seccion->id }}" aria-expanded="false" aria-controls="collapse{{ $seccion->id }}">
                            <span class="icon-button-accordion-govco">{{ explode('.', $seccion->orden)[0] }}</span>
                            <span class="text-button-accordion-govco">{{ $seccion->titulo }}</span>
                        </button>
                    </h2>
                    <div id="collapse{{ $seccion->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $seccion->id }}" data-bs-parent="#accordionExampleTwo">
                      <div class="body-accordion-govco">
                          @if (!empty($seccion->descripcion))
                              <p class="descripcion">{{ nl2br(e($seccion->descripcion)) }}</p>
                          @endif

                          @php
                              // Obtener los subelementos de la sección actual
                              $subElementos = DB::table('transparencia')->where('tipo', 'subelemento')->where('id_padre', $seccion->id)->orderBy('orden')->get();
                          @endphp

                          @if ($subElementos->isNotEmpty())
                              <ol>
                                  @foreach ($subElementos as $subElemento)
                                      <li>
                                          <!-- Utilizar el campo 'enlace' para generar el enlace correcto para cada subelemento -->
                                          <a href="{{ $subElemento->enlace }}" class="text-primary">
                                              {{ $subElemento->titulo }}
                                          </a>
                                      </li>
                                  @endforeach
                              </ol>
                          @else
                              <p>No hay subelementos disponibles.</p>
                          @endif

                          <!-- Botón para ver más detalles de la sección -->
                          <a href="{{ route('transparencia.show', ['id' => $seccion->id]) }}" class="btn btn-primary mt-2">
                              Ver más detalles
                          </a>
                      </div>
                  </div>
                </div>
            @endforeach
        @else
            <p>No hay datos disponibles.</p>
        @endif
    </div>
</div>
@endsection

<style>   
.accordion-govco .accordion-header {
  line-height: 0;
  margin: 0;
}

.accordion-govco .button-accordion-govco {
  width: 100%;
  text-align: left;
  background-color: var(--govco-white-color);
  border: 0;
  min-height: 4.375rem;
  padding: 0 1.5rem;
  border-bottom: 0.125rem solid var(--govco-gray-color);
  display: flex;
  align-items: center;
  position: relative;
}

.accordion-govco .button-accordion-govco:focus {
  background-color: var(--govco-white-color);
}

.accordion-govco .text-button-accordion-govco {
  font-family: var(--govco-font-primary);
  color: var(--govco-tertiary-color);
  font-size: 18px;
  line-height: 1rem;
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

.accordion-govco .item-accordion-govco {
  background-color: var(--govco-white-color);
}

.accordion-govco .body-accordion-govco {
  padding: 1.875rem 1.5rem;
  background-color: var(--govco-gray-color);
}

.accordion-govco .title-one-accordion-govco {
  color: var(--govco-tertiary-color);
  font-size: 18px;
  font-family: var(--govco-font-primary);
  display: block;
}

.accordion-govco .title-two-accordion-govco {
  color: var(--govco-primary-color);
  font-size: 16px;
  font-family: var(--govco-font-primary);
  margin-left: 0.875rem;
  margin-top: 1.875rem;
  display: block;
}

.accordion-govco .text-one-accordion-govco {
  color: var(--govco-tertiary-color);
  font-size: 16px;
  font-family: var(--govco-font-secondary);
  margin-left: 0.875rem;
  margin-top: 0.938rem;
  margin-bottom: 0;
  display: block;
  line-height: 1.5rem;
}

.accordion-govco .icon-button-accordion-govco {
  font-family: var(--govco-font-primary);
  font-size: 20px;
  color: var(--govco-white-color);
  background-color: var(--govco-secondary-color);
  border-radius: 50%;
  min-width: 2rem;
  height: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.625rem;
}
</style>