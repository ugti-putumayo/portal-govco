@extends('layouts.app')
@section('content')
<div class="container-ms organigrama-container">
    <h1 class="organigrama-titulo">Estructura Orgánica - Organigrama</h1>

    <div class="organigrama-card organigrama-card-imagen">
        <div class="organigrama-imagen-box">
            <img class="organigrama-imagen" src="{{ asset('storage/images/organigrama.jpeg') }}" alt="Organigrama de la Gobernación">
        </div>
    </div>

    <div class="organigrama-card">
        <div class="organigrama-acciones-box">
            
            <a href="https://gacetaputumayo.gov.co/files/decretos/decreto0346_8.pdf" target="_blank" class="organigrama-boton organigrama-boton-primario">
                VER ACTO ADMINISTRATIVO
            </a>
            
            <a href="{{ asset('storage/images/organigrama.jpeg') }}" download="Organigrama-Gobernacion.jpeg" class="organigrama-boton organigrama-boton-secundario">
                DESCARGAR IMAGEN
            </a>

        </div>
    </div>
</div>
@endsection
<style>
.organigrama-container {
    margin-left: auto;
    margin-right: auto;
    margin-top: 1.25rem;
    margin-bottom: 1.25rem;
}

.organigrama-titulo {
    font-size: 1.875rem;
    line-height: 2.25rem;
    font-weight: 700;
    color: var(--govco-tertiary-color, #4B4B4B);
    margin-bottom: 2rem;
    text-align: center;
}

.organigrama-card {
    background-color: var(--govco-white-color, #FFFFFF);
    border-radius: var(--govco-border-radius, 0.75rem);
    box-shadow: var(--govco-box-shadow, 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05));
    padding: 1.5rem;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
}

.organigrama-card-imagen {
    margin-bottom: 2rem;
}

.organigrama-imagen-box {
    border: 1px solid var(--govco-border-color, #E5E7EB);
    border-radius: var(--govco-border-radius, 0.5rem);
    padding: 1rem;
    background-color: var(--govco-gray-color, #F2F2F2);
    display: flex;
    justify-content: center;
}

.organigrama-imagen {
    height: auto;
    max-width: 56rem;
}

.organigrama-acciones-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.organigrama-boton {
    width: 100%;
    color: var(--govco-white-color, #FFFFFF);
    font-weight: 700;
    padding: 0.75rem 1.5rem;
    border-radius: var(--govco-border-radius, 0.5rem);
    box-shadow: var(--govco-box-shadow, 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06));
    text-align: center;
    text-decoration: none;
    transition: background-color 0.3s ease-in-out;
}

.organigrama-boton-primario {
    background-color: var(--govco-primary-color, #3366CC);
}
.organigrama-boton-primario:hover {
    color: var(--govco-white-color, #FFFFFF);
    background-color: var(--govco-secondary-color, #004884);
}

.organigrama-boton-secundario {
    background-color: var(--govco-secondary-color, #004884);
}
.organigrama-boton-secundario:hover {
    color: var(--govco-white-color, #FFFFFF);
    background-color: var(--govco-third-color, #00489A);
}


@media (min-width: 640px) {
    .organigrama-titulo {
        font-size: 2.25rem;
        line-height: 2.5rem;
    }

    .organigrama-card {
        padding: 2rem;
    }

    .organigrama-acciones-box {
        flex-direction: row;
    }

    .organigrama-boton {
        width: auto;
    }
}
</style>