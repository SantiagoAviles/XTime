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
                {{-- Marca + toggle sidebar --}}
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
                    <a class="navbar-brand fw-semibold mb-0" href="{{ route('dashboard') }}" style="color: #0f4c5c;">
                        {{ config('app.name') }}
                    </a>
                </div>

                {{-- Usuario + logout --}}
                <div class="d-flex align-items-center gap-3">
                    @auth
                        <div class="d-none d-sm-flex flex-column align-items-end lh-sm">
                            <span class="fw-semibold" style="font-size: 0.875rem; color: #173042;">
                                {{ Auth::user()->name }}
                            </span>
                            <span class="text-muted" style="font-size: 0.75rem;">
                                {{ Auth::user()->roles()->first()?->name ?? 'Sin rol' }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="mb-0">
                            @csrf
                            <button
                                type="submit"
                                class="btn btn-sm btn-outline-secondary"
                                style="font-size: 0.8rem;"
                            >
                                Cerrar sesión
                            </button>
                        </form>
                    @endauth
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

                        {{-- Mensajes flash --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                            </div>
                        @endif

                        @if (session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                                {{ session('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
        </div>
    </body>
</html>
