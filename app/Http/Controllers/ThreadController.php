<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Thread;
use App\Models\Post;
use App\Http\Requests\SaveThreadRequest;

class ThreadController extends Controller
{
    public function index(Project $project)
    {
        $threads = $project->threads;

        return view('projects.threads.index', ['threads' => $threads, 'project' => $project]);
    }

    public function show(Thread $thread)
    {
        $posts = $thread->posts()->get();
        return view('projects.threads.show', ['thread' => $thread, 'posts' => $posts]);
    }


    public function create(Project $project)
    {
        return view('projects.threads.create', ['project' => $project]);
    }

    public function store(SaveThreadRequest $request, Project $project)
    {
        // Validar los datos del formulario
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Crear el thread
        $thread = new Thread([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'project_id' => $project->id,
            'user_id' => auth()->id(),
        ]);
        $thread->save();

        // Redirigir a la página de visualización del thread recién creado
        return redirect()->route('threads.show', $thread->id)->with('status', 'Foro creado correctamente.');
    }

    public function manage(Project $project)
    {
        $threads = $project->threads;

        return view('projects.threads.manage', ['threads' => $threads, 'project' => $project]);
    }

    public function delete(Project $project, Thread $thread)
    {
        return view('projects.threads.delete', ['thread' => $thread, 'project' => $project]);
    }

    public function destroy(Project $project, Thread $thread)
    {
        // Primero, eliminar todas las publicaciones relacionadas con este hilo
        $thread->posts()->delete();

        // Luego, eliminar el hilo en sí
        $thread->delete();

        // Redirigir a la página que prefieras con un mensaje de éxito
        return redirect()->route('projects.threads.manage', $project)->with('status', 'El foro ha sido eliminado correctamente.');
    }

}
