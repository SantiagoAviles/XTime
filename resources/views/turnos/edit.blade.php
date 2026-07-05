@extends('layouts.app')
@section('title', 'Editar turno · ' . $turno->nombre)
@section('content')
    <x-page-header :title="'Editar ' . $turno->nombre" badge="Turnos" />
    <div class="dashboard-card p-4">
        <form method="POST" action="{{ route('turnos.update', $turno) }}">
            @method('PUT')
            @include('turnos._form', ['turno' => $turno])
        </form>
    </div>
@endsection
