@extends('layouts.app')

@section('title', 'Editar empleado')
@section('section_label', 'Empleados')
@section('page_title', 'Editar ' . $empleado->nombreCompleto())
@section('page_description', 'Actualiza la información del empleado. El historial se conserva.')

@section('content')
    <div class="dashboard-card p-4">
        <form method="POST" action="{{ route('empleados.update', $empleado) }}">
            @method('PUT')
            @include('empleados._form', ['empleado' => $empleado, 'areas' => $areas, 'roles' => $roles])
        </form>
    </div>
@endsection
