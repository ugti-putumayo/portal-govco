@extends('layouts.app')
@section('content')
    <h1>Noticias</h1>
    <form method="GET" action="{{ route('publications') }}">
        <input type="text" name="search" placeholder="Buscar..." value="{{ request()->search }}">
        <select name="type">
            <option value="">Todos los tipos</option>
            @foreach($types as $type)
                <option value="{{ $type->id }}" {{ request()->type == $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>
        <button type="submit">Filtrar</button>
    </form>
    <x-public.home.news :publications="$publications" />
    {{ $publications->links() }}
@endsection