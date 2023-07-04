<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Thread;
use App\Models\Post;

class PostController extends Controller
{
    public function index(Project $project, Thread $thread)
    {
        $posts = $thread->posts()->get();
        return view('posts.index', ['project' => $project, 'thread' => $thread, 'posts' => $posts]);
    }

    public function show(Project $project, Thread $thread, Post $post)
    {
        return view('posts.show', ['project' => $project, 'thread' => $thread, 'post' => $post]);
    }

    public function store(Request $request, Thread $thread)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $post = new Post(['content' => $request->input('content')]);
        $post->thread()->associate($thread);
        $post->user()->associate(auth()->user());
        $post->save();

        return redirect()->route('threads.show', ['project' => $thread->project->id, 'thread' => $thread->id])->with('success', 'Comentario publicado correctamente.');
    }

}
