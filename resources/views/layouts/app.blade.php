<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name'))</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <nav class="navbar navbar-expand-lg app-navbar sticky-top">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center gap-3">
                    <button
                        class="btn btn-outline-secondary d-lg-none"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#appSidebar"
                        aria-controls="appSidebar"
                        aria-expanded="false"
                        aria-label="Mostrar navegacion"
                    >
                        Menu
                    </button>
                    <a class="navbar-brand fw-semibold text-dark mb-0" href="{{ route('dashboard') }}">
                        {{ config('app.name') }}
                    </a>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="badge text-bg-light border">Laravel {{ app()->version() }}</span>
                    <span class="badge text-bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                        Base inicial
                    </span>
                </div>
            </div>
        </nav>

        <div class="container-fluid px-0">
            <div class="row g-0">
                <aside class="col-12 col-lg-3 col-xl-2 collapse d-lg-block app-sidebar" id="appSidebar">
                    @include('layouts.partials.sidebar')
                </aside>

                <main class="col-12 col-lg-9 col-xl-10 content-shell">
                    <div class="p-4 p-lg-5">
                        @include('layouts.partials.navbar')
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
