@extends('layouts.app')

@section('titulo', 'Edición del proyecto')

@section('cuerpo')

<h1 class="margen-derecha">Editar el proyecto</h1>

<div class="container">
    <div class="main-body p-0 bg-white rounded">
        <div class="inner-wrapper">
            <!-- Proyectos -->
            <a class="btn btn-warning d-inline-block mt-2 align-self-start" style="margin-left: 0.7%;" href="{{ route('projects.manage') }}" role="button">Volver</a>
                <!-- Formulario edición de proyecto -->
                <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
                    <form class="mx-1 mx-md-4" action="{{ route('projects.update', $project) }}" method="POST">
                        <!-- El token csrf caduca a las 2 horas -->
                        @csrf @method('PATCH')
                        @include('projects.form-fields')

                        <!-- Lista a los usuarios -->
                        <h2 class="mb-1">Usuarios pertenecientes al proyecto</h2>
                        @foreach($users as $user)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{ $user->id }}" id="userCheck{{ $user->id }}" name="user_id[]"
                                @if($project->users->contains($user->id)) checked @endif>
                                <label class="form-check-label" for="userCheck{{ $user->id }}">
                                    {{ $user->name }}
                                </label>
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                            <button type="submit" class="btn btn-danger btn-lg">Actualizar proyecto</button>
                        </div>
                    </form>
                </div>
        </div>
    </div>
</div>

@endsection