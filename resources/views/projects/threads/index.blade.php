@extends('layouts.app')

@section('titulo', 'Foro')

@section('cuerpo')


<h1 class="margen-derecha">Foro</h1>



<div class="container">
    <div class="main-body p-0 bg-white rounded">
        <div class="inner-wrapper">

            <div class="inner-sidebar">
                <!-- Barra de botones -->
                <a class="btn btn-warning d-inline-block mt-2 ml-2 my-2 align-self-start" href="{{ route('projects.index') }}" role="button">Volver</a>
                <div class="inner-sidebar-header d-flex flex-column">
                    <a href="{{ route('projects.vote', $project->id) }}" class="btn btn-gold btn-icon-text my-2 {{ Route::currentRouteName() == 'projects.vote' ? 'active' : '' }}">
                        <img src="{{ asset('imagenes/vote.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                        <span class="text">Votar</span>
                    </a>

                    <a href="{{ route('projects.threads.index', $project->id) }}" class="btn btn-gold btn-icon-text my-2 {{ Route::currentRouteName() == 'projects.threads.index' ? 'active' : '' }}">
                        <img src="{{ asset('imagenes/forum.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                        <span class="text">Foro</span>
                    </a>

                    <a href="{{ route('projects.monitoring', $project->id) }}" class="btn btn-gold btn-icon-text my-2 {{ Route::currentRouteName() == 'projects.monitoring' ? 'active' : '' }}">
                        <img src="{{ asset('imagenes/monitoring.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                        <span class="text">Seguimiento</span>
                    </a>

                </div>

            </div>

            <!-- Foros -->
            <div class="inner-main" id="project">
                @if (Auth::check())
                <h2 class="mb-4">Foros del proyecto {{ $project->name }}</h2>
                <a href="{{ route('projects.threads.create', $project->id) }}" class="btn btn-success d-inline-block mb-2 align-self-end" id="creaForo">Crear foro</a>
                <ul class="forum-list">
                    @if($threads->isEmpty())
                    <h3>No hay foro todavía.</h3>
                    @else
                    @forelse($threads as $thread)
                    <li class="forum-item">
                        <a href="{{ route('threads.show', $thread) }}" class="forum-link">
                            <div class="forum-content">
                                <h1 class="forum-title">{{ $thread->title }}</h1>
                                <span class="forum-author">Autor: {{ $thread->user->name }}</span>
                            </div>
                        </a>
                    </li>
                    @empty
                    <li>No hay foros aún.</li>
                    @endforelse
                    @endif
                </ul>
                @else
                <p>Por favor, inicie sesión para acceder a esta página.</p>
                @endif
            </div>
        </div>
    </div>
</div>


@endsection