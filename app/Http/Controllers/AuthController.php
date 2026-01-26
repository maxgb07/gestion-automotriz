<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login
     */
    public function showLoginForm()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Procesa el login del usuario
     */
    public function login(Request $request)
    {
        // Validar los datos del formulario
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Intentar autenticar al usuario
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerar la sesión para prevenir session fixation
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        // Si la autenticación falla, lanzar error de validación
        throw ValidationException::withMessages([
            'username' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Muestra el dashboard principal
     */
    public function dashboard()
    {
        return view('dashboard');
    }
}
