@extends('layouts.app')

@section('title', 'Contenido Institucional')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-5">Contenido Institucional</h2>

    @if($contents->isEmpty())
        <p class="text-center">No hay contenido institucional disponible.</p>
    @else
        @foreach($contents as $item)
            <div class="mb-5">
                <div class="content-html">
                    {!! $item->content !!}
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection

@push('styles')
<style>
.content-html img {
    max-width: 100%;
    height: auto;
}
.content-html table {
    width: 100%;
    border-collapse: collapse;
}
.content-html table, 
.content-html th, 
.content-html td {
    border: 1px solid #ccc;
    padding: 8px;
}
</style>
@endpush