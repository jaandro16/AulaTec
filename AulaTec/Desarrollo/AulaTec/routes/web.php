<?php

/**
 * ======= ARCHIVO DE RUTAS WEB =======
 * Define todas las rutas HTTP de la aplicación
 * Organizado por: públicas, guest-only, protegidas y admin
 */

// ======= IMPORTACIÓN DE CONTROLADORES =======
// Controladores de autenticación y usuarios
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Usuarios;

// Controladores de funcionalidad principal
use App\Http\Controllers\ClassDetailsController;
use App\Http\Controllers\ClassSessionController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TimeSlotController;
use App\Http\Controllers\DashboardController;

// Controladores de administración (agrupados en namespace admin)
use App\Http\Controllers\admin\CrearClase;
use App\Http\Controllers\admin\Asistencias;
use App\Http\Controllers\admin\Justificantes;
use App\Http\Controllers\admin\InfoClase;
use App\Http\Controllers\admin\AdminQR;
use App\Http\Controllers\admin\CrearProfesor;

use Illuminate\Support\Facades\Route;


// ======= RUTAS PÚBLICAS ======= 
// Accesibles sin autenticación

// Página de inicio de la aplicación
Route::get('/', function () {
    return view('modules.dashboard.home');
})->name('home');


// Página de contacto (GET y POST para formulario)
Route::get('/contacto', [App\Http\Controllers\ContactController::class, 'index'])->name('contacto.index');
Route::post('/contacto', [App\Http\Controllers\ContactController::class, 'enviar'])->name('contacto.enviar');


// ======= RUTAS SOLO PARA USUARIOS NO AUTENTICADOS ======= 
// Middleware 'guest': solo accesibles si NO hay sesión activa
Route::middleware(['guest'])->group(function () {
    
    // ======= REGISTRO DE NUEVOS USUARIOS =======
    Route::get('/registro', [Usuarios::class, 'create'])->name('registro.create');
    Route::post('/registro', [Usuarios::class, 'store'])->name('registro.store');
    
    
    // ======= INICIO DE SESIÓN =======
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login/post', [AuthController::class, 'login'])->name('login.post');
});

// ======= RUTAS PROTEGIDAS POR AUTENTICACIÓN ======= 
// Middleware 'auth': requiere sesión activa
Route::middleware(['auth'])->group(function () {
    
    
    // ======= AUTENTICACIÓN Y SESIÓN =======
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    
    // ======= DASHBOARD PRINCIPAL =======
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    
    // ======= GESTIÓN DE PERFIL DE USUARIO =======
    Route::get('/perfil', [Usuarios::class, 'show'])->name('perfil.show'); 
    Route::put('/perfil/update', [Usuarios::class, 'update'])->name('perfil.update');
    Route::put('/perfil/update-password', [Usuarios::class, 'updatePassword'])->name('perfil.update-password');

    
    // ======= APIS PARA OBTENER DATOS ======= 
    // Endpoints que devuelven JSON para componentes frontend
    
    // Obtener franjas horarias disponibles
    Route::get('/api/time-slots', [TimeSlotController::class, 'getTimeSlots'])->name('time-slots.get');
    
    // Obtener clases disponibles para reservar
    Route::get('/api/available-classes', [ClassSessionController::class, 'getAvailableClasses'])->name('classes.available');

    
    // ======= SELECCIÓN DE ASIENTOS ======= 
    // Flujo completo: mostrar mapa → confirmar → ver confirmación
    
    // Mostrar página de selección con mapa del aula
    Route::get('/seleccion-asientos/{token}', [ClassDetailsController::class, 'show'])->name('seleccion-asientos.show');
    
    // Procesar reserva del asiento seleccionado
    Route::post('/seleccion-asientos/{token}/confirmar', [ClassDetailsController::class, 'store'])->name('seleccion-asientos.store');
    
    // Página de confirmación exitosa con QR
    Route::get('/seleccion-asientos/confirmacion/{reservation}', [ClassDetailsController::class, 'confirmation'])->name('seleccion-asientos.confirmation');

    
    // ======= GENERACIÓN DE PDF =======
    // Descargar comprobante de reserva en PDF
    Route::post('/reservations/pdf', [PdfController::class, 'downloadPdf'])->name('reservations.download-pdf');

    
    // ======= GESTIÓN DE RESERVAS ======= 
    // CRUD completo y consultas específicas
    
    // Obtener reservas activas del usuario autenticado
    Route::get('/api/user/reservations', [ReservationController::class, 'getUserReservations'])->name('user.reservations');
    
    // Eliminar/cancelar una reserva específica
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

    // Obtener historial completo de reservas
    Route::get('/api/reservations/historial', [ReservationController::class, 'getHistorialReservations'])->name('reservations.historial');

    // Obtener clases perdidas/no asistidas
    Route::get('/api/reservations/missed', [ReservationController::class, 'getMissedClasses'])->name('reservations.missed');

    // Subir justificante para ausencia
    Route::post('/reservations/{reservation}/justificante', [ReservationController::class, 'uploadJustificante'])->name('reservations.justificante.upload');

    // Obtener reservas activas disponibles para intercambio
    Route::get('/api/reservations/active', [ReservationController::class, 'getActiveReservations'])->name('reservations.active');

    // Obtener reservas que pueden ser intercambiadas
    Route::get('/api/reservations/available-for-exchange', [ReservationController::class, 'getAvailableForExchange']);

    
    // ======= SISTEMA DE INTERCAMBIOS ======= 
    // Funcionalidad para intercambiar asientos entre estudiantes
    
    // Obtener intercambios activos/disponibles
    Route::get('/api/exchanges/active', [ExchangeController::class, 'getActiveExchanges']);

    // Obtener lista de asignaturas para filtros
    Route::get('/api/subjects', [ExchangeController::class, 'getSubjects']);

    // Crear nueva oferta de intercambio
    Route::post('/api/exchanges', [ExchangeController::class, 'store'])->name('exchanges.store');
    
    // Crear solicitud de intercambio a una oferta existente
    Route::post('/api/exchange-requests', [ExchangeController::class, 'storeRequest'])->name('exchange-requests.store');

    // Obtener ofertas de intercambio creadas por el usuario
    Route::get('/api/exchanges/user-posts', [ExchangeController::class, 'getUserExchangePosts'])->name('exchanges.user-posts');

    // Eliminar oferta de intercambio
    Route::delete('/api/exchanges/{id}', [ExchangeController::class, 'destroy'])->name('exchanges.destroy');

    // Aceptar solicitud de intercambio recibida
    Route::patch('/api/exchange-requests/{id}/accept', [ExchangeController::class, 'acceptRequest']);

    // Verificar estado de reserva para intercambio
    Route::get('/api/exchanges/check-reservation/{reservationId}', [ExchangeController::class, 'checkReservation']);

    // Obtener solicitudes de intercambio enviadas por el usuario
    Route::get('/api/exchange-requests/my-requests', [ExchangeController::class, 'getMyRequests'])->name('exchange-requests.my-requests');

    // Rechazar solicitud de intercambio recibida
    Route::patch('/api/exchange-requests/{id}/reject', [ExchangeController::class, 'rejectRequest']);

    // Eliminar solicitud de intercambio enviada
    Route::delete('/api/exchange-requests/{id}', [ExchangeController::class, 'destroyRequest']);
    
    // ======= RUTAS DE ADMINISTRACIÓN ======= 
    // Funcionalidades exclusivas para profesores y administradores
    
    // ======= CREACIÓN DE CLASES =======
    // Panel para que profesores programen nuevas sesiones
    Route::prefix('crear-clase')->group(function () {
        // Mostrar formulario de creación de clase
        Route::get('/create', [CrearClase::class, 'create'])->name('admin.crear-clase.create');
        // Procesar y guardar nueva clase
        Route::post('/store', [CrearClase::class, 'store'])->name('admin.crear-clase.store');
    });

    // ======= GESTIÓN DE JUSTIFICANTES =======
    // Revisar y aprobar/rechazar justificantes de ausencias
    Route::prefix('justificantes')->group(function () {
        // Listar justificantes pendientes de revisión
        Route::get('/index', [Justificantes::class, 'index'])->name('admin.justificantes.index');
    });

    // ======= CONTROL DE ASISTENCIAS =======
    // Gestión completa de presencia en clases
    Route::prefix('asistencias')->group(function () {
        // Panel principal de asistencias
        Route::get('/index', [Asistencias::class, 'index'])->name('admin.asistencias.index');
        // Aprobar justificante específico
        Route::post('/justificantes/{reservation}/justificar', [Justificantes::class, 'justificar'])->name('justificantes.justificar');
    });

    // ======= INFORMACIÓN DE CLASES =======
    // Consultar detalles y gestionar clases existentes
    Route::prefix('infoclase')->group(function () {
        // Listar todas las clases
        Route::get('/index', [InfoClase::class, 'index'])->name('admin.infoclase.index');
        // Obtener detalles específicos de una clase
        Route::get('/getDetalles/{id}', [InfoClase::class, 'getDetalles'])->name('admin.infoclase.getDetalles');
        // Eliminar clase específica
        Route::delete('/delete/{id}', [InfoClase::class, 'destroy'])->name('admin.infoclase.destroy');
    });

    // ======= SCANNER QR PARA ASISTENCIA =======
    // Herramienta para verificar presencia mediante códigos QR
    Route::prefix('admin-qr')->group(function () {
        // Panel principal del scanner
        Route::get('/index', [AdminQR::class, 'index'])->name('admin.admin-qr.index');
        // Procesar código QR escaneado
        Route::post('/process', [AdminQR::class, 'processQR'])->name('admin.qr.process');
        // Confirmar asistencia del estudiante
        Route::post('/registrar-asistencia', [AdminQR::class, 'registrarAsistencia'])->name('admin.qr.registrar-asistencia');
    });

    // ======= GESTIÓN DE PROFESORES =======
    // CRUD para cuentas de profesores y perfil
    Route::prefix('crear-profesor')->group(function () {
        // Mostrar formulario de creación de profesor
        Route::get('/create', [CrearProfesor::class, 'create'])->name('admin.crear-profesor.create');
        // Procesar y crear nueva cuenta de profesor
        Route::post('/store', [CrearProfesor::class, 'store'])->name('admin.crear-profesor.store');
        // Mostrar perfil del profesor autenticado
        Route::get('/edit', [CrearProfesor::class, 'edit'])->name('admin.crear-profesor.edit');
        // Actualizar datos del perfil de profesor
        Route::put('/perfil/update', [Usuarios::class, 'update'])->name('perfil.update');
        // Cambiar contraseña del profesor
        Route::put('/update-password', [Usuarios::class, 'updatePassword'])->name('profesor.update-password');
    });
});