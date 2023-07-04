@extends('layouts.app')

@section('titulo', 'Info del proyecto')

@section('cuerpo')

    <h1 class="margen-derecha">Información del proyecto</h1>

    <div class="container">
        <div class="main-body p-0 bg-white rounded">
            <div class="inner-wrapper">

                <div class="inner-sidebar">
                    <!-- Barra de botones -->
                    @if(auth()->user()->isAdmin())
                    <a class="btn btn-warning d-inline-block mt-2 ml-2 my-2 align-self-start" href="{{ route('projects.manage') }}" role="button">Volver</a>
                    @else
                        <a class="btn btn-warning d-inline-block mt-2 ml-2 my-2 align-self-start" href="{{ route('projects.index') }}" role="button">Volver</a>
                    @endif
                    <div class="inner-sidebar-header d-flex flex-column">
                        <a href="{{ route('projects.vote', $proyecto->id) }}" class="btn btn-gold btn-icon-text my-2">
                            <img src="{{ asset('imagenes/vote.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                            <span class="text">Votar</span>
                        </a>

                        <a href="{{ route('projects.threads.index', $proyecto->id) }}" class="btn btn-gold btn-icon-text my-2">
                            <img src="{{ asset('imagenes/forum.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                            <span class="text">Foro</span>
                        </a>

                        <a href="{{ route('projects.monitoring', $proyecto->id) }}" class="btn btn-gold btn-icon-text my-2">
                            <img src="{{ asset('imagenes/monitoring.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                            <span class="text">Seguimiento</span>
                        </a>

                    </div>

                </div>

                <div class="inner-main">

                    <!-- Información del proyecto -->
                    <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
                        <h1 class="mb-4">{{ $proyecto->name }}</h1>
                        Descripción:
                        <h3 class="mb-4">{{ $proyecto->description }}</h3>
                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection