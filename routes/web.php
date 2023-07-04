<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthenticatedSessionController;


// Vista inicio
Route::view('/', 'inicio')->name('inicio');

// Vistas proyectos
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/project/manage', [ProjectController::class, 'manage'])->name('projects.manage');
Route::get('/project/{project}/monitoring', [ProjectController::class, 'monitoring'])->name('projects.monitoring');
Route::get('/project/create', [ProjectController::class, 'create'])->name('projects.create');
Route::post('/project', [ProjectController::class, 'store'])->name('projects.store');
Route::get('/project/{project}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/project/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
Route::patch('/project/{project}', [ProjectController::class, 'update'])->name('projects.update');
Route::get('/project/{project}/delete', [ProjectController::class, 'delete'])->name('projects.delete');
Route::delete('/project/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
Route::get('/project/{project}/vote', [ProjectController::class, 'vote'])->name('projects.vote');
Route::post('/projects/{project}/vote', [VoteController::class, 'store'])->name('vote.store');
Route::get('/project/{project}/export', [ProjectController::class, 'export'])->name('projects.export');

// Vistas foros y comentarios
Route::resource('projects.threads', ThreadController::class)->shallow();
Route::resource('threads.posts', PostController::class)->shallow();

Route::get('/projects/{project}/threads/manage', [ThreadController::class, 'manage'])->name('projects.threads.manage');
Route::get('/project/{project}/threads/{thread}/delete', [ThreadController::class, 'delete'])->name('projects.threads.delete');
Route::delete('/project/{project}/threads/{thread}', [ThreadController::class, 'destroy'])->name('projects.threads.destroy');

// Vistas login
Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Vistas registro
Route::view('/register', 'auth.register')->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

