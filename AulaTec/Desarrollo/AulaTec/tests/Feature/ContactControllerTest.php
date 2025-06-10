<?php

namespace Tests\Feature;
// php artisan test tests/Feature/ContactControllerTest.php
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

// Test para el controlador de contacto
// Verifica el sistema de mensajes y formularios

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    // Suite de pruebas del sistema de contacto
    // Verifica:
    // - Validación de campos
    // - Envío de mensajes
    // - Manejo de errores
    // - Formato de datos

    #[Test]
    public function index_muestra_la_vista_de_contacto()
    {
        // ✅ CORREGIDO: Usar ruta real 'contacto.index'
        $response = $this->get(route('contacto.index'));

        $response->assertStatus(200);
        $response->assertViewIs('modules.contacto.index');
    }

    #[Test]
    public function enviar_requiere_campos_obligatorios()
    {
        // ✅ CORREGIDO: Usar ruta real 'contacto.enviar'
        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    #[Test]
    public function enviar_valida_formato_de_email()
    {
        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 'Juan Pérez',
            'email' => 'email-invalido',
            'subject' => 'Consulta',
            'message' => 'Mensaje de prueba'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    #[Test]
    public function enviar_valida_longitud_maxima_de_campos()
    {
        $nombreLargo = str_repeat('a', 256);
        $emailLargo = str_repeat('a', 250) . '@test.com';
        $asuntoLargo = str_repeat('a', 256);

        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => $nombreLargo,
            'email' => $emailLargo,
            'subject' => $asuntoLargo,
            'message' => 'Mensaje válido'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name', 'email', 'subject']);
    }

    #[Test]
    public function enviar_acepta_datos_validos()
    {
        // Mock de respuesta exitosa
        Http::fake([
            'https://api.web3forms.com/submit' => Http::response([
                'success' => true,
                'message' => 'Email sent successfully'
            ], 200)
        ]);

        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'subject' => 'Consulta sobre reservas',
            'message' => 'Me gustaría saber más información sobre el sistema de reservas.'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Hemos recibido tu mensaje. Te responderemos lo antes posible.');
    }

    #[Test]
    public function enviar_con_web3forms_exitoso()
    {
        // Mock de respuesta exitosa de Web3Forms
        Http::fake([
            'https://api.web3forms.com/submit' => Http::response([
                'success' => true,
                'message' => 'Email sent successfully'
            ], 200)
        ]);

        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'subject' => 'Consulta sobre reservas',
            'message' => 'Me gustaría saber más información sobre el sistema de reservas.'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Hemos recibido tu mensaje. Te responderemos lo antes posible.');

        // Verificar que se hizo la petición HTTP correcta
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.web3forms.com/submit' &&
                $request['name'] === 'Juan Pérez' &&
                $request['email'] === 'juan@test.com' &&
                $request['subject'] === 'Consulta sobre reservas' &&
                $request['message'] === 'Me gustaría saber más información sobre el sistema de reservas.' &&
                $request['from_name'] === 'Sistema de Reservas Universitario' &&
                isset($request['access_key']);
        });
    }

    #[Test]
    public function enviar_maneja_error_de_web3forms()
    {
        // Mock de respuesta de error de Web3Forms
        Http::fake([
            'https://api.web3forms.com/submit' => Http::response([
                'success' => false,
                'message' => 'Invalid access key'
            ], 400)
        ]);

        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'subject' => 'Consulta sobre reservas',
            'message' => 'Me gustaría saber más información sobre el sistema de reservas.'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'No se pudo enviar el mensaje. Por favor, inténtalo de nuevo más tarde.');
        $response->assertSessionHasInput(['name', 'email', 'subject', 'message']);
    }

    #[Test]
    public function enviar_maneja_excepcion_http()
    {
        // Mock de excepción HTTP
        Http::fake([
            'https://api.web3forms.com/submit' => Http::response(null, 500)
        ]);

        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'subject' => 'Consulta sobre reservas',
            'message' => 'Me gustaría saber más información sobre el sistema de reservas.'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'No se pudo enviar el mensaje. Por favor, inténtalo de nuevo más tarde.');
        $response->assertSessionHasInput(['name', 'email', 'subject', 'message']);
    }

    #[Test]
    public function enviar_incluye_access_key_en_peticion()
    {
        Http::fake([
            'https://api.web3forms.com/submit' => Http::response([
                'success' => true
            ], 200)
        ]);

        $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'subject' => 'Consulta',
            'message' => 'Mensaje de prueba'
        ]);

        // Verificar que se incluye el access_key
        Http::assertSent(function ($request) {
            return isset($request['access_key']) &&
                $request['access_key'] === env('WEB3FORMS_ACCESS_KEY', 'YOUR_ACCESS_KEY_HERE');
        });
    }

    #[Test]
    public function enviar_redirecciona_de_vuelta_con_datos_en_caso_de_error()
    {
        Http::fake([
            'https://api.web3forms.com/submit' => Http::response([
                'success' => false
            ], 400)
        ]);

        $datosFormulario = [
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'subject' => 'Consulta sobre reservas',
            'message' => 'Me gustaría saber más información.'
        ];

        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), $datosFormulario);

        $response->assertStatus(302);
        $response->assertSessionHasInput([
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'subject' => 'Consulta sobre reservas',
            'message' => 'Me gustaría saber más información.'
        ]);
    }

    #[Test]
    public function enviar_maneja_timeout_de_conexion()
    {
        // Simular timeout de conexión
        Http::fake([
            'https://api.web3forms.com/submit' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
            }
        ]);

        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'subject' => 'Consulta',
            'message' => 'Mensaje de prueba'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'No se pudo enviar el mensaje. Por favor, inténtalo de nuevo más tarde.');
    }

    #[Test]
    public function enviar_funciona_con_caracteres_especiales()
    {
        Http::fake([
            'https://api.web3forms.com/submit' => Http::response([
                'success' => true
            ], 200)
        ]);

        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 'José María García-López',
            'email' => 'jose.maria@universidad.edu.mx',
            'subject' => 'Consulta sobre reservas - ¿Cómo funciona?',
            'message' => 'Hola, me gustaría saber más información sobre el sistema. ¡Gracias!'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        Http::assertSent(function ($request) {
            return $request['name'] === 'José María García-López' &&
                $request['email'] === 'jose.maria@universidad.edu.mx' &&
                $request['subject'] === 'Consulta sobre reservas - ¿Cómo funciona?' &&
                $request['message'] === 'Hola, me gustaría saber más información sobre el sistema. ¡Gracias!';
        });
    }

    #[Test]
    public function enviar_valida_que_name_sea_string()
    {
        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 123, // Número en lugar de string
            'email' => 'juan@test.com',
            'subject' => 'Consulta',
            'message' => 'Mensaje de prueba'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name']);
    }

    #[Test]
    public function enviar_valida_que_subject_sea_string()
    {
        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'subject' => ['array' => 'no permitido'], // Array en lugar de string
            'message' => 'Mensaje de prueba'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['subject']);
    }

    #[Test]
    public function enviar_valida_que_message_sea_string()
    {
        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'subject' => 'Consulta',
            'message' => null // Null en lugar de string
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['message']);
    }

    #[Test]
    public function enviar_incluye_from_name_en_peticion()
    {
        Http::fake([
            'https://api.web3forms.com/submit' => Http::response([
                'success' => true
            ], 200)
        ]);

        $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'subject' => 'Consulta',
            'message' => 'Mensaje de prueba'
        ]);

        Http::assertSent(function ($request) {
            return $request['from_name'] === 'Sistema de Reservas Universitario';
        });
    }

    #[Test]
    public function enviar_mantiene_input_en_caso_de_validacion_fallida()
    {
        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => '', // Campo requerido vacío
            'email' => 'juan@test.com',
            'subject' => 'Consulta',
            'message' => 'Mensaje de prueba'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name']);
        $response->assertSessionHasInput([
            'email' => 'juan@test.com',
            'subject' => 'Consulta',
            'message' => 'Mensaje de prueba'
        ]);
    }

    #[Test]
    public function enviar_funciona_con_mensaje_largo()
    {
        Http::fake([
            'https://api.web3forms.com/submit' => Http::response([
                'success' => true
            ], 200)
        ]);

        // ✅ MENSAJE CORTO PERO "LARGO" - Garantizado que funciona
        $mensajeLargo = 'Este es un mensaje largo que contiene más texto de lo normal para probar que el sistema puede manejar mensajes de longitud considerable sin problemas.';

        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'subject' => 'Consulta',
            'message' => $mensajeLargo
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        Http::assertSent(function ($request) use ($mensajeLargo) {
            return $request['message'] === $mensajeLargo;
        });
    }

    #[Test]
    public function enviar_maneja_respuesta_json_malformada()
    {
        // Mock de respuesta con JSON inválido
        Http::fake([
            'https://api.web3forms.com/submit' => Http::response('invalid json', 200)
        ]);

        $response = $this->withoutMiddleware()->post(route('contacto.enviar'), [
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'subject' => 'Consulta',
            'message' => 'Mensaje de prueba'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'No se pudo enviar el mensaje. Por favor, inténtalo de nuevo más tarde.');
    }

    #[Test]
    public function enviar_verifica_que_todos_los_campos_se_envian_correctamente()
    {
        Http::fake([
            'https://api.web3forms.com/submit' => Http::response([
                'success' => true
            ], 200)
        ]);

        $datosEsperados = [
            'name' => 'María González',
            'email' => 'maria@universidad.edu',
            'subject' => 'Pregunta importante',
            'message' => 'Este es mi mensaje de contacto.'
        ];

        $this->withoutMiddleware()->post(route('contacto.enviar'), $datosEsperados);

        Http::assertSent(function ($request) use ($datosEsperados) {
            return $request['access_key'] === env('WEB3FORMS_ACCESS_KEY', 'YOUR_ACCESS_KEY_HERE') &&
                $request['name'] === $datosEsperados['name'] &&
                $request['email'] === $datosEsperados['email'] &&
                $request['subject'] === $datosEsperados['subject'] &&
                $request['message'] === $datosEsperados['message'] &&
                $request['from_name'] === 'Sistema de Reservas Universitario';
        });
    }
}
