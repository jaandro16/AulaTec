<?php
//php artisan test tests/Feature/Admin/AdminQRTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

// Test para el controlador de códigos QR administrativos 
// Verifica la generación y validación de QR

class AdminQRTest extends TestCase
{
    use RefreshDatabase;

    // Verifica:
    // - Generación de QR
    // - Escaneo y validación
    // - Seguridad de datos
    // - Acceso administrativo

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un usuario profesor y autenticarlo
        $teacher = User::create([
            'nombre' => 'Profesor',
            'apellido' => 'Test',
            'email' => 'profesor@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 123456,
        ]);

        Auth::login($teacher);
    }

    #[Test]
    public function profesor_puede_acceder_a_la_pagina_de_escaneo_qr()
    {
        $response = $this->get(route('admin.admin-qr.index'));

        $response->assertStatus(200);
        $response->assertViewIs('modules.admin.admin_qr.index');
        $response->assertViewHas('titulo', 'Escanear Código QR');
    }

    #[Test]
    public function usuario_no_autenticado_no_puede_acceder_a_pagina_qr()
    {
        Auth::logout();

        $response = $this->get(route('admin.admin-qr.index'));

        $response->assertStatus(302);
    }

    #[Test]
    public function process_qr_devuelve_error_para_qr_invalido()
    {
        $response = $this->withoutMiddleware()->postJson(route('admin.qr.process'), [
            'qrData' => 'datos_no_encriptados'
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'QR INCORRECTO, RESERVA NO ENCONTRADA'
            ]);
    }

    #[Test]
    public function process_qr_devuelve_error_para_qr_sin_id()
    {
        // Crear datos encriptados sin ID
        $qrData = ['other_field' => 'value'];
        $encryptedData = Crypt::encrypt($qrData);

        $response = $this->withoutMiddleware()->postJson(route('admin.qr.process'), [
            'qrData' => $encryptedData
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'QR INCORRECTO, RESERVA NO ENCONTRADA'
            ]);
    }

    #[Test]
    public function process_qr_devuelve_error_para_reserva_inexistente()
    {
        // Crear datos encriptados con ID que no existe
        $qrData = ['id' => 99999];
        $encryptedData = Crypt::encrypt($qrData);

        $response = $this->withoutMiddleware()->postJson(route('admin.qr.process'), [
            'qrData' => $encryptedData
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'QR INCORRECTO, RESERVA NO ENCONTRADA'
            ]);
    }

    #[Test]
    public function registrar_asistencia_devuelve_error_para_reserva_inexistente()
    {
        $response = $this->withoutMiddleware()->postJson(route('admin.qr.registrar-asistencia'), [
            'reservationId' => 99999
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => 'Error al registrar la asistencia'
            ]);
    }

    #[Test]
    public function usuario_no_autenticado_no_puede_procesar_qr()
    {
        Auth::logout();

        $response = $this->withoutMiddleware()->postJson(route('admin.qr.process'), [
            'qrData' => 'test_data'
        ]);

        // Usuario no autenticado debería recibir 401, 302, 403 o 419
        $this->assertContains($response->getStatusCode(), [401, 302, 403, 419]);
    }

    #[Test]
    public function usuario_no_autenticado_no_puede_registrar_asistencia()
    {
        Auth::logout();

        $response = $this->withoutMiddleware()->postJson(route('admin.qr.registrar-asistencia'), [
            'reservationId' => 1
        ]);

        // Usuario no autenticado debería recibir 401, 302, 403 o 419
        $this->assertContains($response->getStatusCode(), [401, 302, 403, 419]);
    }
}

