@extends('layouts.app')

@section('titulo', 'Login')

@section('cuerpo')
<div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-xl-10">
            <div class="card rounded-3 text-black">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <div class="card-body p-md-5 mx-md-4">

                            <div class="text-center">
                                <img src="{{ asset('imagenes/LogoDoradoSinFondo.png') }}" style="width: 185px;" alt="logo">
                                <h4 class="mt-1 mb-5 pb-1">Somos IntelliDec</h4>
                            </div>

                            <form class="mx-1 mx-md-4" action="{{ route('login') }}" method="POST">
                                @csrf
                                <p>Inicia sesión con tu cuenta</p>

                                <div class="form-outline mb-4">
                                    <label class="form-label" for="form2Example11">
                                        <h3>Correo</h3>
                                    </label>
                                    <input type="email" name="email" class="form-control" placeholder="Introduce tu correo electrónico" />
                                </div>

                                <div class="form-outline mb-4">
                                    <label class="form-label" for="form2Example22">
                                        <h3>Contraseña</h3>
                                    </label>
                                    <input type="password" name="password" class="form-control" placeholder="Introduce tu contraseña" />
                                </div>

                                <div class="form-outline mb-4">
                                    <label class="flex items-center">
                                        <input name="remember" type="checkbox">
                                        <span class="cursor-pointer ml-2">Recuérdame</span>
                                    </label>
                                </div>

                                <div class="text-center pt-1 mb-5 pb-1">
                                    <button type="submit" class="btn btn-block fa-lg gradient-custom-2 mb-3">Iniciar sesión</button>
                                </div>

                                <div class="d-flex align-items-center justify-content-center pb-4">
                                    <p class="mb-0 me-2">¿Aún no tienes cuenta?</p>
                                    <a class="btn btn-outline-warning" href="{{ route('register') }}" role="button">Regístrate</a>
                                </div>

                            </form>

                        </div>
                    </div>
                    <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                        <div class="text-black px-3 py-4 p-md-5 mx-md-4">
                            <h2 class="mb-4">Somos más que formularios</h2>
                            <p class="mb-0">Nuestra misión es hacer la toma de decisiones una labor sencilla, intuitiva y elegante.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Diseño basado de: https://mdbootstrap.com/docs/standard/extended/login/ -->

@endsection