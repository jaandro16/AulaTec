<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador de Horarios
 * 
 * Maneja la obtención de horarios disponibles para:
 * - Mostrar opciones de horarios en formularios de reserva
 * - Filtrar clases por franjas horarias
 */
class TimeSlotController extends Controller
{
    /**
     * Verifica que el usuario actual tenga rol de alumno
     * 
     * Control de acceso centralizado para asegurar que solo los estudiantes
     * puedan consultar los horarios disponibles
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
     * Obtiene todos los horarios disponibles en formato legible
     * 
     * Devuelve una lista de todos los slots de tiempo configurados
     * en el sistema, formateados para mostrar en dropdowns del frontend
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTimeSlots()
    {
        // Verificar permisos del usuario
        $this->checkTeacherRole();
        
        try {
            // Obtener todos los horarios y formatearlos para el frontend
            $timeSlots = TimeSlot::all()->map(function($slot) {
                return [
                    'id' => $slot->id,                                    // ID para usar en formularios
                    // Formatear las horas en formato HH:MM - HH:MM legible
                    'formatted_time' => date('H:i', strtotime($slot->start_time)) . ' - ' . 
                                     date('H:i', strtotime($slot->end_time))
                ];
            });

            // Respuesta exitosa con los datos formateados
            return response()->json([
                'status' => 'success',
                'data' => $timeSlots
            ]);

        } catch (\Exception $e) {
            // En caso de error, devolver mensaje descriptivo
            return response()->json([
                'status' => 'error',
                'message' => 'Error al cargar los horarios.'
            ], 500);
        }
    }
}