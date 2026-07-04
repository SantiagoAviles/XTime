@extends('layouts.app')

@section('title', 'Nueva área')
@section('section_label', 'Áreas')
@section('page_title', 'Nueva área')

@section('content')
    <div class="dashboard-card p-4">
        <form method="POST" action="{{ route('areas.store') }}">
            @include('areas._form', ['area' => null])
        </form>
    </div>
@endsection
