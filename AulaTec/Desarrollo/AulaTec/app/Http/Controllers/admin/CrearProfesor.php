<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para la gestión de profesores
 * Maneja la creación y edición de perfiles de profesor
 */
class CrearProfesor extends Controller
{
    /**
     * Verifica que el usuario sea un profesor autenticado
     */
    private function checkTeacherRole()
    {
        if (!Auth::check()) {
            abort(403, 'Debes iniciar sesión para acceder.');
        }

        if (Auth::user()->rol !== 'profesor') {
            abort(403, 'Acceso restringido.');
        }
    }

    /**
     * Muestra el formulario de creación de profesor
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->checkTeacherRole();

        $titulo = 'Crear Nuevo Profesor';
        return view('modules.admin.crear_profesor.create', compact('titulo'));
    }

    /**
     * Almacena un nuevo profesor en la base de datos
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {  
        $this->checkTeacherRole();

        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required'
        ], [
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password_confirmation.required' => 'Por favor, confirma la contraseña'
        ]);

        try {
            $user = new User();
            $user->nombre = $request->nombre;
            $user->apellido = $request->apellido;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->rol = 'profesor';
            $user->save();

            return redirect()->route('admin.crear-profesor.create')
                           ->with('success', '¡Profesor registrado exitosamente!');

        } catch (\Exception $e) {
            Log::error('Error al registrar profesor: ' . $e->getMessage());
            return redirect()->route('admin.crear-profesor.create')
                            ->with('error', 'Error al registrar el profesor. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Muestra el formulario de edición del perfil
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $this->checkTeacherRole();

        $titulo = 'Editar Profesor';

        $usuario = Auth::user();

        if (!$usuario || $usuario->rol !== 'profesor') {
            return redirect()->route('login')
                ->with('error', 'No tienes permiso para acceder a esta página');
        }

        return view('modules.admin.crear_profesor.edit', compact('titulo', 'usuario'));
    }
}
