@extends('layouts.app')

@section('titulo', 'Votación del proyecto')

@section('cuerpo')

<h1 class="margen-derecha">Votación del proyecto</h1>

<div class="container">
    <div class="main-body p-0 bg-white rounded">
        <div class="inner-wrapper">

            <div class="inner-sidebar">
                <!-- Barra de botones -->
                <a class="btn btn-warning d-inline-block mt-2 ml-2 my-2 align-self-start" href="{{ route('projects.index') }}" role="button">Volver</a>
                <div class="inner-sidebar-header d-flex flex-column">
                    <a href="{{ route('projects.vote', $proyecto->id) }}" class="btn btn-gold btn-icon-text my-2 {{ Route::currentRouteName() == 'projects.vote' ? 'active' : '' }}">
                        <img src="{{ asset('imagenes/vote.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                        <span class="text">Votar</span>
                    </a>

                    <a href="{{ route('projects.threads.index', $proyecto->id) }}" class="btn btn-gold btn-icon-text my-2 {{ Route::currentRouteName() == 'projects.threads.index' ? 'active' : '' }}">
                        <img src="{{ asset('imagenes/forum.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                        <span class="text">Foro</span>
                    </a>

                    <a href="{{ route('projects.monitoring', $proyecto->id) }}" class="btn btn-gold btn-icon-text my-2 {{ Route::currentRouteName() == 'projects.monitoring' ? 'active' : '' }}">
                        <img src="{{ asset('imagenes/monitoring.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                        <span class="text">Seguimiento</span>
                    </a>

                </div>

            </div>

            <div class="inner-main" id="project">
                <h1>Elige el nivel de preferencia entre criterios</h1>
                <!-- votación del proyecto -->
                <form method="POST" action="{{ route('vote.store', $proyecto->id) }}">
                    @csrf
                    @if($criterios)
                        @foreach($criterios as $i => $ci)
                        @foreach($criterios as $j => $cj)
                        @if($i != $j)
                        <div>
                            <p>¿Qué nivel de preferencia tienes de {{ $ci }} con respecto a {{ $cj }}?</p>
                            @php
                            $prefs = ['1 (Nada)' => 1, '2 (Poco)' => 2, '3 (Igual)' => 3, '4 (Mayor)' => 4, '5 (Mucho)' => 5];
                            @endphp

                            @foreach($prefs as $prefText => $prefValue)
                            <div style="display: inline-block; text-align: center; margin-right: 10px; margin-bottom: 2%;">
                                @php
                                $radioId = "pref_{$i}_{$j}_{$prefValue}";
                                $radioName = "pref[{$ci}][{$cj}]";
                                $claveRespuesta = "{$ci}-{$cj}";
                                $respuestaPrevias = isset($respuestasPrevias[$claveRespuesta]) ? $respuestasPrevias[$claveRespuesta] : null;
                                $isChecked = $respuestaPrevias === $prefValue;
                                @endphp
                                <input type="radio" id="{{ $radioId }}" name="{{ $radioName }}" value="{{ $prefValue }}" {{ $isChecked ? 'checked' : '' }}>
                                <label for="{{ $radioId }}">{{ $prefText }}</label>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @endforeach
                        @endforeach
                        <button class="btn btn-gold d-block mx-auto mt-2" id="boton_vote" type="submit">Enviar votación</button>
                    @else
                        <h3>El proyecto seleccionado no tiene criterios sobre los que votar</h3>
                    @endif

                </form>
            </div>
        </div>
    </div>
</div>

@endsection