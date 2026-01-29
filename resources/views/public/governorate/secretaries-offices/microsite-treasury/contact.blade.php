@extends('layouts.app')

@section('title', 'Micrositio Hacienda - Contacto')

@section('content')
    <section class="contact-page py-5">
        <div class="container">
            <h2>Contacto</h2>
            <p>Déjanos un mensaje y nos pondremos en contacto contigo lo antes posible.</p>
            
            <form action="/contacto" method="POST" class="contact-form mt-4">
                @csrf
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group mt-3">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group mt-3">
                    <label for="message">Mensaje</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-4">Enviar</button>
            </form>
        </div>
    </section>
@endsection

<style>
    .contact-page {
        text-align: center;
    }
    .contact-form {
        max-width: 600px;
        margin: 0 auto;
        text-align: left;
    }
    .contact-form .form-control {
        border-radius: 4px;
    }
    .contact-form button {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>
