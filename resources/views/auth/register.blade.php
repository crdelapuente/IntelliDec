@extends('layouts.app')

@section('titulo', 'Registro')

@section('cuerpo')

<div class="container">
    <div class="main-body p-0 bg-white">
        <div class="inner-wrapper">
            <!-- Proyectos -->
            <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">

                <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Registro</p>

                <div class="mx-auto w-50">

                    <form class="mx-1 mx-md-4" action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="d-flex flex-column mb-3">
                            <div class="form-outline flex-fill mb-0 d-flex align-items-center">
                                <img src="{{ asset('imagenes/username.svg') }}" class="bi icon me-3" id="icono" width="24" height="24" alt="Icono de foro" style="margin-left: 10px;">
                                <input name="username" type="text" placeholder="Nombre de usuario" class="form-control" autofocus="autofocus" value="{{ old('username') }}" />
                            </div>
                            @error('username')
                            <small style="color: red">* El campo nombre de usuario es obligatorio</small>
                            @enderror
                        </div>

                        <div class="d-flex flex-column mb-3">
                            <div class="form-outline flex-fill mb-0 d-flex align-items-center">
                                <img src="{{ asset('imagenes/name.svg') }}" class="bi icon me-3" id="icono" width="24" height="24" alt="Icono de foro" style="margin-left: 10px;">
                                <input name="name" type="text" placeholder="Nombre completo" class="form-control" value="{{ old('name') }}" />
                            </div>
                            @error('name')
                            <small style="color: red">* El campo nombre es obligatorio</small>
                            @enderror
                        </div>

                        <div class="d-flex flex-column mb-3">
                            <div class="form-outline flex-fill mb-0 d-flex align-items-center">
                                <img src="{{ asset('imagenes/email.svg') }}" class="bi icon me-3" id="icono" width="24" height="24" alt="Icono de foro" style="margin-left: 10px;">
                                <input name="email" type="email" placeholder="Email" class="form-control" value="{{ old('email') }}" />
                            </div>
                            @error('email')
                            <small style="color: red">* El campo email es obligatorio</small>
                            @enderror
                        </div>

                        <div class="d-flex flex-column mb-3">
                            <div class="form-outline flex-fill mb-0 d-flex align-items-center">
                                <img src="{{ asset('imagenes/password.svg') }}" class="bi icon me-3" id="icono" width="24" height="24" alt="Icono de foro" style="margin-left: 10px;">
                                <input name="password" type="password" placeholder="Contraseña" class="form-control" />
                            </div>
                            @error('password')
                            <small style="color: red">* El campo contraseña es obligatorio</small>
                            @enderror
                        </div>

                        <div class="d-flex flex-column mb-3">
                            <div class="form-outline flex-fill mb-0 d-flex align-items-center">
                                <img src="{{ asset('imagenes/password.svg') }}" class="bi icon me-3" id="icono" width="24" height="24" alt="Icono de foro" style="margin-left: 10px;">
                                <input name="password_confirmation" type="password" placeholder="Repite la contraseña" class="form-control" />
                            </div>
                            @error('password_confirmation')
                            <small style="color: red">* El campo confirmación de contraseña es obligatorio</small>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                            <button type="submit" class="btn btn-gold btn-lg">Registrarse</button>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>
</div>



@endsection