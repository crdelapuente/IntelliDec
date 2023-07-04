@extends('layouts.app')

@section('titulo', 'Eliminación del proyecto')

@section('cuerpo')

<h1 class="margen-derecha">Eliminar el proyecto</h1>

<div class="container">
    <div class="main-body p-0 bg-white rounded">
        <div class="inner-wrapper">
            <!-- Proyectos -->
            <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
                <!-- Confirmación eliminación de proyecto -->
                <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
                    <form class="mx-1 mx-md-4" action="{{ route('projects.destroy', $proyecto) }}" method="POST">
                        <!-- El token csrf caduca a las 2 horas -->
                        @csrf
                        @method('DELETE')
                        <div class="text-center">
                            <h1>¿Estás seguro que quieres borrar el proyecto {{ $proyecto->titulo }}?</h1>
                            <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                                <a class="btn btn-secondary btn-lg mx-2 my-2" href="{{ route('projects.manage') }}" role="button">Cancelar</a>
                                <button type="submit" class="btn btn-danger btn-lg mx-2 my-2">Eliminar proyecto</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection