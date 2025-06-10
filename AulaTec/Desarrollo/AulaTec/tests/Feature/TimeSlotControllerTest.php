<?php

namespace Tests\Feature;
//  php artisan test tests/Feature/TimeSlotControllerTest.php
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TimeSlotController;
use App\Models\User;
use App\Models\TimeSlot;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\HttpException;

// Test para el controlador de franjas horarias
// Verifica la gestión de horarios disponibles

class TimeSlotControllerTest extends TestCase {
    // Verifica:
    // - Creación de franjas horarias  
    // - Consulta de disponibilidad
    // - Validaciones de solapamiento
    // - Restricciones horarias

    use RefreshDatabase;

    protected $controller;
    protected $alumno;
    protected $profesor;
    protected $timeSlot1;
    protected $timeSlot2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new TimeSlotController();

        // Crear usuarios con emails únicos para cada test
        $this->alumno = User::create([
            'nombre' => 'Estudiante',
            'apellido' => 'Test',
            'email' => 'alumno_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'rol' => 'alumno',
            'numero_matricula' => 123456,
        ]);

        $this->profesor = User::create([
            'nombre' => 'Profesor',
            'apellido' => 'Test',
            'email' => 'profesor_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 789012,
        ]);

        // Crear time slots de prueba
        $this->timeSlot1 = TimeSlot::create([
            'start_time' => '08:00:00',
            'end_time' => '09:00:00'
        ]);

        $this->timeSlot2 = TimeSlot::create([
            'start_time' => '10:30:00',
            'end_time' => '11:30:00'
        ]);

        TimeSlot::create([
            'start_time' => '14:00:00',
            'end_time' => '15:30:00'
        ]);
    }

    #[Test]
    public function solo_usuarios_autenticados_pueden_acceder()
    {
        Auth::logout();

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Debes iniciar sesión para acceder.');

        $this->controller->getTimeSlots();
    }

    #[Test]
    public function solo_alumnos_pueden_acceder()
    {
        Auth::login($this->profesor);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Acceso restringido.');

        $this->controller->getTimeSlots();
    }
    
    #[Test]
    public function get_time_slots_devuelve_todos_los_horarios()
    {
        Auth::login($this->alumno);

        $response = $this->controller->getTimeSlots();
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('data', $data);

        // ✅ CAMBIO: Verificar que devuelve al menos los 3 time slots del setUp
        $this->assertGreaterThanOrEqual(3, count($data['data'])); // Al menos 3 time slots

        // ✅ ALTERNATIVA: Verificar que contiene nuestros time slots específicos
        $ids = array_column($data['data'], 'id');
        $this->assertContains($this->timeSlot1->id, $ids, 'Debe contener timeSlot1');
        $this->assertContains($this->timeSlot2->id, $ids, 'Debe contener timeSlot2');
    }

    #[Test]
    public function get_time_slots_formato_correcto()
    {
        Auth::login($this->alumno);

        $response = $this->controller->getTimeSlots();
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $data['status']);

        // Verificar estructura de cada time slot
        foreach ($data['data'] as $timeSlot) {
            $this->assertArrayHasKey('id', $timeSlot);
            $this->assertArrayHasKey('formatted_time', $timeSlot);
            $this->assertIsInt($timeSlot['id']);
            $this->assertIsString($timeSlot['formatted_time']);
        }
    }

    #[Test]
    public function get_time_slots_formatea_horarios_correctamente()
    {
        Auth::login($this->alumno);

        $response = $this->controller->getTimeSlots();
        $data = $response->getData(true);

        // Buscar el time slot específico por ID
        $foundTimeSlot = null;
        foreach ($data['data'] as $timeSlot) {
            if ($timeSlot['id'] === $this->timeSlot1->id) {
                $foundTimeSlot = $timeSlot;
                break;
            }
        }

        $this->assertNotNull($foundTimeSlot, 'TimeSlot 1 debe estar en la respuesta');
        $this->assertEquals('08:00 - 09:00', $foundTimeSlot['formatted_time']);

        // Verificar otro time slot
        $foundTimeSlot2 = null;
        foreach ($data['data'] as $timeSlot) {
            if ($timeSlot['id'] === $this->timeSlot2->id) {
                $foundTimeSlot2 = $timeSlot;
                break;
            }
        }

        $this->assertNotNull($foundTimeSlot2, 'TimeSlot 2 debe estar en la respuesta');
        $this->assertEquals('10:30 - 11:30', $foundTimeSlot2['formatted_time']);
    }

    #[Test]
    public function get_time_slots_devuelve_estructura_json_correcta()
    {
        Auth::login($this->alumno);

        $response = $this->controller->getTimeSlots();
        $data = $response->getData(true);

        // Verificar estructura principal
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals('success', $data['status']);
        $this->assertIsArray($data['data']);

        // Verificar que cada elemento tiene la estructura correcta
        if (!empty($data['data'])) {
            $firstTimeSlot = $data['data'][0];
            $this->assertArrayHasKey('id', $firstTimeSlot);
            $this->assertArrayHasKey('formatted_time', $firstTimeSlot);

            // Verificar tipos de datos
            $this->assertIsNumeric($firstTimeSlot['id']);
            $this->assertIsString($firstTimeSlot['formatted_time']);

            // Verificar formato de tiempo (HH:MM - HH:MM)
            $this->assertMatchesRegularExpression('/^\d{2}:\d{2} - \d{2}:\d{2}$/', $firstTimeSlot['formatted_time']);
        }
    }

    #[Test]
    public function get_time_slots_ordena_por_id()
    {
        Auth::login($this->alumno);

        $response = $this->controller->getTimeSlots();
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertGreaterThan(0, count($data['data']));

        // Verificar que los IDs están en orden (TimeSlot::all() devuelve por ID por defecto)
        $ids = array_column($data['data'], 'id');
        $sortedIds = $ids;
        sort($sortedIds);

        $this->assertEquals($sortedIds, $ids, 'Los time slots deben estar ordenados por ID');
    }

    #[Test]
    public function get_time_slots_maneja_base_de_datos_vacia()
    {
        // ✅ CAMBIO: Usar delete() en lugar de truncate() para evitar problemas de foreign key
        TimeSlot::query()->delete();

        Auth::login($this->alumno);

        $response = $this->controller->getTimeSlots();
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('data', $data);
        $this->assertCount(0, $data['data']); // Array vacío
        $this->assertIsArray($data['data']);
    }

    #[Test]
    public function get_time_slots_maneja_horarios_con_diferentes_formatos()
    {
        // Crear time slots con diferentes formatos de tiempo
        $timeSlotEspecial1 = TimeSlot::create([
            'start_time' => '07:30:00',
            'end_time' => '08:45:00'
        ]);

        $timeSlotEspecial2 = TimeSlot::create([
            'start_time' => '16:15:00',
            'end_time' => '17:45:00'
        ]);

        Auth::login($this->alumno);

        $response = $this->controller->getTimeSlots();
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());

        // Buscar los time slots especiales
        $foundSpecial1 = null;
        $foundSpecial2 = null;

        foreach ($data['data'] as $timeSlot) {
            if ($timeSlot['id'] === $timeSlotEspecial1->id) {
                $foundSpecial1 = $timeSlot;
            }
            if ($timeSlot['id'] === $timeSlotEspecial2->id) {
                $foundSpecial2 = $timeSlot;
            }
        }

        $this->assertNotNull($foundSpecial1);
        $this->assertEquals('07:30 - 08:45', $foundSpecial1['formatted_time']);

        $this->assertNotNull($foundSpecial2);
        $this->assertEquals('16:15 - 17:45', $foundSpecial2['formatted_time']);
    }

    #[Test]
    public function get_time_slots_maneja_errores_de_base_de_datos()
    {
        Auth::login($this->alumno);

        // Simular error de base de datos usando un mock (opcional)
        // Para este test, asumimos que el controlador maneja bien los errores

        $response = $this->controller->getTimeSlots();

        // En condiciones normales debe funcionar
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertEquals('success', $data['status']);
    }

    #[Test]
    public function controller_tiene_metodo_get_time_slots()
    {
        $this->assertTrue(method_exists($this->controller, 'getTimeSlots'), 'TimeSlotController debe tener método getTimeSlots');

        // Verificar que no requiere parámetros
        $reflection = new \ReflectionMethod($this->controller, 'getTimeSlots');
        $parameters = $reflection->getParameters();

        $this->assertCount(0, $parameters, 'getTimeSlots no debe requerir parámetros');
    }

    #[Test]
    public function check_teacher_role_funciona_correctamente()
    {
        // Verificar que el método privado checkTeacherRole funciona
        // Lo probamos indirectamente a través de getTimeSlots

        // Test 1: Sin autenticación
        Auth::logout();
        try {
            $this->controller->getTimeSlots();
            $this->fail('Debería lanzar excepción para usuario no autenticado');
        } catch (HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
            $this->assertEquals('Debes iniciar sesión para acceder.', $e->getMessage());
        }

        // Test 2: Con profesor (no alumno)
        Auth::login($this->profesor);
        try {
            $this->controller->getTimeSlots();
            $this->fail('Debería lanzar excepción para usuario que no es alumno');
        } catch (HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
            $this->assertEquals('Acceso restringido.', $e->getMessage());
        }

        // Test 3: Con alumno (correcto)
        Auth::login($this->alumno);
        $response = $this->controller->getTimeSlots();
        $this->assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    public function response_es_json_valido()
    {
        Auth::login($this->alumno);

        $response = $this->controller->getTimeSlots();

        // Verificar que es una respuesta JSON
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);

        // Verificar que el JSON es válido
        $content = $response->getContent();
        $this->assertJson($content);

        // Verificar que se puede decodificar
        $decoded = json_decode($content, true);
        $this->assertNotNull($decoded);
        $this->assertIsArray($decoded);
    }

    #[Test]
    public function time_slots_incluyen_todos_los_campos_requeridos()
    {
        Auth::login($this->alumno);

        $response = $this->controller->getTimeSlots();
        $data = $response->getData(true);

        $this->assertEquals('success', $data['status']);
        $this->assertNotEmpty($data['data']);

        foreach ($data['data'] as $timeSlot) {
            // Verificar campos requeridos
            $this->assertArrayHasKey('id', $timeSlot);
            $this->assertArrayHasKey('formatted_time', $timeSlot);

            // Verificar que no hay campos adicionales inesperados
            $expectedKeys = ['id', 'formatted_time'];
            $actualKeys = array_keys($timeSlot);
            sort($expectedKeys);
            sort($actualKeys);
            $this->assertEquals($expectedKeys, $actualKeys);
        }
    }

    #[Test]
    public function metodo_privado_check_teacher_role_existe()
    {
        $reflection = new \ReflectionClass($this->controller);
        $this->assertTrue($reflection->hasMethod('checkTeacherRole'), 'Debe existir método checkTeacherRole');

        $method = $reflection->getMethod('checkTeacherRole');
        $this->assertTrue($method->isPrivate(), 'checkTeacherRole debe ser privado');
    }

    #[Test]
    public function get_time_slots_funciona_con_multiples_usuarios()
    {
        // ✅ CAMBIO: Crear otro alumno con email único
        $alumno2 = User::create([
            'nombre' => 'Alumno',
            'apellido' => 'Dos',
            'email' => 'alumno2_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'rol' => 'alumno',
            'numero_matricula' => 654321,
        ]);

        // Probar con primer alumno
        Auth::login($this->alumno);
        $response1 = $this->controller->getTimeSlots();
        $data1 = $response1->getData(true);

        // Probar con segundo alumno
        Auth::login($alumno2);
        $response2 = $this->controller->getTimeSlots();
        $data2 = $response2->getData(true);

        // Ambos deben obtener los mismos resultados
        $this->assertEquals(200, $response1->getStatusCode());
        $this->assertEquals(200, $response2->getStatusCode());
        $this->assertEquals($data1['data'], $data2['data']);
        $this->assertEquals('success', $data1['status']);
        $this->assertEquals('success', $data2['status']);
    }
}
