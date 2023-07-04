<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'IntelliDec') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('sass/app.scss') }}">

    @vite(['resources/js/app.js'])

</head>

<body>
    <div id="app">

        <div class="px-3 py-2 text-bg-dark">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                    <a href="<?= route('inicio') ?>" class="navbar-brand d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white text-decoration-none">
                        <img src="{{ asset('imagenes/LogoDoradoSinFondo.png') }}" id="icono" width="50" height="50" alt="Logo">
                        <img src="{{ asset('imagenes/NombreDoradoSinFondo.png') }}" id="icono" width="25%" height="auto" alt="Nombre">
                    </a>

                    <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
                        <li>
                            <a href="{{ route('inicio') }}" class="nav-link text-white">
                                <!-- <svg class="bi d-block mx-auto mb-1" width="24" height="24"><use xlink:href="/imagenes/inicio.svg"></use></svg> -->
                                <img src="{{ asset('imagenes/home.svg') }}" class="bi d-block mx-auto mb-1" id="icono" width="24" height="24" alt="Icono de inicio">
                                Inicio
                            </a>
                        </li>
                        @guest
                        <li>
                            <a href="{{ route('login') }}" class="nav-link text-white">
                                <img src="{{ asset('imagenes/profile.svg') }}" class="bi d-block mx-auto mb-1" id="icono" width="24" height="24" alt="Icono de login">
                                Inicia sesión
                            </a>
                        </li>
                        @else
                        @if(auth()->user()->isAdmin())
                        <li>
                            <a href="{{ route('projects.manage') }}" class="nav-link text-white">
                                <img src="{{ asset('imagenes/admin-project.svg') }}" class="bi d-block mx-auto mb-1" id="icono" width="24" height="24" alt="Icono de proyectos">
                                Gestión proyectos
                            </a>
                        </li>
                        @else
                        <li>
                            <a href="{{ route('projects.index') }}" class="nav-link text-white">
                                <img src="{{ asset('imagenes/project.svg') }}" class="bi d-block mx-auto mb-1" id="icono" width="24" height="24" alt="Icono de proyectos">
                                Proyectos
                            </a>
                        </li>
                        @endif
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="btn btn-gold mb-3 mt-2">Salir</button>
                            </form>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </div>

        <section style="min-height: 100vh; background-color: #eee;">
            <div style="padding-bottom: 3%;">
                @if (session('status'))
                <div class="alert alert-success text-center" style="font-size: 32px;">
                    {{ session('status') }}
                </div>
                @endif

                <!-- @if ($errors->any())
        <div class="alert alert-danger text-center" style="font-size: 32px;">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif -->

                @yield('cuerpo')
            </div>
        </section>

    </div>
</body>

</html>