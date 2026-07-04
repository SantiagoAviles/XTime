@extends('layouts.app')

@section('title', 'Editar área')
@section('section_label', 'Áreas')
@section('page_title', 'Editar ' . $area->nombre)

@section('content')
    <div class="dashboard-card p-4">
        <form method="POST" action="{{ route('areas.update', $area) }}">
            @method('PUT')
            @include('areas._form', ['area' => $area])
        </form>
    </div>
@endsection
