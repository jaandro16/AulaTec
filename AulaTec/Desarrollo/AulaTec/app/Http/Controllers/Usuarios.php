<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador de Usuarios
 * 
 * Maneja la gestión completa de usuarios del sistema:
 * - Registro de nuevos usuarios (estudiantes)
 * - Visualización y edición de perfiles
 * - Actualización de contraseñas
 * - Control de acceso basado en roles
 */
class Usuarios extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Muestra el formulario de registro de nuevos usuarios
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Retorna la vista del formulario de registro
        return view('modules.usuarios.create');
    }

    /**
     * Procesa el registro de un nuevo usuario en el sistema
     * 
     * Crea una cuenta de estudiante después de validar todos los datos
     * 
     * @param \Illuminate\Http\Request $request Datos del formulario de registro
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // VALIDACIÓN DE DATOS DEL FORMULARIO
        $request->validate([
            'nombre' => 'required|string|max:255',                          // Nombre obligatorio, máximo 255 caracteres
            'apellido' => 'required|string|max:255',                        // Apellido obligatorio, máximo 255 caracteres
            'email' => 'required|string|email|max:255|unique:users',        // Email único y válido
            'numero_matricula' => 'required|string|min:5|unique:users',     // Matrícula única, mínimo 5 caracteres
            'password' => 'required|string|min:8|confirmed',                // Contraseña mínimo 8 caracteres con confirmación
        ]);

        try {
            // CREAR EL NUEVO USUARIO
            $user = User::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'numero_matricula' => $request->numero_matricula,
                'password' => Hash::make($request->password),    // Encriptar la contraseña
                'rol' => 'alumno'                               // Todos los registros nuevos son estudiantes
            ]);

            // Redireccionar al login con mensaje de éxito
            return to_route('login')->with('success', 'Cuenta creada exitosamente. Por favor inicia sesión.');
        } catch (\Throwable $e) {
            // En caso de error, redireccionar con mensaje de error
            return to_route('login')->with('error', 'Error al crear la cuenta. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Muestra el perfil del usuario autenticado
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show()
    {
        // Obtener el usuario actualmente autenticado
        $usuario = Auth::user();
        
        // Verificar que el usuario esté autenticado
        if (!$usuario) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para ver tu perfil.');
        }

        // Mostrar la vista del perfil con los datos del usuario
        return view('modules.perfil.index', compact('usuario'));
    }

    /**
     * Muestra el formulario de edición del perfil del usuario autenticado
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza los datos del perfil del usuario autenticado
     * 
     * Permite modificar nombre, apellido y email con validaciones
     * 
     * @param \Illuminate\Http\Request $request Nuevos datos del perfil
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        try {
            // Obtener el usuario autenticado
            $usuario = Auth::user();

            // Verificar que el usuario esté autenticado
            if (!$usuario) {
                return redirect()->route('login')
                    ->with('error', 'Debes iniciar sesión para actualizar tu perfil');
            }

            // Log para debug
            // logger()->info('Actualizando perfil de usuario:', [
            //     'user_id' => $usuario->id,
            //     'rol' => $usuario->rol,
            //     'request_data' => $request->except(['_token', '_method'])
            // ]);

            // VALIDACIÓN DIFERENCIADA SEGÚN EL ROL DEL USUARIO
            if ($usuario->rol === 'profesor') {
                // ======= VALIDACIÓN PARA PROFESORES =======
                // Solo validar campos que corresponden a profesores
                $validated = $request->validate([
                    'nombre' => 'required|string|max:255',
                    'apellido' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email,'.$usuario->id,
                ], [
                    // Mensajes de error personalizados para profesores
                    'nombre.required' => 'El nombre es obligatorio',
                    'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
                    'apellido.required' => 'El apellido es obligatorio',
                    'apellido.max' => 'El apellido no puede exceder los 255 caracteres',
                    'email.required' => 'El correo electrónico es obligatorio',
                    'email.email' => 'El correo electrónico debe ser válido',
                    'email.unique' => 'Este correo electrónico ya está en uso',
                ]);

            } else {
                // ======= VALIDACIÓN PARA ESTUDIANTES =======
                // Validar todos los campos incluyendo matrícula y carrera
                $validated = $request->validate([
                    'nombre' => 'required|string|max:255',
                    'apellido' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email,'.$usuario->id,
                    'numero_matricula' => 'required|string|min:5|unique:users,numero_matricula,'.$usuario->id,
                    'carrera' => 'nullable|string|max:255',
                ], [
                    // Mensajes de error personalizados para estudiantes
                    'nombre.required' => 'El nombre es obligatorio',
                    'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
                    'apellido.required' => 'El apellido es obligatorio',
                    'apellido.max' => 'El apellido no puede exceder los 255 caracteres',
                    'email.required' => 'El correo electrónico es obligatorio',
                    'email.email' => 'El correo electrónico debe ser válido',
                    'email.unique' => 'Este correo electrónico ya está en uso',
                    'numero_matricula.required' => 'El número de matrícula es obligatorio',
                    'numero_matricula.min' => 'El número de matrícula debe tener al menos 5 caracteres',
                    'numero_matricula.unique' => 'Este número de matrícula ya está en uso',
                    'carrera.max' => 'La carrera no puede exceder los 255 caracteres',
                ]);
            }

            // Log de datos validados
            // logger()->info('Datos validados correctamente:', [
            //     'user_id' => $usuario->id,
            //     'validated_data' => $validated
            // ]);

            // ======= ACTUALIZAR USUARIO EN LA BASE DE DATOS =======
            $updated = $usuario->update($validated);

            if (!$updated) {
                throw new \Exception('No se pudieron actualizar los datos en la base de datos');
            }

            // Refrescar el modelo para obtener los datos actualizados
            $usuario->refresh();

            // Log de confirmación de actualización exitosa
            // logger()->info('Usuario actualizado correctamente:', [
            //     'user_id' => $usuario->id,
            //     'rol' => $usuario->rol,
            //     'nombre_actualizado' => $usuario->nombre,
            //     'apellido_actualizado' => $usuario->apellido,
            //     'email_actualizado' => $usuario->email,
            //     'numero_matricula_actualizado' => $usuario->numero_matricula ?? 'N/A (profesor)',
            //     'carrera_actualizada' => $usuario->carrera ?? 'N/A'
            // ]);

            // ======= REDIRECCIÓN BASADA EN EL ROL =======
            $redirectRoute = $usuario->rol === 'profesor' 
                ? 'admin.crear-profesor.edit'    // Profesores van al panel admin
                : 'perfil.show';                // Estudiantes van a su perfil

            return redirect()->route($redirectRoute)
                ->with('success', '¡Perfil actualizado correctamente!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            // ======= MANEJO DE ERRORES DE VALIDACIÓN =======
            // logger()->warning('Error de validación al actualizar perfil:', [
            //     'user_id' => Auth::id(),
            //     'errors' => $e->errors(),
            //     'request_data' => $request->except(['_token', '_method'])
            // ]);

            // Determinar ruta de redirección basada en rol
            $redirectRoute = Auth::user()->rol === 'profesor' 
                ? 'admin.crear-profesor.edit'
                : 'perfil.show';

            return redirect()->route($redirectRoute)
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Por favor, corrige los errores en el formulario');
                
        } catch (\Exception $e) {
            // ======= MANEJO DE ERRORES GENERALES =======
            // logger()->error('Error crítico al actualizar perfil:', [
            //     'user_id' => Auth::id(),
            //     'error_message' => $e->getMessage(),
            //     'error_trace' => $e->getTraceAsString(),
            //     'request_data' => $request->except(['_token', '_method'])
            // ]);

            // Determinar ruta de redirección basada en rol
            $redirectRoute = Auth::user()->rol === 'profesor' 
                ? 'admin.crear-profesor.edit'
                : 'perfil.show';

            return redirect()->route($redirectRoute)
                ->with('error', 'Error interno del servidor. Por favor, inténtalo de nuevo o contacta al administrador.')
                ->withInput();
        }
    }

    /**
     * Actualiza la contraseña del usuario autenticado
     * 
     * Valida la contraseña actual y establece una nueva diferente
     * 
     * @param \Illuminate\Http\Request $request Datos del cambio de contraseña
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        try {
            // Obtener el usuario autenticado
            $usuario = Auth::user();

            // Verificar que el usuario esté autenticado
            if (!$usuario) {
                return redirect()->route('login')
                    ->with('error', 'Debes iniciar sesión para actualizar tu contraseña');
            }

            // VALIDACIÓN COMPLEJA DE CONTRASEÑAS
            $validated = $request->validate([
                'password' => 'required|current_password',          // Validar contraseña actual
                'newPassword' => [
                    'required',
                    'min:8',                                        // Mínimo 8 caracteres
                    // Validación personalizada: nueva contraseña debe ser diferente
                    function ($attribute, $value, $fail) use ($request) {
                        if (Hash::check($value, Auth::user()->password)) {
                            $fail('La nueva contraseña debe ser diferente a la actual.');
                        }
                    },
                ],
                'confirmPassword' => 'required|same:newPassword'    // Confirmación debe coincidir
            ], [
                // Mensajes de error personalizados
                'password.current_password' => 'La contraseña actual no es correcta',
                'newPassword.min' => 'La nueva contraseña debe tener al menos 8 caracteres',
                'confirmPassword.same' => 'Las contraseñas no coinciden'
            ]);

            // Buscar y actualizar la contraseña del usuario
            $usuario = User::find($usuario->id);
            $usuario->update([
                'password' => Hash::make($validated['newPassword'])  // Encriptar la nueva contraseña
            ]);

            // REDIRECCIÓN BASADA EN EL ROL DEL USUARIO
            $redirectRoute = $usuario->rol === 'profesor' 
                ? 'admin.crear-profesor.edit'    // Profesores van al panel admin
                : 'perfil.show';                // Estudiantes van a su perfil

            return redirect()->route($redirectRoute)
                ->with('success', '¡Contraseña actualizada correctamente!');

        } catch (\Exception $e) {
            // En caso de error, determinar ruta de redirección basada en rol
            $redirectRoute = Auth::user()->rol === 'profesor' 
            ? 'admin.crear-profesor.edit'
            : 'perfil.show';

            return redirect()->route($redirectRoute)
                ->with('error', 'Error al actualizar la contraseña. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}