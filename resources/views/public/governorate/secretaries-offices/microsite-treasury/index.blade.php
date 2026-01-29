@extends('layouts.app')

@section('title', 'Micrositio Hacienda - Sobre Nosotros')

@section('content')
    <section class="slider-section">
        <h2 style="display: none;">Slider</h2>
        @include('public.governorate.secretaries-offices.microsite-treasury.slider')
    </section>
    <div style="margin: 20px 0;"></div>
    <section class="about-section">
        <h2 style="display: none;">Sobre Nosotros</h2>
        @include('public.governorate.secretaries-offices.microsite-treasury.about')
    </section>
    <section class="fiscalizacion-section">
        <h2 style="display: none;">fiscalizacion</h2>
        @include('public.governorate.secretaries-offices.microsite-treasury.audit')
    </section>
@endsection
