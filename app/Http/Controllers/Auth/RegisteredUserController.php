<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Hash;


class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'rol' => 'registrado',
            // 'password' => bcrypt($request->password),
            // 'password' => Hash::make($request->password),
        ]);

        // Login automático
        // Auth::login($user);

        // Login manual con redirección a iniciar sesión
        return to_route('login')->with('status', 'La cuenta se ha creado correctamente');
    }
}
