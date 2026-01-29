@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Menú Participa</h2>
    <p class="text-justify">Menú Participa es una categoría que contiene información sobre los espacios, mecanismos y acciones que la entidad implementa para la participación ciudadana.</p>
    
    <div class="row">
        @foreach ($participates as $participate)
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="{{ asset($participate->image) }}" alt="{{ $participate->title }}" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">{{ $participate->title }}</h5>
                        <p class="card-text text-justify">{{ Str::limit($participate->description, 70) }}</p>
                        <a href="{{ route('participate.show', $participate->id) }}" class="btn btn-primary">Leer más...</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

<style>
    .text-justify {
        text-align: justify;
    }
</style>
