@extends('layouts.app')
@section('title', 'Nuevo turno')
@section('content')
    <x-page-header title="Nuevo turno" badge="Turnos" />
    <div class="dashboard-card p-4">
        <form method="POST" action="{{ route('turnos.store') }}">
            @include('turnos._form', ['turno' => null])
        </form>
    </div>
@endsection
