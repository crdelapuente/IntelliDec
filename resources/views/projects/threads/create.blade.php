@extends('layouts.app')

@section('titulo', 'Creación de foro')

@section('cuerpo')

<h1 class="margen-derecha">Creación de foro</h1>

<div class="container">
    <div class="main-body p-0 bg-white rounded">
        <div class="inner-wrapper">

        @if(auth()->user()->isAdmin())
            <a class="btn btn-warning d-inline-block mt-2 align-self-start" style="margin-left: 0.7%;" href="{{ route('projects.threads.manage', $project->id) }}" role="button">Volver</a>
        @else
            <a class="btn btn-warning d-inline-block mt-2 align-self-start" style="margin-left: 0.7%;" href="{{ route('projects.threads.index', $project->id) }}" role="button">Volver</a>
        @endif
            <!-- Formulario creación de foro -->
            <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
            <form class="mx-1 mx-md-4" action="{{ route('projects.threads.store', $project->id) }}" method="POST">
                <!-- El token csrf caduca a las 2 horas -->
                @csrf
                @include('projects.threads.form-fields')
                    <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                        <button type="submit" class="btn btn-gold btn-lg">Crear foro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    @endsection