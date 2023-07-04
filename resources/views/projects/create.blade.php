@extends('layouts.app')

@section('titulo', 'Creación de proyecto')

@section('cuerpo')

<h1 class="margen-derecha">Creación de proyecto</h1>

<div class="container">
    <div class="main-body p-0 bg-white rounded">
        <div class="inner-wrapper">
        <a class="btn btn-warning d-inline-block mt-2 align-self-start" style="margin-left: 0.7%;" href="{{ route('projects.manage') }}" role="button">Volver</a>
            <!-- Formulario creación de proyecto -->
            <div class="inner-main-body p-2 p-sm-3 collapse forum-content show">
            <form class="mx-1 mx-md-4" action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                <!-- El token csrf caduca a las 2 horas -->
                @csrf
                @include('projects.form-fields')

                <!-- Añadir fichero de alternativas -->

                <div class="d-flex flex-column mb-3">
                    <h2 class="mb-1">Fichero de alternativas</h2>
                    <div class="form-outline flex-fill mb-0 d-flex align-items-center">
                        <input type="hidden" id="csv-file-data" name="csv_file" />
                        <input name="csv-file" id="csv-file-input" type="file" class="form-control" />
                    </div>
                    @error('csv-file')
                    <small style="color: red">* Debes incluir un fichero con las alternativas</small>
                    @enderror
                </div>

                <div id="column-checkboxes" class="mb-3" style="display: none;">
                </div>

                <script>
                    document.getElementById('csv-file-input').addEventListener('change', function(e) {
                        var file = e.target.files[0];
                        if (!file) {
                            return;
                        }

                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var contents = e.target.result;
                            document.getElementById('csv-file-data').value = contents;
                            var lines = contents.split('\n');
                            var columns = lines[0].split(',');

                            var columnCheckboxesDiv = document.getElementById('column-checkboxes');
                            columnCheckboxesDiv.innerHTML = '<h2 class="mb-1">Elige los criterios que consideres más importantes</h2>';
                            for (var i = 0; i < columns.length; i++) {
                                var checkbox = document.createElement('input');
                                checkbox.type = 'checkbox';
                                checkbox.name = 'columns[]';
                                checkbox.value = columns[i];
                                checkbox.id = 'column-' + i;

                                var label = document.createElement('label');
                                label.htmlFor = 'column-' + i;
                                label.appendChild(document.createTextNode(columns[i]));

                                var checkboxDiv = document.createElement('div');
                                checkboxDiv.classList.add('checkbox-group');
                                checkboxDiv.appendChild(checkbox);
                                checkboxDiv.appendChild(label);

                                columnCheckboxesDiv.appendChild(checkboxDiv);
                            }

                            columnCheckboxesDiv.style.display = 'block';
                        };
                        reader.readAsText(file);
                    });

                </script>

                    <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                        <button type="submit" class="btn btn-gold btn-lg">Crear proyecto</button>
                    </div>
            </form>
        </div>
    </div>
</div>

@endsection