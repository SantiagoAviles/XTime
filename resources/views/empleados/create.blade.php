@extends('layouts.app')

@section('title', 'Nuevo empleado')
@section('section_label', 'Empleados')
@section('page_title', 'Registrar nuevo empleado')
@section('page_description', 'Completa los datos del personal. El DNI debe ser único en el sistema.')

@section('content')
    <div class="dashboard-card p-4">
        <form method="POST" action="{{ route('empleados.store') }}">
            @include('empleados._form', ['empleado' => null, 'areas' => $areas, 'roles' => $roles])
        </form>
    </div>
@endsection
