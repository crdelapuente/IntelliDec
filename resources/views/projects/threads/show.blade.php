@extends('layouts.app')

@section('titulo', 'Info del foro')

@section('cuerpo')

<h1 class="margen-derecha">Foro</h1>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css" integrity="sha256-46r060N2LrChLLb5zowXQ72/iKKNiw/lAmygmHExk/o=" crossorigin="anonymous" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<div class="container">
    <div class="main-body p-0 bg-white rounded">
        <div class="inner-wrapper">

            <div class="inner-sidebar">

                <!-- Botón de nueva discusión -->
                <div class="inner-sidebar-header justify-content-center">
                    <button class="btn btn-gold btn-icon-text my-2" type="button" data-toggle="modal" data-target="#threadModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus mr-2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Nuevo comentario
                    </button>
                </div>

            </div>

            <div class="inner-main">

                <!-- Discusiones -->
                <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">

                    <div class="card mb-2">
                        <div class="card-body p-2 p-sm-3">
                            <div class="media forum-item">
                                <div class="media-body">
                                    <h3><img src="https://bootdey.com/img/Content/avatar/avatar1.png" class="mr-3 rounded-circle" width="50" alt="User" /><a href="#" data-toggle="collapse" data-target=".forum-content" class="text-body text-decoration-none"> {{ $thread->title }}</a></h3>
                                    <p class="text-secondary">
                                        {{ $thread->content }}
                                    </p>
                                    <p class="text-muted"><a href="javascript:void(0)" class="text-dark-gold">{{ $thread->user->name }}</a> creado <span class="text-secondary font-weight-bold">el {{ $thread->created_at }}</span></p>
                                </div>
                                <div class="text-muted small text-center align-self-center">
                                    <span><i class="far fa-comment ml-2"></i>{{ $thread->posts()->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenido de la discusión -->
                <div class="inner-main-body p-2 p-sm-3 collapse forum-content">
                    <a href="#" class="btn btn-light btn-sm mb-3 has-icon" data-toggle="collapse" data-target=".forum-content"><i class="fa fa-arrow-left mr-2"></i>Atrás</a>
                    <h2>Comentarios</h2>
                    @if($posts->isEmpty())
                    <p>No hay comentarios aún.</p>
                    <button type="button" class="btn btn-gold" data-toggle="modal" data-target="#threadModal">
                        Añadir comentario
                    </button>
                    @else
                    @foreach($posts as $post)
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="media forum-item">
                                <a href="javascript:void(0)" class="card-link">
                                    <img src="https://bootdey.com/img/Content/avatar/avatar1.png" class="rounded-circle" width="50" alt="User" />
                                </a>
                                <div class="media-body ml-3">
                                    <a href="javascript:void(0)" class="text-secondary">{{ $post->user->name }}</a>
                                    <small class="text-muted ml-2">publicado el {{ $post->created_at }}</small>
                                    <div class="mt-3 font-size-sm">
                                        <p>
                                            {{ $post->content }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>

            </div>

        </div>

        <div class="modal fade" id="threadModal" tabindex="-1" role="dialog" aria-labelledby="threadModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('threads.posts.store', ['thread' => $thread->id]) }}">
                        @csrf
                        <div class="modal-header d-flex align-items-center bg-gold text-black">
                            <h6 class="modal-title mb-0" id="threadModalLabel">Nuevo comentario</h6>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="content">Nuevo comentario:</label>
                                <textarea id="content" name="content" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-gold">Publicar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">

</script>




@endsection