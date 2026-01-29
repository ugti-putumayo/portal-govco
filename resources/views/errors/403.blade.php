@extends('layouts.app')

@section('minimal', true)

@section('title', '403 | Acceso denegado')

@section('content')
  <div style="display:grid;place-items:center;background:var(--govco-gray-menu);min-height:100dvh;padding:24px;">
    <div style="width:min(700px,90vw);background:var(--govco-white-color);border-radius:var(--govco-border-radius);
                box-shadow:var(--govco-box-shadow);border:1px solid rgba(0,0,0,.06);padding:2.5rem;text-align:center">
      <div style="height:4px;border-radius:999px;margin-bottom:1.5rem;
                  background:linear-gradient(90deg,var(--govco-secondary-color),
                  var(--govco-third-color) 35%,var(--govco-primary-color) 70%,var(--govco-fourth-color));"></div>

      <img src="{{ asset('img/errors/403_access_denied.svg') }}" alt="Acceso denegado"
           style="width:180px;height:auto;filter:drop-shadow(0 3px 6px rgba(0,0,0,.08));margin-bottom:1rem">

      <p style="font-size:clamp(48px,8vw,72px);font-weight:800;color:var(--govco-secondary-color);margin:0;">403</p>
      <h1 style="font-size:1.5rem;font-weight:700;color:var(--govco-tertiary-color);margin:.25rem 0 0.5rem;">
        Acceso denegado
      </h1>
      <p style="color:var(--govco-tertiary-color);font-family:var(--govco-font-secondary);margin:0 0 1.5rem;">
        No tienes permisos para realizar esta acción.
        @if(isset($exception) && $exception->getMessage())
          <br><small style="opacity:.7">{{ $exception->getMessage() }}</small>
        @endif
      </p>

      <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap">
        <a href="{{ url()->previous() }}"
           style="padding:.6rem 1.2rem;border-radius:8px;border:1px solid var(--govco-secondary-color);
                  color:var(--govco-secondary-color);text-decoration:none;">
          Volver
        </a>
        @auth
          <a href="{{ route('dashboard') }}"
             style="padding:.6rem 1.2rem;border-radius:8px;background:var(--govco-secondary-color);
                    color:var(--govco-white-color);text-decoration:none;border:1px solid var(--govco-secondary-color);">
            Ir al dashboard
          </a>
        @else
          <a href="{{ route('login') }}"
             style="padding:.6rem 1.2rem;border-radius:8px;background:var(--govco-secondary-color);
                    color:var(--govco-white-color);text-decoration:none;border:1px solid var(--govco-secondary-color);">
            Iniciar sesión
          </a>
        @endauth
      </div>

      @auth
        <p style="margin-top:1rem;font-size:.8rem;color:var(--govco-border-color)">Usuario: {{ auth()->user()->name }}</p>
      @endauth
    </div>
  </div>
@endsection