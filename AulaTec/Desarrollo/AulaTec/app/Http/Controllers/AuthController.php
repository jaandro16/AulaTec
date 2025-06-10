<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador de Autenticación
 * 
 * Maneja todas las operaciones relacionadas con la autenticación de usuarios:
 * - Mostrar formulario de login
 * - Procesar el login
 * - Cerrar sesión (logout)
 */
class AuthController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión
     * 
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Retorna a la vista del formulario de login
        return view('modules.auth.login');
    }

    /**
     * Procesa el intento de inicio de sesión
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validar las credenciales recibidas del formulario
        $credentials = $request->validate([
            'email' => 'required|email',      // Email obligatorio y con formato válido
            'password' => 'required'          // Contraseña obligatoria
        ]);

        // Intentar autenticar al usuario con las credenciales proporcionadas
        if (Auth::attempt($credentials)) {
            // Si la autenticación es exitosa:
            
            // Regenerar el ID de sesión para prevenir ataques de fijación de sesión
            $request->session()->regenerate();

            // Verificar el rol del usuario autenticado
            if (Auth::user()->rol === 'profesor') {
                // Si es profesor, redirigir al panel de creación de clases
                return redirect()->route('admin.crear-clase.create');
            }
            
            // Si es estudiante u otro rol, redirigir al dashboard por defecto
            // intended() redirige a la página que el usuario intentaba acceder antes del login
            return redirect()->intended('dashboard');
        }

        // Si la autenticación falla:
        // Regresar al formulario con errores y mantener el email ingresado
        return back()
            ->withErrors([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ])
            ->withInput($request->only('email')); // Solo mantener el email, no la contraseña
    }

    /**
     * Cierra la sesión del usuario actual
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)   
    {
        // Cerrar la sesión del usuario autenticado
        Auth::logout();
    
        // Invalidar la sesión actual para prevenir reutilización
        $request->session()->invalidate();
        
        // Regenerar el token CSRF para mayor seguridad
        $request->session()->regenerateToken();
        
        // Redirigir al formulario de login con un mensaje de confirmación
        return redirect()->route('login')->with('message', 'Has cerrado sesión correctamente');
    }
}