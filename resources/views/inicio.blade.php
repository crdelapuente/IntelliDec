@extends('layouts.app')

@section('titulo', 'Inicio')

@section('cuerpo')

    <div id="Inicio" class="d-flex flex-column justify-content-center align-items-center">
        <img src="{{ asset('imagenes/LogoDorado.png') }}" class="bi d-block mx-auto mb-3" id="icono" width="auto" height="450px" alt="Imagen logo y nombre de la plataforma" style="border-bottom-right-radius: 10px; border-bottom-left-radius: 10px;">
        <h1>¿Qué es IntelliDec?</h1>
        <h4 class="mx-5">IntelliDec es una plataforma de toma de decisiones online. </h4>
        <p>Gracias a esto, los expertos pueden debatir y llegar a tomar una decisión de forma remota y asíncrona.</p>

        <h3>¿Quieres probarla?</h3>
        <div class="d-grid gap-2 col-auto mx-auto">
            <a href="<?= route('login') ?>" class="btn btn-gold" role="button">¡Inicia sesión o regístrate!</a>
        </div>
    </div>
    

@endsection