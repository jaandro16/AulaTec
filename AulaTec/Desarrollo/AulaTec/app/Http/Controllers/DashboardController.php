<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador del Dashboard
 * 
 * Maneja la página principal del dashboard para estudiantes:
 * - Verificar permisos de acceso
 * - Mostrar la vista principal del dashboard
 */
class DashboardController extends Controller
{
    /**
     * Verifica que el usuario actual tenga rol de alumno
     * 
     * Este método centraliza la verificación de permisos para asegurar
     * que solo los estudiantes puedan acceder al dashboard
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function checkTeacherRole()
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            abort(403, 'Debes iniciar sesión para acceder.');
        }

        // Verificar que el usuario tenga rol de alumno (no profesor)
        if (Auth::user()->rol !== 'alumno') {
            abort(403, 'Acceso restringido.');
        }
    }
    
    /**
     * Muestra la página principal del dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Verificar permisos del usuario antes de mostrar el dashboard
        $this->checkTeacherRole();
        
        // Retornar la vista del dashboard para estudiantes
        return view('layouts.dashboard');
    }
}