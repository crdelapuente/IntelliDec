@extends('layouts.app')

@section('titulo', 'Gestión de proyectos')

@section('cuerpo')

<h1 class="margen-derecha">Gestión de proyectos</h1>

<div class="container">
    <div class="main-body p-0 bg-white rounded">
        <div class="inner-wrapper">
            <div class="inner-sidebar">
                <!-- Botón de nuevo proyecto -->
                <div class="inner-sidebar-header justify-content-center">
                    <button class="btn btn-gold btn-icon-text my-2" onclick="location.href='{{ route('projects.create') }}'" style="display: flex; align-items: center;">
                        <img src="{{ asset('imagenes/new.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                        <span class="text">NUEVO PROYECTO</span>
                    </button>
                </div>
            </div>

            <div class="inner-main">
                <!-- Proyectos -->
                <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
                    <ul class="list-unstyled">
                        @forelse($proyectos as $proyecto)
                        <li class="project-item">
                            <div class="project-details">
                                <h2>{{ $proyecto->name }}</h2>
                            </div>
                            <div class="project-actions">
                                <a href="{{ route('projects.monitoring', $proyecto->id) }}" class="project-action">
                                    <img src="{{ asset('imagenes/info.svg') }}" class="bi d-block mx-auto mb-1" id="icono" width="36" height="36" alt="Icono de info">
                                </a>
                                <a href="{{ route('projects.threads.manage', $proyecto->id) }}" class="project-action">
                                    <img src="{{ asset('imagenes/thread.svg') }}" class="bi d-block mx-auto mb-1" id="icono" width="38" height="38" alt="Icono de foro">
                                </a>
                                <a href="{{ route('projects.edit', $proyecto) }}" class="project-action">
                                    <img src="{{ asset('imagenes/edit.svg') }}" class="bi d-block mx-auto mb-1" id="icono" width="42" height="42" alt="Icono de editar">
                                </a>
                                <a href="{{ route('projects.delete', $proyecto) }}" class="project-action">
                                    <img src="{{ asset('imagenes/delete.svg') }}" class="bi d-block mx-auto mb-1" id="icono" width="42" height="42" alt="Icono de borrar">
                                </a>
                            </div>
                        </li>
                        @empty
                        <li>No hay proyectos aún.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
