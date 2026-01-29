@extends('layouts.guest')

@section('content')
<div class="govco-login-layout">
    <div class="govco-login-left">
        <div class="form-container">
            <div class="entity-header">
                <img src="{{ asset($entityLogo) }}" class="govco-logo" alt="Logo {{ $entityName }}">
                <span class="entity-name">{{ $entityName }}</span>
            </div>
            <h2>Inicio de Sesión</h2>
            @if (session('status'))
                <div class="status-message">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>

                <div class="form-aux">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>

                <button type="submit" class="login-button">Iniciar Sesión</button>
            </form>

            <div class="divider">o continuar con</div>

            <div class="social-login">
                <a href="{{ route('login.google') }}" class="social-btn">
                    <img src="{{ asset('logos/google.png') }}" alt="Google"> Google
                </a>
                <a href="{{ route('login.outlook') }}" class="social-btn outlook">
                    <img src="{{ asset('logos/outlook.png') }}" alt="Outlook"> Outlook
                </a>
            </div>

            <div class="signup-note">
                ¿No tienes una cuenta? <a href="#">Regístrate</a>
            </div>
        </div>
    </div>

    <div class="govco-login-right">
        <div class="overlay-content">
            <div class="overlay-text">
                <h3>Una nueva forma de<br>transformar lo público<br>con tecnología e innovación.</h3>
                <a href="#" class="learn-more">Conoce más</a>
            </div>
            <div class="overlay-image">
                <img src="{{ asset('img/auth/auth-white.svg') }}" alt="Ilustración" />
            </div>
        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

body {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    background-color: #000;
}

.govco-login-layout {
    display: flex;
    height: 100vh;
    width: 100%;
}

.govco-login-left {
    width: 50%;
    background: radial-gradient(circle at top left, #00489A 0%, #002d4a 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.form-container {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
}

.entity-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.govco-logo {
    width: 60px;
    margin-bottom: 1rem;
}

.entity-name {
    font-size: 1.7rem;
    font-weight: 600;
    color: #ffffff;
}

h2 {
    font-size: 1.8rem;
    margin-bottom: 2rem;
    color: #ffffff;
}

.form-group {
    margin-bottom: 1.5rem;
}

label {
    font-size: 0.9rem;
    color: #dce3ea; /* Gris claro-legible sobre azul */
    margin-bottom: 0.4rem;
    display: block;
}

.input-icon {
    position: relative;
}

.input-icon i {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    color: #999;
}

.input-icon input {
    width: 100%;
    padding: 0.75rem 0.75rem 0.75rem 2.5rem;
    background: #102e47;
    border: 1px solid #3d5a73;
    color: white;
    font-size: 1rem;
    border-radius: 4px;
    transition: border-color 0.3s ease;
}

.input-icon input,
.login-button,
.social-btn {
    width: 100%;
    box-sizing: border-box;
}

.input-icon input:focus {
    border-color: var(--govco-fourth-color);
    background-color: #143e61;
}

input::placeholder {
    color: #aab8c2;
}

.form-aux {
    text-align: right;
    margin-bottom: 1.5rem;
}

.form-aux a {
    color: #FEDF13;
    font-size: 0.85rem;
    text-decoration: none;
}

.form-aux a:hover {
    text-decoration: underline;
}

.login-button {
    width: 100%;
    padding: 0.75rem;
    background-color:var(--govco-fourth-color);
    color: var(--govco-primary-color);
    border: none;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    border-radius: 4px;
    margin-bottom: 1rem;
    transition: background 0.3s ease;
}

.login-button:hover {
    color: white;
    background-color: #00325d;
}

.divider {
    text-align: center;
    font-size: 0.85rem;
    color: #888;
    margin: 1rem 0;
}

.social-login {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.social-btn {
    background: #fff;
    color: #000;
    text-align: center;
    padding: 0.6rem;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
}

.social-btn img {
    height: 20px;
    margin-right: 10px;
}

.social-btn.outlook {
    background: #0072C6;
    color: white;
}

.social-btn.outlook:hover {
    background: #005a9e;
}

.signup-note {
    text-align: center;
    font-size: 0.85rem;
    margin-top: 2rem;
}

.signup-note a {
    color: #FEDF13;
    text-decoration: none;
}

.govco-login-right {
    width: 50%;
    background: linear-gradient(135deg, #00489A 0%, #005ea8 100%);
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    padding: 2rem;
}

.overlay-content {
    max-width: 500px;
    text-align: center;
}

.overlay-text h3 {
    font-size: 1.5rem;
    line-height: 1.6;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 1rem;
}

.overlay-text .learn-more {
    color: #FEDF13;
    font-weight: 600;
    text-decoration: underline;
    font-size: 0.95rem;
}

.overlay-image img {
    margin-top: 2rem;
    max-width: 300px;
    width: 100%;
    height: auto;
    opacity: 0.9;
}

@media (max-width: 768px) {
    .govco-login-layout {
        flex-direction: column;
    }

    .govco-login-left,
    .govco-login-right {
        width: 100%;
        height: auto;
    }

    .overlay-text {
        position: static;
        padding: 2rem;
        text-align: center;
    }
}
</style>
@endsection