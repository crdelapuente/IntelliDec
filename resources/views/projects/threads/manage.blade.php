@extends('layouts.app')

@section('titulo', 'Gestion de foros')

@section('cuerpo')

<h1 class="margen-derecha">Gestión de foros</h1>

<div class="container">
    <div class="main-body p-0 bg-white rounded">
        <div class="inner-wrapper">
            <div class="inner-sidebar">
                <!-- Botón de nuevo foro -->
                <div class="inner-sidebar-header justify-content-center">
                    <button class="btn btn-gold btn-icon-text my-2" onclick="location.href='{{ route('projects.threads.create', $project->id) }}'" style="display: flex; align-items: center;">
                        <img src="{{ asset('imagenes/new.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                        <span class="text">NUEVO FORO</span>
                    </button>
                </div>
            </div>

            <div class="inner-main">
                <!-- Foros -->
                <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
                    <ul class="list-unstyled">
                        @forelse($threads as $thread)
                        <li class="project-item">
                            <div class="project-details">
                                <h2>{{ $thread->title }}</h2>
                                <small class="text-muted">Autor: {{ $thread->user->name }}</small>
                            </div>
                            <div class="project-actions">
                                <a href="{{ route('threads.show', $thread) }}" class="project-action">
                                    <img src="{{ asset('imagenes/info.svg') }}" class="bi d-block mx-auto mb-1" id="icono" width="36" height="36" alt="Icono de info">
                                </a>
                                <a href="{{ route('projects.threads.delete', ['project' => $project, 'thread' => $thread]) }}" class="project-action">
                                    <img src="{{ asset('imagenes/delete.svg') }}" class="bi d-block mx-auto mb-1" id="icono" width="42" height="42" alt="Icono de borrar">
                                </a>
                            </div>
                        </li>
                        @empty
                        <li>No hay foros aún.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
