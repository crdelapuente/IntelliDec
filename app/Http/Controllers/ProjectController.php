<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\RelProject;
use App\Models\RelUser;
use App\Models\User;
use App\Models\Pref;
use Illuminate\Http\Request;
use App\Http\Requests\SaveProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Exports\ScoresExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class ProjectController
{
    public function index()
    {
        $proyectos = Project::with('threads')->get();

        return view('projects.index', ['proyectos' => $proyectos]);
    }

    public function show(Project $project)
    {
        $threads = $project->threads()->get();
        return view('projects.show', ['proyecto' => $project, 'threads' => $threads]);
    }

    public function showThreads(Project $project)
    {
        $threads = $project->threads;
        return view('projects.threads.show', ['threads' => $threads]);
    }

    public function monitoring(Project $project)
    {
        $resultado = $this->obtenerInfo($project->id);
        $GDD = $resultado[0];
        $GNDD = $resultado[1];
        $ranking = $resultado[2];
        $weights = $resultado[3];
        if ($project->id == '1') {
            $scores = $this->ordenaPortatiles($weights, $ranking);
        } else {
            $scores = $this->ordenaItems($weights, 'FO_' . $project->id, $ranking);
        }

        $global = $resultado[4];
        $valores = $resultado[5];
        $distancia = $resultado[6];

        if ($global == "No hay votos aún") {
            return view('projects.monitoring', ['id_project' => $project->id, 'project' => $project, 'GDD' => $GDD, 'GNDD' => $GNDD, 'ranking' => $ranking, 'weights' => $weights, 'scores' => $scores, 'global' => $global, 'valores' => $valores, 'distancia' => $distancia, 'numberToLabel' => [$this, 'numberToLabel'], 'numberToLabelA' => [$this, 'numberToLabelA']]);
        } else {
            return view('projects.monitoring', ['id_project' => $project->id, 'project' => $project, 'GDD' => $GDD, 'GNDD' => $GNDD, 'ranking' => $ranking, 'weights' => $weights, 'scores' => $scores, 'global' => $global, 'valores' => $valores, 'distancia' => $distancia, 'numberToLabel' => [$this, 'numberToLabel'], 'numberToLabelA' => [$this, 'numberToLabelA']]);
        }
    }

    public function export(Project $project)
    {
        $resultado = $this->obtenerInfo($project->id);
        $ranking = $resultado[2];
        $weights = $resultado[3];
        if ($project->id == '1') {
            $scores = $this->ordenaPortatiles($weights, $ranking);
        } else {
            $scores = $this->ordenaItems($weights, 'FO_' . $project->id, $ranking);
        }
        return Excel::download(new ScoresExport($scores), 'puntuaciones'. $project->name .'.xlsx');
    }

    public function create()
    {
        return view('projects.create', ['project' => new Project]);
    }

    public function store(Request $request)
    {
        // Primero, validamos los datos del formulario
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'csv-file' => 'required|file|mimes:csv,txt',
        ]);

        // Luego, creamos el proyecto
        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Creamos la entrada en rel_projects
        RelProject::create([
            'project_id' => $project->id,
            'fo_id' => $project->id,
            'consenso' => 0,
        ]);

        // Creamos la entrada en rel_users
        RelUser::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'consistencia' => 0,
        ]);

        // Convertimos la cadena CSV en un array
        $csvData = str_getcsv($request->csv_file, "\n");
        // Crear un stream a partir del array
        $stream = fopen('php://memory', 'r+');
        foreach ($csvData as $line) {
            fputcsv($stream, str_getcsv($line));
        }
        rewind($stream);

        // Tomamos las columnas a mantener desde el request
        $columnsToKeep = $request->input('columns', []);

        // Finalmente, creamos la tabla dinámica
        $this->createDynamicTable($project->id, $stream, $columnsToKeep);
        fclose($stream);

        return redirect()->route('projects.manage')->with('status', 'El proyecto se ha creado correctamente.');
    }

    public function manage()
    {
        $proyectos = Project::get();

        return view('projects.manage', ['proyectos' => $proyectos]);
    }

    public function edit(Project $project)
    {
        $users = User::all();
        return view('projects.edit', ['project' => $project, 'users' => $users]);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        // Actualiza el nombre y la descripción del proyecto
        $project->name = $request->input('name');
        $project->description = $request->input('description');
        $project->save();

        // Actualiza las relaciones de usuarios del proyecto
        if ($request->has('user_id')) {
            $user_ids = $request->input('user_id');
            $project->users()->sync($user_ids);
        } else {
            $project->users()->detach(); // Si no se seleccionó ningún usuario, se eliminan todas las relaciones
        }

        return redirect()->route('projects.manage')->with('status', 'El proyecto se ha actualizado correctamente');
    }

    public function delete(Project $project)
    {
        return view('projects.delete', ['proyecto' => $project]);
    }

    public function destroy(Project $project)
    {
        // Borra los registros en prefs que están asociados con este proyecto
        DB::table('prefs')->whereIn('rel_users_id', $project->relUsers()->pluck('id'))->delete();

        // Borra la relación rel_users
        $project->relUsers()->delete();

        // Borra la relación rel_projects
        $project->relProjects()->delete();

        // Borra la tabla fo_id
        $tableName = 'fo_' . $project->id;
        Schema::dropIfExists($tableName);

        // Borra el proyecto
        $project->delete();

        return redirect()->route('projects.manage')->with('status', 'El proyecto se ha eliminado correctamente.');
    }

    public function vote(Project $project)
    {
        $tableName = 'fo_' . $project->id;
        $tableColumns = Schema::getConnection()->getDoctrineSchemaManager()->listTableColumns($tableName);
        $criterios = array_keys($tableColumns);

        array_shift($criterios);
        array_pop($criterios);

        $userId = auth()->user()->id;  // Asegúrate de que el usuario esté autenticado
        $respuestasPrevias = $this->obtenerRespuestasPrevias($userId, $project->id);

        return view('projects.vote', [
            'criterios' => $criterios,
            'proyecto' => $project,
            'respuestasPrevias' => $respuestasPrevias
        ]);
    }

    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ OBTENER RESPUESTAS @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

    private function obtenerRespuestasPrevias($userId, $projectId)
    {
        // Obtiene el rel_users_id correspondiente al userId y projectId.
        $rel_users_id = DB::table('rel_users')
            ->where('user_id', $userId)
            ->where('project_id', $projectId)
            ->pluck('id')
            ->first();

        // Si no se encuentra rel_users_id, devolver un array vacío.
        if (!$rel_users_id) {
            return [];
        }

        // Obtiene las preferencias para el rel_users_id.
        $prefs = Pref::where('rel_users_id', $rel_users_id)->get(['ci', 'cj', 'pref']);

        // Organizar los datos en un formato similar al anterior
        $prefs = $prefs->mapWithKeys(function($item) {
            return [$item['ci'] . '-' . $item['cj'] => $item['pref']];
        })->toArray();

        return $prefs;
    }

    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ CREACIÓN TABLA FO @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

    // Función que crea la tabla FO_id para cada proyecto con su información
    private function createDynamicTable($projectId, $csvFile, $columnsToKeep)
    {
        // Modificamos los datos del archivo CSV
        $data = $this->modifyCSV($csvFile, $columnsToKeep);

        // Creamos la sintaxis SQL para la tabla
        $tableSyntax = '`id` INT AUTO_INCREMENT PRIMARY KEY, ';
        foreach ($columnsToKeep as $column) {
            $tableSyntax .= "`$column` VARCHAR(255), ";
        }
        $tableSyntax .= '`descripcion` TEXT';

        // Creamos el nombre de la tabla
        $tableName = 'fo_' . $projectId;

        // Finalmente, creamos la tabla
        DB::statement("CREATE TABLE $tableName ($tableSyntax)");

        // Luego, insertamos los datos del CSV en la tabla
        foreach ($data as $row) {
            DB::table($tableName)->insert($row);
        }
    }

    private function modifyCSV($csvFile, $columnsToKeep)
    {
        $data = [];
        while (($row = fgetcsv($csvFile)) !== FALSE) {
            $data[] = $row;
        }

        // Extraemos los nombres de las columnas del primer elemento del array
        $columns = array_shift($data);

        $newData = [];

        foreach ($data as $row) {
            $record = array_combine($columns, $row);
            $newRecord = [];
            $description = [];

            foreach ($record as $column => $value) {
                if (in_array($column, $columnsToKeep)) {
                    $newRecord[$column] = $value;
                }
                $description[] = "$column: $value";
            }

            $newRecord['descripcion'] = implode(', ', $description);
            $newData[] = $newRecord;
        }

        return $newData;
    }

    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ ORDENACIÓN DE PORTATILES @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

    public function calculate_screen_score($laptop)
    {
        $screen = $laptop->pantalla;
        $touch_bonus = 0;
        $max_score = 3840 * 2160;  // Consideramos 4K como la resolución máxima.

        // Verifica si la pantalla es táctil.
        if (strpos($screen, 'Touchscreen') !== false) {
            $touch_bonus = 0.5;
        }

        // Extrae los números de la resolución.
        preg_match('/(\d+)x(\d+)/', $screen, $matches);

        // Si hay dos números en la resolución, multiplica los números para obtener el score base.
        if (count($matches) == 3) {
            $base_score = $matches[1] * $matches[2];
        } else {
            // Si no hay dos números, asigna un score base por defecto.
            $base_score = 0;
        }

        // Normalizamos el score base.
        $base_score = $base_score / $max_score;

        // El score total es el score base más el bonus de pantalla táctil.
        $total_score = $base_score + $touch_bonus;

        // Aseguramos que el score total no sea mayor que 1.
        $total_score = min($total_score, 1.0);

        return $total_score;
    }

    public function calculate_cpu_score($laptop)
    {
        $cpu = $laptop->procesador;
        $brand_bonus = 0;
        $series_bonus = 0;
        $speed_score = 0;

        $max_speed = 5.0;  // Suponemos que 5.0 GHz es la velocidad máxima posible para un procesador de portátil.

        // Verifica la marca del procesador y asigna un bonus.
        if (strpos($cpu, 'Intel') !== false) {
            $brand_bonus = 1.0;  // Intel es preferible, por lo que le damos un puntaje completo.
        } elseif (strpos($cpu, 'AMD') !== false) {
            $brand_bonus = 0.8;  // AMD es menos preferible que Intel, así que le damos un puntaje más bajo.
        }

        // Verifica la serie del procesador y asigna un bonus.
        if (preg_match('/i7/', $cpu)) {
            $series_bonus = 1.0;  // i7 es la mejor serie, así que le damos un puntaje completo.
        } elseif (preg_match('/i5/', $cpu)) {
            $series_bonus = 0.7;  // i5 es intermedio, por lo que tiene un puntaje intermedio.
        } elseif (preg_match('/i3/', $cpu)) {
            $series_bonus = 0.4;  // i3 es la serie más baja, por lo que tiene un puntaje más bajo.
        }

        // Extrae la velocidad del procesador.
        preg_match('/\d+\.\d+GHz/', $cpu, $matches);
        if (count($matches) > 0) {
            // Si encuentra la velocidad, la normaliza según el valor máximo.
            $speed = floatval($matches[0]);
            $speed_score = $speed / $max_speed;
        }

        // El score total es la suma de los tres scores.
        $total_score = $brand_bonus + $series_bonus + $speed_score;

        // Nos aseguramos de que el score total no sea mayor que 1.
        $total_score = min($total_score, 1.0);

        return $total_score;
    }

    public function calculate_storage_score($laptop)
    {
        $storage = $laptop->almacenamiento;
        $type_bonus = 0;
        $amount_score = 0;
        $multi_drive_bonus = 0;

        $max_amount_score = 4000000;  // Suponiendo que 4TB es el almacenamiento máximo que puedes tener en un portátil.

        // Verifica el tipo de almacenamiento y asigna un bonus.
        if (strpos($storage, 'SSD') !== false) {
            $type_bonus = 1.0;  // SSD es el mejor tipo de almacenamiento, le damos un puntaje completo.
        } elseif (strpos($storage, 'HDD') !== false) {
            $type_bonus = 0.6;  // HDD es menos preferible que SSD, así que le damos un puntaje más bajo.
        } elseif (strpos($storage, 'Hybrid') !== false || strpos($storage, 'Flash Storage') !== false) {
            $type_bonus = 0.8;  // Hybrid y Flash Storage son intermedios, por lo que tienen un puntaje intermedio.
        }

        // Extrae la cantidad de almacenamiento y multiplica este valor por un factor para obtener el score de cantidad.
        preg_match_all('/\d+GB|\d+TB/', $storage, $matches);
        if (count($matches[0]) > 0) {
            foreach ($matches[0] as $match) {
                $amount = floatval($match);
                if (strpos($match, 'TB') !== false) {
                    $amount *= 1000;  // Convertir TB a GB.
                }
                $amount_score += $amount;
            }
            // Normalizamos el score de la cantidad.
            $amount_score = $amount_score * 100 / $max_amount_score;
        }

        // Verifica si hay múltiples discos y asigna un bonus.
        if (strpos($storage, '+') !== false) {
            $multi_drive_bonus = 0.1;  // Este bonus ya está en el rango de [0,1], por lo que no necesitamos normalizarlo.
        }

        // El score total es la suma de los tres scores.
        $total_score = $type_bonus + $amount_score + $multi_drive_bonus;

        // Nos aseguramos de que el score total no sea mayor que 1.
        $total_score = min($total_score, 1.0);

        return $total_score;
    }

    public function calculate_price_score($laptop)
    {
        $price = floatval($laptop->precio);

        $max_price = 73569000; // valor máximo 
        $min_price = 79290.72; // valor mínimo

        // Normaliza el precio al rango [0,1]
        $normalized_price = ($price - $min_price) / ($max_price - $min_price);

        // Invierte el precio normalizado, ya que un precio más bajo debería dar un score más alto
        $score = 1 - $normalized_price;

        // Nos aseguramos de que el score no sea mayor que 1
        $score = min($score, 1.0);

        return $score;
    }

    public function ordenaPortatiles($weights, $ranking)
{
    // Obtén todos los registros de la tabla.
    $laptops = DB::table('FO_1')->get();

    // Ordena los pesos en base al ranking
    array_multisort($ranking, SORT_DESC, $weights);

    // Inicializa un array para almacenar los scores e IDs.
    $scores = [];

    foreach ($laptops as $laptop) {
        // Calcula los scores.
        $screen_score = $this->calculate_screen_score($laptop);
        $cpu_score = $this->calculate_cpu_score($laptop);
        $storage_score = $this->calculate_storage_score($laptop);
        $price_score = $this->calculate_price_score($laptop);
        $description = $laptop->descripcion;

        $score_values = [
            $price_score,
            $cpu_score,
            $screen_score,
            $storage_score,
        ];

        // Calcula el valor de similitud.
        $similarity_value = 0;
        array_map(function ($weight, $score) use (&$similarity_value) {
            $similarity_value += $score * $weight;
        }, $weights, $score_values);

        // Almacena el ID, los scores y el valor de similitud en el array.
        $scores[] = [
            'id' => $laptop->id,
            'screen_score' => $screen_score,
            'cpu_score' => $cpu_score,
            'storage_score' => $storage_score,
            'price_score' => $price_score,
            'similarity_value' => $similarity_value,
            'description' => $description,
        ];
    }

    // Ordena el array por los scores y el precio.
    usort($scores, function ($a, $b) {
        return $b['similarity_value'] <=> $a['similarity_value'];
    });

    return $scores;
}

    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ ORDENACIÓN DE ITEMS @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

    public function normalizeCriteria($table)
    {
        $scores = [];

        // Obtenemos los nombres de las columnas de la tabla
        //$columns = DB::getSchemaBuilder()->getColumnListing($table);      // Aquí no las muestra por orden
        $columnsraw = DB::select('SHOW COLUMNS FROM ' . $table);
        $columns = array_map(function ($column) {
            return $column->Field;
        }, $columnsraw);

        // Guardamos la última columna (la descripción)
        $descriptionColumn = array_pop($columns);

        // Eliminamos la primera columna que no es criterio
        array_shift($columns);

        foreach ($columns as $column) {
            $values = DB::table($table)->pluck($column)->map(function ($value) {
                return floatval($value);
            });

            $max_value = $values->max();
            $min_value = $values->min();

            $items = DB::table($table)->get();

            foreach ($items as $item) {
                // Convertimos el valor de la columna a float
                $item_value = floatval($item->$column);

                // Verificamos si max_value es igual a min_value para evitar una división por cero
                if ($max_value == $min_value) {
                    $scores[$item->id][$column] = 1;
                } else {
                    $scores[$item->id][$column] = round(($item_value - $min_value) / ($max_value - $min_value), 5);
                }
            }
        }

        // Añadimos la descripción al final de cada item
        foreach (DB::table($table)->get() as $item) {
            $scores[$item->id][$descriptionColumn] = $item->$descriptionColumn;
        }

        return $scores;
    }

    public function ordenaItems($weights, $table, $ranking)
    {
        // Obten los scores
        $scores = $this->normalizeCriteria($table);

        // Verifica que el ranking no esté vacío
        $weightsExist = !empty($ranking) && !empty($weights);
        if ($weightsExist) {
            // Ordena los pesos en base al ranking
            array_multisort($ranking, SORT_DESC, $weights);
        }

        // Inicializa un array para almacenar los scores e IDs.
        $items_scores = [];
        $max_similarity = 0;

        foreach ($scores as $id => $item_scores) {
            // Calcula el valor de similitud.
            $similarity_value = 0;

            // Aquí es donde se implementa la multiplicación ordenada de pesos y scores
            array_map(function ($key, $score, $weight) use (&$similarity_value, $weightsExist) {
                if (is_numeric($score) && $key != 'descripcion') { // Asegura que el score es numérico y no es la descripción
                    $similarity_value += $weightsExist ? $score * $weight : $score;
                }
            }, array_keys($item_scores), $item_scores, $weightsExist ? $weights : []);

            // Actualiza el valor máximo de similitud si es necesario
            $max_similarity = max($max_similarity, $similarity_value);

            // Almacena el ID, los scores y el valor de similitud en el array.
            $items_scores[] = [
                'id' => $id,
                'scores' => $item_scores,
                'similarity_value' => $similarity_value,
                'description' => $item_scores['descripcion'],
            ];
        }

        // Normaliza los valores de similitud para que estén en el rango [0, 1].
        foreach ($items_scores as &$item) {
            $item['similarity_value'] /= $max_similarity;
        }

        // Ordena el array por el valor de similitud.
        usort($items_scores, function ($a, $b) {
            return $b['similarity_value'] <=> $a['similarity_value'];
        });

        return $items_scores;
    }


    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ CÁLCULOS @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

    public function calculateGDD($collectiva, $criterios)
    {
        $gdd = array();

        foreach ($criterios as $criterio_i) {
            $gdd[$criterio_i] = 0;
            foreach ($criterios as $criterio_j) {
                if ($criterio_i != $criterio_j) {
                    $gdd[$criterio_i] += $collectiva[$criterio_i][$criterio_j];
                }
            }
            $gdd[$criterio_i] /= (count($criterios) - 1);
        }

        return $gdd;
    }

    public function calculateGNDD($collectiva, $criterios)
    {
        // Inicializar la matriz colectivaS
        $colectivaS = array();
        foreach ($criterios as $criterio_i) {
            $colectivaS[$criterio_i] = array();
            foreach ($criterios as $criterio_j) {
                $colectivaS[$criterio_i][$criterio_j] = ($criterio_i == $criterio_j) ? -1 : 0;
            }
        }

        // Calcular colectivaS
        foreach ($criterios as $criterio_i) {
            foreach ($criterios as $criterio_j) {
                if ($criterio_i != $criterio_j) {
                    if (($collectiva[$criterio_j][$criterio_i] - $collectiva[$criterio_i][$criterio_j]) < 0) {
                        $colectivaS[$criterio_j][$criterio_i] = 1 - 0;
                    } else {
                        $colectivaS[$criterio_j][$criterio_i] = 1 - ($collectiva[$criterio_j][$criterio_i] - $collectiva[$criterio_i][$criterio_j]);
                    }
                }
            }
        }

        // Calcular GNDD
        $gndd = array();
        foreach ($criterios as $criterio_i) {
            $gndd[$criterio_i] = 0;
            foreach ($criterios as $criterio_j) {
                if ($criterio_i != $criterio_j) {
                    $gndd[$criterio_i] += $colectivaS[$criterio_j][$criterio_i];
                }
            }
            $gndd[$criterio_i] /= (count($criterios) - 1);
        }

        return $gndd;
    }

    public function calculateRanking($gdd, $gndd, $criterios)
    {
        $rv = array();

        foreach ($criterios as $criterio) {
            $rv[$criterio] = ($gdd[$criterio] + $gndd[$criterio]) / 2;
        }

        return $rv;
    }

    public function calculateWeights($numAlternativas)
    {
        // Si no hay alternativas, devuelve un array de ceros
        if ($numAlternativas == 0) {
            return array_fill(0, 4, 0);
        }

        $total = $numAlternativas * ($numAlternativas + 1) / 2;
        $weights = [];
        for ($i = $numAlternativas; $i >= 1; $i--) {
            $weights[] = $i / $total;
        }
        return $weights;
    }

    private function inicializaMatrizCuadrada($criterios)
    {
        $matriz = [];
        foreach ($criterios as $criterio_i) {
            foreach ($criterios as $criterio_j) {
                $matriz[$criterio_i][$criterio_j] = $criterio_i === $criterio_j ? -1 : 0;
            }
        }
        return $matriz;
    }

    private function obtenerPreferencias($rel_users_id)
    {
        $prefs = Pref::where('rel_users_id', $rel_users_id)->get(['ci', 'cj', 'pref']);

        $preferencias = [];
        foreach ($prefs as $pref) {
            $preferencias[$pref->ci][$pref->cj] = $pref->pref;
        }

        return $preferencias;
    }

    private function compara2expertos($e1_id, $e2_id, $criterios)
    {
        // No necesitamos pasar el número de alternativas ya que podemos usar los criterios directamente.
        $resultado = $this->inicializaMatrizCuadrada($criterios);

        // Obtén las preferencias para cada experto.
        $e1_prefs = $this->obtenerPreferencias($e1_id);
        $e2_prefs = $this->obtenerPreferencias($e2_id);

        foreach ($e1_prefs as $ci => $values) {
            foreach ($values as $cj => $value) {
                $e1_prefs[$ci][$cj] = ($value - 1) / 4;
            }
        }

        foreach ($e2_prefs as $ci => $values) {
            foreach ($values as $cj => $value) {
                $e2_prefs[$ci][$cj] = ($value - 1) / 4;
            }
        }

        // Compara las preferencias.
        foreach ($criterios as $criterio_i) {
            foreach ($criterios as $criterio_j) {
                if ($criterio_i !== $criterio_j) {
                    $diff = $e1_prefs[$criterio_i][$criterio_j] - $e2_prefs[$criterio_i][$criterio_j];
                    $resultado[$criterio_i][$criterio_j] = $diff < 0 ? -$diff : $diff;
                    // print_r($criterio_i.' '.$criterio_j.' ');
                    // print_r($resultado[$criterio_i][$criterio_j]);
                    // echo '<br/>';
                }
            }
        }
        return $resultado;
    }

    public function numberToLabelA($number)
    {
        if ($number <= 0.2) {
            return 'muy baja';
        } else if ($number <= 0.4) {
            return 'baja';
        } else if ($number <= 0.6) {
            return 'media';
        } else if ($number <= 0.8) {
            return 'alta';
        } else {
            return 'muy alta';
        }
    }

    public function numberToLabel($number)
    {
        if ($number <= 0.2) {
            return 'muy bajo';
        } else if ($number <= 0.4) {
            return 'bajo';
        } else if ($number <= 0.6) {
            return 'medio';
        } else if ($number <= 0.8) {
            return 'alto';
        } else {
            return 'muy alto';
        }
    }

    public function obtenerInfo($projectId)
    {
        // Obtén los criterios para el proyecto específico.
        $query = "SELECT ci FROM prefs WHERE rel_users_id IN (SELECT id FROM rel_users WHERE project_id=?) GROUP BY ci";
        $criterios = DB::select($query, [$projectId]);

        // Convertir el resultado a un array de strings.
        $criterios = array_map(function ($obj) {
            return $obj->ci;
        }, $criterios);

        // Crear un mapa de criterios a índices.
        $numAlternativas = count($criterios);

        // Obtén los IDs de los expertos para el proyecto específico que han votado.
        $expertos = DB::table('rel_users')
            ->join('prefs', 'rel_users.id', '=', 'prefs.rel_users_id')
            ->where('rel_users.project_id', $projectId)
            ->distinct()
            ->pluck('rel_users.id')
            ->toArray();

        $contador = 0;
        $collectiva = array();

        foreach ($criterios as $criterio) {
            // Inicializar las filas de la matriz colectiva
            $collectiva[$criterio] = array();
            foreach ($criterios as $inner_criterio) {
                if ($criterio == $inner_criterio) {
                    $collectiva[$criterio][$inner_criterio] = -1;
                } else {
                    $collectiva[$criterio][$inner_criterio] = 0;
                }
            }
        }

        foreach ($expertos as $experto_i) {
            $prefs = Pref::where('rel_users_id', $experto_i)->get(['ci', 'cj', 'pref']);

            // Actualizar los valores de la matriz colectiva
            foreach ($prefs as $pref) {
                if ($pref->ci != $pref->cj) {
                    $collectiva[$pref->ci][$pref->cj] += $pref->pref;
                }
            }
        }

        // Normalizar los valores de la matriz colectiva
        foreach ($criterios as $criterio_i) {
            foreach ($criterios as $criterio_j) {
                if ($criterio_i != $criterio_j) {
                    $collectiva[$criterio_i][$criterio_j] /= count($expertos);
                }
            }
        }

        // Convertimos la matriz collectiva en intervalo unitario
        foreach ($criterios as $criterio_i) {
            foreach ($criterios as $criterio_j) {
                if ($criterio_i != $criterio_j) {
                    $collectiva[$criterio_i][$criterio_j] = ($collectiva[$criterio_i][$criterio_j] - 1) / 4;
                }
            }
        }

        // CALCULO DEL CONSENSO

        // Inicializamos las matrices de consenso.
        $CC = $this->inicializaMatrizCuadrada($criterios);
        $comp = $this->inicializaMatrizCuadrada($criterios);

        foreach ($expertos as $i => $experto_i) {
            foreach (array_slice($expertos, $i + 1) as $experto_j) {
                $contador++;
                $comp = $this->compara2expertos($experto_i, $experto_j, $criterios);
                for ($k = 0; $k < count($criterios); $k++) {
                    for ($l = 0; $l < count($criterios); $l++) {
                        $CC[$criterios[$k]][$criterios[$l]] += $comp[$criterios[$k]][$criterios[$l]];
                    }
                }
            }
        }

        // Cálculo del consenso por pares.
        foreach ($criterios as $criterio_i) {
            foreach ($criterios as $criterio_j) {
                if ($contador > 0) {
                    $CC[$criterio_i][$criterio_j] = $CC[$criterio_i][$criterio_j] / $contador;
                } else {
                    $CC[$criterio_i][$criterio_j] = 0;
                }
                if ($criterio_i == $criterio_j) $CC[$criterio_i][$criterio_j] = 1;
                $CC[$criterio_i][$criterio_j] = 1 - $CC[$criterio_i][$criterio_j];
            }
        }

        // Cálculo del consenso por alternativa.
        $consensusALT = array_fill_keys($criterios, 0);
        foreach ($criterios as $criterio_i) {
            $contador = 0;
            foreach ($criterios as $criterio_j) {
                if ($criterio_i != $criterio_j) {
                    $consensusALT[$criterio_i] += $CC[$criterio_j][$criterio_i] + $CC[$criterio_i][$criterio_j];
                    $contador += 2;
                }
            }
            $consensusALT[$criterio_i] = $consensusALT[$criterio_i] / $contador;
        }

        foreach ($criterios as $criterio_i) {
            $consensusALT[$criterio_i] = 1 - $consensusALT[$criterio_i];
        }

        // Cálculo del consenso global.
        $global = 0;
        foreach ($consensusALT as $value) {
            $global += $value;
        }
        if (count($consensusALT) > 0) {
            $global = 1 - ($global / count($consensusALT));
        } else {
            $global = "No hay votos aún";
        }

        // Almacenamos el valor de consenso global en la BD
        $project = RelProject::where('project_id', $projectId)->first();
        if ($project) {
            // Solo intenta guardar $global si es un número
            if (is_numeric($global)) {
                $project->consenso = $global;
                $project->save();
            }
        } else {
            // Manejar el caso cuando no se encuentra el proyecto
            echo "No se encontró el proyecto con project_id: " . $projectId;
        }

        $GDD = $this->calculateGDD($collectiva, $criterios);
        $GNDD = $this->calculateGNDD($collectiva, $criterios);
        $ranking = $this->calculateRanking($GDD, $GNDD, $criterios);
        $weights = $this->calculateWeights($numAlternativas);

        // Convierte los arrays en strings.
        $GDD = implode(", ", $GDD);
        $GNDD = implode(", ", $GNDD);


        // Cálculo de Inconsistencia

        // Obtiene los user_id de la tabla rel_users
        $userIds = array_map(function ($obj) {
            return DB::table('rel_users')->where('id', $obj)->value('user_id');
        }, $expertos);

        // Usando esos user_id, obtiene los nombres de la tabla users
        $names = array_map(function ($userId) {
            return DB::table('users')->where('id', $userId)->value('name');
        }, $userIds);

        // Inicializando el array de valores
        $valores = array_fill_keys($names, 0);

        // Crea una función para mapear los nombres de usuario a los user_id
        $userIdMap = array_combine($names, $userIds);

        foreach ($expertos as $index => $idRelUsers) {
            $userId = DB::table('rel_users')->where('id', $idRelUsers)->value('user_id');
            $userName = DB::table('users')->where('id', $userId)->value('name');
            $contador = 0;
        
            if (count($expertos) > 1) {
                foreach ($criterios as $i => $criterio_i) {
                    foreach (array_slice($criterios, $i + 1) as $criterio_j) {
                        if ($criterio_i != $criterio_j) {
                            // Obteniendo las preferencias de la base de datos
                            $pref_i_j = Pref::where('rel_users_id', $idRelUsers)->where('ci', $criterio_i)->where('cj', $criterio_j)->first()->pref;
                            $pref_j_i = Pref::where('rel_users_id', $idRelUsers)->where('ci', $criterio_j)->where('cj', $criterio_i)->first()->pref;
        
                            $pref_i_j = ($pref_i_j - 1) / count($expertos);
                            $pref_j_i = ($pref_j_i - 1) / count($expertos);
        
                            if ($pref_i_j - (1 - $pref_j_i) > 0) {
                                $valores[$userName] += $pref_i_j - (1 - $pref_j_i);
                            } else if ($pref_i_j - (1 - $pref_j_i) <= 0) {
                                $valores[$userName] += (1 - $pref_j_i) - $pref_i_j;
                            }
                            $contador++;
                        }
                    }
                }
        
                $valores[$userName] /= $contador;
            } else {
                $valores[$userName] = 0;
            }
        
            // Buscar al experto en la tabla 'rel_users' usando user_id en lugar de nombre de usuario
            $expert = RelUser::where('user_id', $userIdMap[$userName])
                ->where('project_id', $projectId)
                ->first();
            if ($expert) {
                // Almacenar el valor de inconsistencia
                $expert->consistencia = $valores[$userName];
                $expert->save();
            }
        }

        // Cálculo de la Distancia con respecto a la Matriz Colectiva
        $distancia = array_fill_keys($expertos, 0);

        foreach ($expertos as $experto) {
            $contador = 0;
            foreach ($criterios as $criterio_i) {
                foreach ($criterios as $criterio_j) {
                    if ($criterio_i != $criterio_j) {
                        $pref_i_j_raw = Pref::where('rel_users_id', $experto)->where('ci', $criterio_i)->where('cj', $criterio_j)->first()->pref;
                        $pref_i_j = ($pref_i_j_raw - 1) / 4;
                        $distancia[$experto] += abs($pref_i_j - $collectiva[$criterio_i][$criterio_j]);
                        $contador++;
                    }
                }
            }
            $distancia[$experto] /= $contador;
        }

        return array($GDD, $GNDD, $ranking, $weights, $global, $valores, $distancia);
    }
}
