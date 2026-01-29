@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                @yield('sidebar')
            </div>
            <div class="col-md-8">
                @yield('main-content')
            </div>
        </div>
    </div>
@endsection
