<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador de Contacto
 * 
 * Maneja la funcionalidad del formulario de contacto:
 * - Mostrar la página de contacto
 * - Procesar y enviar mensajes a través de Web3Forms
 */
class ContactController extends Controller
{
    /**
     * Muestra la página de contacto
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Retorna la vista del formulario de contacto
        return view('modules.contacto.index');
    }

    /**
     * Procesa el envío del formulario de contacto
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enviar(Request $request)
    {
        // VALIDACIÓN DE DATOS DEL FORMULARIO
        // Definir las reglas de validación para cada campo requerido
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',        // Nombre: obligatorio, texto, máximo 255 caracteres
            'email' => 'required|email|max:255',        // Email: obligatorio, formato válido, máximo 255 caracteres
            'subject' => 'required|string|max:255',     // Asunto: obligatorio, texto, máximo 255 caracteres
            'message' => 'required|string',             // Mensaje: obligatorio, texto sin límite específico
        ]);

        // Si la validación falla, regresar al formulario con errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)           // Pasar los errores de validación a la vista
                ->withInput();                     // Mantener los datos ingresados por el usuario
        }

        try {
            // ENVÍO DEL FORMULARIO A WEB3FORMS
            // Web3Forms es un servicio externo que maneja el envío de emails
            $response = Http::post('https://api.web3forms.com/submit', [
                'access_key' => env('WEB3FORMS_ACCESS_KEY', 'YOUR_ACCESS_KEY_HERE'), // Clave API desde archivo .env
                'name' => $request->name,                                           // Nombre del remitente
                'email' => $request->email,                                         // Email del remitente
                'subject' => $request->subject,                                     // Asunto del mensaje
                'message' => $request->message,                                     // Contenido del mensaje
                'from_name' => 'Sistema de Reservas Universitario',                // Identificador del sistema
            ]);

            // Obtener la respuesta JSON del servicio Web3Forms
            $result = $response->json();

            // VERIFICAR SI EL ENVÍO FUE EXITOSO
            if ($result['success']) {
                // Si el envío fue exitoso, regresar con mensaje de confirmación
                return redirect()->back()->with('success', 'Hemos recibido tu mensaje. Te responderemos lo antes posible.');
            } else {
                // Si Web3Forms reporta error, lanzar excepción para manejar en catch
                throw new \Exception('Error al enviar el formulario');
            }
        } catch (\Exception $e) {
            // MANEJO DE ERRORES
            // En caso de cualquier error (conexión, servicio, etc.)
            return redirect()->back()
                ->with('error', 'No se pudo enviar el mensaje. Por favor, inténtalo de nuevo más tarde.')
                ->withInput();                     // Mantener los datos del formulario para el usuario
        }
    }
}