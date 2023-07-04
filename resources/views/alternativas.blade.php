@extends('layouts.app')

@section('titulo', 'Alternativas')

@section('cuerpo')

    <h1 class="margen-derecha">Alternativas</h1>

    <ul>
        @forelse($alternativas as $alternativa)
            <li> {{ $alternativa['titulo'] }} </li>
        @empty
            <li> No hay alternativas aún. </li>
        @endforelse
    </ul>

@endsection