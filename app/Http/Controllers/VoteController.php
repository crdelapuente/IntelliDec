<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\RelUser;
use App\Models\Pref;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class VoteController
{
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Revisa si ya existe una relación entre el usuario y el proyecto
        $rel = RelUser::where('user_id', $user->id)->where('project_id', $project->id)->first();

        if (!$rel) {
            // Si no existe la relación, la crea
            $rel = RelUser::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'consistencia' => 0
            ]);
        }

        $prefs = $request->input('pref');

        foreach ($prefs as $ci => $cjPrefs) {
            foreach ($cjPrefs as $cj => $pref) {
                // Crea una nueva entrada en la tabla de preferencias.
                Pref::create([
                    'rel_users_id' => $rel->id,
                    'ci' => $ci,
                    'cj' => $cj,
                    'pref' => $pref,
                ]);
            }
        }

        return redirect()->route('projects.show', $project->id)->with('status', 'Tu voto ha sido registrado con éxito.');
    }

}
