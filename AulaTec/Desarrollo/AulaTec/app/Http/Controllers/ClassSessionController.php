<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador de Sesiones de Clase
 * 
 * Maneja las operaciones relacionadas con las sesiones de clase disponibles:
 * - Obtener clases disponibles por fecha y horario
 * - Verificar disponibilidad de asientos
 * - Generar tokens de seguridad para el acceso a las clases
 */
class ClassSessionController extends Controller
{
    /**
     * Verifica que el usuario actual tenga rol de alumno
     * 
     * Este método se ejecuta antes de cada acción para asegurar que solo
     * los estudiantes puedan consultar y reservar clases
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function checkTeacherRole()
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            abort(403, 'Debes iniciar sesión para acceder.');
        }

        // Verificar que el usuario sea un alumno (no profesor)
        if (Auth::user()->rol !== 'alumno') {
            abort(403, 'Acceso restringido.');
        }
    }
    
    /**
     * Obtiene las clases disponibles para una fecha y horario específicos
     * 
     * Este método es utilizado por AJAX para cargar dinámicamente las clases
     * disponibles cuando el usuario selecciona una fecha y hora en el frontend
     * 
     * @param \Illuminate\Http\Request $request Contiene 'date' y 'time_slot_id'
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableClasses(Request $request)
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();
        
        try {
            // PREPARAR LA CONSULTA DE CLASES
            
            // Convertir la fecha recibida del frontend a formato Y-m-d para la BD
            $fecha = date('Y-m-d', strtotime($request->date));

            // Buscar todas las clases que coincidan con la fecha y horario seleccionados
            $classes = ClassSession::where('date', $fecha)
                ->where('time_slot_id', $request->time_slot_id)
                // Cargar relaciones necesarias para mostrar información completa
                ->with([
                    'teacher',      // Datos del profesor que imparte la clase
                    'classroom',    // Información del aula (nombre, capacidad)
                    'subject',      // Materia de la clase
                    'reservations'  // Reservas existentes para calcular disponibilidad
                ])
                ->get()
                // Transformar cada clase para el frontend
                ->map(function($class) {
                    // GENERAR TOKEN DE SEGURIDAD ÚNICO PARA CADA CLASE
                    
                    // Crear un token encriptado basado en el ID de la clase
                    $token = encrypt($class->id);
                    
                    // Guardar la relación token->clase_id en la sesión
                    // Esto permite validar el acceso posterior sin exponer el ID real
                    session(["class_token_{$token}" => $class->id]);
                    
                    // PREPARAR DATOS PARA EL FRONTEND
                    return [
                        'id' => $class->id,                              // ID interno (para referencia)
                        'token' => $token,                               // Token seguro para URLs
                        'nombre' => $class->subject->name,               // Nombre de la materia
                        'profesor' => $class->teacher->nombre . ' ' . $class->teacher->apellido, // Nombre completo del profesor
                        'aula' => $class->classroom->name,               // Nombre del aula
                        'capacidad' => $class->classroom->capacity,      // Capacidad total del aula
                        // Calcular asientos disponibles: capacidad total - reservas confirmadas
                        'asientosDisponibles' => $class->classroom->capacity - $class->reservations->count()
                    ];
                });

            // RESPUESTA EXITOSA
            return response()->json([
                'status' => 'success',
                'data' => $classes
            ]);

        } catch (\Exception $e) {
            // MANEJO DE ERRORES
            
            // En caso de cualquier error (BD, datos inválidos, etc.)
            // devolver respuesta JSON con detalles del error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al cargar las clases: ' . $e->getMessage()
            ], 500);
        }
    }
}