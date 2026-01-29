@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>{{ $dataProtection->title }}</h2>
    <p>A continuación puedes visualizar o descargar la política de tratamiento de datos:</p>
    @if($dataProtection->file_path)
        <embed src="{{ asset('storage/' . $dataProtection->file_path) }}" type="application/pdf" width="100%" height="600px" />
    
        <a href="{{ asset('storage/' . $dataProtection->file_path) }}" class="btn btn-primary mt-3" target="_blank">Descargar PDF</a>
    @else
        <p>No se encontró el archivo de la política de tratamiento de datos.</p>
    @endif
</div>

<style>
    .btn-primary {
        background-color: #0033A0;
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 5px;
    }
    .btn-primary:hover {
        background-color: #002080;
    }
</style>
@endsection
