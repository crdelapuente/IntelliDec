@extends('layouts.app')

@section('titulo', 'Proyectos')

@section('cuerpo')


<h1 class="margen-derecha">Proyectos</h1>

@php
    $hasProject = false;
@endphp

<div class="container">
    <div class="main-body p-0 bg-white rounded">
        <div class="inner-wrapper">
            <!-- Proyectos -->
            <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
                @if (Auth::check())
                    <h2 class="mb-4">¡Hola, {{ Auth::user()->name }}! Aquí están tus proyectos:</h2>
                    <ul class="text-center">
                        @forelse($proyectos as $proyecto)
                            @if ($proyecto->users->contains(Auth::user()->id))
                            @php
                                $hasProject = true;
                            @endphp
                                <li>
                                    <a href="{{ route('projects.show', $proyecto) }}" class="btn btn-gold mb-3" id="btn-projects" role="button">
                                        <h1>{{ $proyecto->name }}</h1>
                                    </a>
                                </li>
                            @endif
                        @empty
                        <li>No hay proyectos aún.</li>
                        @endforelse
                    </ul>
                    @if (!$hasProject)
                        <p>No tienes proyectos asignados.</p>
                    @endif
                @else
                    <p>Por favor, inicie sesión para acceder a esta página.</p>
                @endif
            </div>
        </div>
    </div>
</div>


@endsection