@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Agendar Cita</h1>
    
    <form action="{{ route('appointments.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="document_type">Tipo de Documento:</label>
            <select class="form-control" id="document_type" name="document_type" required>
                <option value="11">Registro civil</option>
                <option value="12">Tarjeta de identidad</option>
                <option value="13">Cédula de ciudadanía</option>
                <option value="22">Cédula de extranjería</option>
                <option value="41">Pasaporte</option>
            </select>
        </div>

        <div class="form-group">
            <label for="document_number">Número de Documento:</label>
            <input type="text" class="form-control" id="document_number" name="document_number" pattern="\d{1,11}" title="Solo se permiten números hasta 11 caracteres" required>
        </div>

        <div class="form-group">
            <label for="name">Nombre Completo:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="phone">Teléfono:</label>
            <input type="text" class="form-control" id="phone" name="phone" required>
        </div>

        <div class="form-group">
            <label for="date">Fecha de la Cita:</label>
            <input type="date" class="form-control" id="date" name="date" value="{{ request()->get('date') }}" required>
        </div>

        <div class="form-group">
            <label for="hour">Hora de la Cita:</label>
            <select class="form-control" id="hour" name="hour" required>
                <option value="">Seleccionar hora</option>
                <option value="08:00">08:00 AM</option>
                <option value="08:15">08:15 AM</option>
                <option value="08:30">08:30 AM</option>
                <option value="08:45">08:45 AM</option>
                <option value="09:00">09:00 AM</option>
                <option value="09:15">09:15 AM</option>
                <option value="09:30">09:30 AM</option>
                <option value="09:45">09:45 AM</option>
                <option value="10:00">10:00 AM</option>
                <option value="10:15">10:15 AM</option>
                <option value="10:30">10:30 AM</option>
                <option value="10:45">10:45 AM</option>
                <option value="11:00">11:00 AM</option>
                <!-- más opciones de horas -->
            </select>
        </div>

        <div class="form-group">
            <label for="employee">Funcionario Asignado:</label>
            <select class="form-control" id="employee" name="employee" required>
                <option value="">Seleccionar funcionario</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Agendar Cita</button>
    </form>
</div>
@endsection
