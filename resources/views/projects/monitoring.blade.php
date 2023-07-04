@extends('layouts.app')

@section('titulo', 'Seguimiento del proyecto')

@section('cuerpo')

<h1 class="margen-derecha">Seguimiento del proyecto</h1>

<div class="container">
    <div class="main-body p-0 bg-white rounded">
        <div class="inner-wrapper">

            @if(auth()->user()->isAdmin())

            <div class="inner-main-body p-2 collapse forum-content show">
                <a class="btn btn-warning d-inline-block ml-2 mb-2 align-self-start" href="{{ route('projects.manage') }}" role="button">Volver</a>
                <!-- seguimiento del proyecto -->
                <!-- <h1>GDD: </h1>[{{ $GDD }}]
                <h1>GNDD: </h1>[{{ $GNDD }}] -->
                <h1>Consenso: </h1>
                @php
                $consenso = App\Models\RelProject::where('project_id', $id_project)->first();
                @endphp
                @if($consenso->consenso != 0)
                <p>El consenso obtenido es <strong>{{ $numberToLabel($global) }}</strong></p>
                @else
                <p>Aún no se ha calculado el consenso</p>
                @endif
                <h1>Inconsistencia:</h1>
                @if(!empty($valores))
                @foreach($valores as $userId => $valor)
                <p>Las respuestas de <strong>{{ $userId }}</strong> tienen un nivel de inconsistencia <strong>{{ $numberToLabel($valor) }}</strong>.</p>
                @endforeach
                @else
                <p>No se ha calculado aún la inconsistencia.</p>
                @endif
                <h1>Distancia: </h1>
                @if(!empty($distancia))
                @foreach($distancia as $userId => $userDistance)
                @php
                // Buscar el user_id en la tabla rel_users
                $userIdInUsersTable = DB::table('rel_users')->where('id', $userId)->value('user_id');
                $userName = DB::table('users')->where('id', $userIdInUsersTable)->value('name');
                @endphp
                <p>Las respuestas de <strong>{{ $userName }}</strong> tienen una distancia <strong>{{ $numberToLabelA($userDistance) }}</strong> con respecto a la media de las opiniones.</p>
                @endforeach
                @else
                <p>No se han calculado aún las distancias.</p>
                @endif
                <h1>Top alternativas:</h1>
                @if ($scores == 0)
                <p>Todavía no se pueden estimar las alternativas</p>
                @else
                <ul>
                    <div class="export-button-container">
                        <button class="btn btn-success d-inline-block mb-2" onclick="location.href='{{ route('projects.export', $project) }}'">
                            <img src="{{ asset('imagenes/excel.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                            <span class="text">Exportar a Excel</span>
                        </button>
                    </div>
                    @if ($id_project == 1)
                    @foreach (array_slice($scores, 0, 5) as $id => $score)
                    <li>
                        <h1><strong>{{ $id + 1 }}.</strong></h1>
                        <p><strong>Id:</strong> {{ $score['id'] }},
                        <strong>Descripción:</strong> {{ $score['description'] }}</p>
                        <strong>Puntuación pantalla:</strong> {{ $score['screen_score'] }}<br>
                        <strong>Puntuación procesador:</strong> {{ $score['cpu_score'] }}<br>
                        <strong>Puntuación almacenamiento:</strong> {{ $score['storage_score'] }}<br>
                        <strong>Puntuación precio:</strong> {{ $score['price_score'] }}<br>
                        <br>
                        <strong>Similitud:</strong> {{ $score['similarity_value'] }}.
                    </li>
                    @if (!$loop->last)
                    <br>
                    @endif
                    @endforeach
                    @else
                    @foreach (array_slice($scores, 0, 5) as $index => $item_score)
                    <li>
                        <h1><strong>{{ $index + 1 }}.</strong></h1>
                        <p><strong>Id:</strong> {{ $item_score['id'] }},
                            <strong>Descripción:</strong> {{ $item_score['scores']['descripcion'] }}
                        </p>
                        @foreach ($item_score['scores'] as $criteria => $score)
                        @if ($criteria != 'descripcion') <!-- Ignoramos la descripción, ya que se muestra por separado -->
                        <strong>Puntuación {{ $criteria }}:</strong> {{ $score }}<br>
                        @endif
                        @endforeach
                        <br>
                        <strong>Similitud:</strong> {{ $item_score['similarity_value'] }}.
                    </li>
                    @if (!$loop->last)
                    <br>
                    @endif
                    @endforeach

                    @endif
                </ul>
                @endif
            </div>

            @else


            <div class="inner-sidebar">
                <!-- Barra de botones -->
                <a class="btn btn-warning d-inline-block mt-2 ml-2 my-2 align-self-start" href="{{ route('projects.index') }}" role="button">Volver</a>
                <div class="inner-sidebar-header d-flex flex-column">
                <a href="{{ route('projects.vote', $project->id) }}" class="btn btn-gold btn-icon-text my-2 {{ Route::currentRouteName() == 'projects.vote' ? 'active' : '' }}">
                        <img src="{{ asset('imagenes/vote.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                        <span class="text">Votar</span>
                    </a>

                    <a href="{{ route('projects.threads.index', $project->id) }}" class="btn btn-gold btn-icon-text my-2 {{ Route::currentRouteName() == 'projects.threads.index' ? 'active' : '' }}">
                        <img src="{{ asset('imagenes/forum.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                        <span class="text">Foro</span>
                    </a>

                    <a href="{{ route('projects.monitoring', $project->id) }}" class="btn btn-gold btn-icon-text my-2 {{ Route::currentRouteName() == 'projects.monitoring' ? 'active' : '' }}">
                        <img src="{{ asset('imagenes/monitoring.svg') }}" class="bi icon" id="icono" width="24" height="24" alt="Icono de foro">
                        <span class="text">Seguimiento</span>
                    </a>

                </div>

            </div>

            <div class="inner-main" id="project">
                <!-- seguimiento del proyecto -->
                <h1>Top alternativas:</h1>
                @if ($id_project == 1)
                <ul>
                    @foreach (array_slice($scores, 0, 5) as $id => $score)
                    <li>
                        <strong>{{ $id + 1 }}.</strong> {{ $score['description'] }}
                    </li>
                    @if (!$loop->last)
                    <br>
                    @endif
                    @endforeach
                </ul>
                @else
                <ul>
                    @foreach (array_slice($scores, 0, 5) as $id => $item_score)
                    <li>
                        <strong>{{ $id + 1 }}.</strong> {{ $item_score['description'] }}
                    </li>
                    @if (!$loop->last)
                    <br>
                    @endif
                    @endforeach
                    @endif
                </ul>
                <h1>Consenso:</h1>
                <ul>
                    @php
                    $consenso = App\Models\RelProject::where('project_id', $id_project)->first();
                    @endphp
                    @if($consenso->consenso != 0)
                    <p>El consenso obtenido es <strong>{{ $numberToLabel($global) }}</strong></p>
                    @else
                    <p>Aún no se ha calculado el consenso</p>
                    @endif
                </ul>
                <h1>Inconsistencia:</h1>
                <ul>
                    @php
                    $inconsistencia = App\Models\RelUser::where('user_id', auth()->user()->id)
                    ->where('project_id', $id_project)
                    ->first();
                    $user = App\Models\User::find(auth()->user()->id);
                    @endphp
                    @if($inconsistencia->consistencia != 0)
                    <p>La inconsistencia de tus respuestas es <strong>{{ $numberToLabelA($valores[$user->name]) }}</strong></p>
                    @else
                    <p>Aún no se ha calculado la inconsistencia</p>
                    @endif
                </ul>
                <h1>Distancia: </h1>
                <ul>
                    @php
                    $relUserId = DB::table('rel_users')
                    ->where('user_id', auth()->user()->id)
                    ->where('project_id', $id_project)
                    ->value('id');
                    @endphp
                    @if(isset($distancia[$relUserId]))
                    <p>La distancia de tus respuestas con respecto a la matriz colectiva es <strong>{{ $numberToLabelA($distancia[$relUserId]) }}</strong></p>
                    @else
                    <p>Aún no se ha calculado la distancia</p>
                    @endif
                </ul>
            </div>

            @endif
        </div>
    </div>
</div>

@endsection