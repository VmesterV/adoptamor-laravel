<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // ... (Mantén los métodos login y logout igual que antes) ...
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Notificación de éxito
            return redirect()->intended('/')->with('toast_success', '¡Bienvenido de nuevo, ' . Auth::user()->name . '!');
        }

        // Notificación de error específica
        return back()->with('toast_error', 'Credenciales incorrectas. Verifica tu correo y contraseña.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * REGISTRO DE NUEVO USUARIO
     */
    public function register(Request $request)
    {
        // 1. Validar reglas según el rol
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', 
            'role' => 'required|in:person,shelter,store', 
            'department' => 'required_if:role,shelter,store',
            'province' => 'required_if:role,shelter,store',
            'district' => 'required_if:role,shelter,store',
            'address' => 'required_if:role,shelter,store',
        ]);

        // 2. Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            // Guardamos dirección solo si corresponde, sino null
            'department' => $request->role !== 'person' ? $request->department : null,
            'province' => $request->role !== 'person' ? $request->province : null,
            'district' => $request->role !== 'person' ? $request->district : null,
            'address' => $request->role !== 'person' ? $request->address : null,
            // Foto por defecto (avatar generado)
            'photo_url' => null, 
        ]);

        // 3. Iniciar sesión automáticamente
        Auth::login($user);

        return redirect('/')->with('success', '¡Cuenta creada exitosamente! Bienvenido a AdoptAmor.');
    }
}