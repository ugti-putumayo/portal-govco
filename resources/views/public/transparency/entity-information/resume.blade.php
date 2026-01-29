@extends('public.transparency.shared.sidebar')
@section('sidebar')
    @include('partials.sidebar', ['secciones' => $secciones])
@endsection
@section('main-content')
<div class="container">
    <h1>Directorio SIGEP - Función Pública</h1>
    <p>Puedes consultar el directorio de servidores públicos del SIGEP accediendo al siguiente enlace:</p>
    <a href="https://www1.funcionpublica.gov.co/web/sigep2/directorio" target="_blank" class="btn btn-primary">Abrir Directorio SIGEP</a>
</div>
@endsection

@push('styles')
<style>
.container {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.btn-primary {
    background-color: #004884;
    border-color: #004884;
    color: white;
    padding: 10px 20px;
    font-size: 16px;
}

.btn-primary:hover {
    background-color: #003366;
    border-color: #003366;
}
</style>
@endpush
