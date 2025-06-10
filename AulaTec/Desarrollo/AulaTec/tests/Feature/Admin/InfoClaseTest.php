<?php
namespace Tests\Feature;
// php artisan test tests/Feature/Admin/InfoClaseTest.php

use Tests\TestCase;
use App\Models\User;
use App\Models\ClassSession;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\TimeSlot;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Test para el controlador de información de clases
// Verifica la consulta de información detallada de clases

class InfoClaseTest extends TestCase {
    // Verifica:
    // - Consulta de detalles
    // - Lista de asistentes
    // - Estadísticas
    // - Permisos de acceso

    use RefreshDatabase;

    protected $teacher;
    protected $subject;
    protected $classroom;
    protected $timeSlot;

    protected function setUp(): void
    {
        parent::setUp();

        // Configurar Carbon en español
        Carbon::setLocale('es');

        // Crear un usuario profesor
        $this->teacher = User::create([
            'nombre' => 'Profesor',
            'apellido' => 'Test',
            'email' => 'profesor@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 123456,
        ]);

        // Crear datos base para las clases
        $this->subject = Subject::create([
            'name' => 'Matemáticas',
            'code' => 'MAT001',
            'description' => 'Matemáticas básicas'
        ]);

        $this->classroom = Classroom::create([
            'name' => 'Aula 101',
            'capacity' => 30
        ]);

        $this->timeSlot = TimeSlot::create([
            'start_time' => '09:00:00',
            'end_time' => '10:00:00'
        ]);
    }

    /** @test */
    public function profesor_puede_acceder_a_info_clases()
    {
        Auth::login($this->teacher);

        $response = $this->get(route('admin.infoclase.index'));

        $response->assertStatus(200);
        $response->assertViewIs('modules.admin.infoclase.index');
        $response->assertViewHas('titulo', 'Info Clase');
        $response->assertViewHas('clases');
    }

    /** @test */
    public function usuario_no_autenticado_es_redirigido_en_index()
    {
        $response = $this->get(route('admin.infoclase.index'));

        $response->assertStatus(302);
    }

    /** @test */
    public function index_muestra_solo_clases_del_profesor_autenticado()
    {
        Auth::login($this->teacher);

        // Crear otro profesor
        $otherTeacher = User::create([
            'nombre' => 'Otro',
            'apellido' => 'Profesor',
            'email' => 'otro@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 999999,
        ]);

        // Crear clases para ambos profesores
        $miClase = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
        ]);

        $otraClase = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $otherTeacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => Carbon::tomorrow()->addDay()->format('Y-m-d'),
        ]);

        $response = $this->get(route('admin.infoclase.index'));

        $response->assertStatus(200);

        // Verificar que solo se muestran las clases del profesor autenticado
        $clases = $response->viewData('clases');
        $this->assertCount(1, $clases);
        $this->assertEquals($miClase->id, $clases[0]['id']);
    }

    /** @test */
    public function index_muestra_solo_clases_futuras()
    {
        Auth::login($this->teacher);

        // Crear clases pasadas y futuras
        $clasePasada = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => Carbon::yesterday()->format('Y-m-d'),
        ]);

        $claseFutura = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
        ]);

        $response = $this->get(route('admin.infoclase.index'));

        $response->assertStatus(200);

        // Solo debe mostrar la clase futura
        $clases = $response->viewData('clases');
        $this->assertCount(1, $clases);
        $this->assertEquals($claseFutura->id, $clases[0]['id']);
    }

    /** @test */
    public function index_formatea_datos_correctamente()
    {
        Auth::login($this->teacher);

        $clase = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
        ]);

        $response = $this->get(route('admin.infoclase.index'));

        $response->assertStatus(200);

        $clases = $response->viewData('clases');
        $claseData = $clases[0];

        // Verificar formato de los datos
        $this->assertEquals($clase->id, $claseData['id']);
        $this->assertEquals('Matemáticas', $claseData['nombre']);
        $this->assertEquals('09:00', $claseData['hora_inicio']);
        $this->assertEquals('10:00', $claseData['hora_fin']);
        $this->assertEquals(60, $claseData['duracion']); // 60 minutos
        $this->assertEquals('Profesor Test', $claseData['profesor']);
        $this->assertEquals('Aula 101', $claseData['aula']);
    }

    /** @test */
    public function get_detalles_devuelve_informacion_completa()
    {
        Auth::login($this->teacher);

        $clase = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
        ]);

        // Crear estudiante y reserva
        $estudiante = User::create([
            'nombre' => 'Estudiante',
            'apellido' => 'Test',
            'email' => 'estudiante@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor', // Usar rol válido
            'numero_matricula' => 789012,
        ]);

        Reservation::create([
            'user_id' => $estudiante->id,
            'class_id' => $clase->id,
            'asiento' => 'A1',
            'estado' => 'Completada',
            'justificado' => false,
        ]);

        $response = $this->get(route('admin.infoclase.getDetalles', $clase->id));

        $response->assertStatus(200);
        $response->assertJson([
            'hora' => '09:00-10:00 (60 minutos)',
            'aula' => 'Aula 101',
            'profesor' => 'Profesor: Profesor Test',
            'estadisticas' => [
                'ocupados' => 1,
                'total' => 30,
                'porcentaje' => 3
            ]
        ]);

        // Verificar estructura de asientos ocupados
        $data = $response->json();
        $this->assertArrayHasKey('asientosOcupados', $data);
        $this->assertCount(1, $data['asientosOcupados']);
        $this->assertEquals('A1', $data['asientosOcupados'][0]['asiento']);
        $this->assertEquals('Estudiante Test', $data['asientosOcupados'][0]['estudiante']['nombre']);
    }

    /** @test */
    public function get_detalles_requiere_autenticacion()
    {
        $clase = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
        ]);

        $response = $this->get(route('admin.infoclase.getDetalles', $clase->id));

        $response->assertStatus(302);
    }

    /** @test */
    public function get_detalles_retorna_404_para_clase_inexistente()
    {
        Auth::login($this->teacher);

        $response = $this->get(route('admin.infoclase.getDetalles', 999999));

        $response->assertStatus(404);
    }


    /** @test */
    public function clases_se_ordenan_por_fecha_ascendente()
    {
        Auth::login($this->teacher);

        // Crear clases en diferentes fechas
        $clase1 = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => Carbon::today()->addDays(3)->format('Y-m-d'),
        ]);

        $clase2 = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => Carbon::today()->addDay()->format('Y-m-d'),
        ]);

        $clase3 = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => Carbon::today()->addDays(2)->format('Y-m-d'),
        ]);

        $response = $this->get(route('admin.infoclase.index'));

        $response->assertStatus(200);

        $clases = $response->viewData('clases');

        // Verificar que están ordenadas por fecha (más próxima primero)
        $this->assertEquals($clase2->id, $clases[0]['id']); // Mañana
        $this->assertEquals($clase3->id, $clases[1]['id']); // Pasado mañana
        $this->assertEquals($clase1->id, $clases[2]['id']); // En 3 días
    }

    /** @test */
    public function estadisticas_se_calculan_correctamente()
    {
        Auth::login($this->teacher);

        $clase = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
        ]);

        // Crear 9 estudiantes y reservas (30% del aula con capacidad 30)
        for ($i = 1; $i <= 9; $i++) {
            $estudiante = User::create([
                'nombre' => "Estudiante{$i}",
                'apellido' => 'Test',
                'email' => "estudiante{$i}@test.com",
                'password' => bcrypt('password'),
                'rol' => 'profesor',
                'numero_matricula' => 700000 + $i,
            ]);

            Reservation::create([
                'user_id' => $estudiante->id,
                'class_id' => $clase->id,
                'asiento' => "A{$i}",
                'estado' => 'Completada',
                'justificado' => false,
            ]);
        }

        $response = $this->get(route('admin.infoclase.getDetalles', $clase->id));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertEquals(9, $data['estadisticas']['ocupados']);
        $this->assertEquals(30, $data['estadisticas']['total']);
        $this->assertEquals(30, $data['estadisticas']['porcentaje']); // 9/30 = 30%
    }

    /** @test */
    public function index_sin_clases_retorna_array_vacio()
    {
        Auth::login($this->teacher);

        $response = $this->get(route('admin.infoclase.index'));

        $response->assertStatus(200);
        $clases = $response->viewData('clases');
        $this->assertCount(0, $clases);
    }

    /** @test */
    public function fecha_se_formatea_en_espanol()
    {
        Auth::login($this->teacher);

        $clase = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => '2025-06-15', // Domingo
        ]);

        $response = $this->get(route('admin.infoclase.getDetalles', $clase->id));

        $response->assertStatus(200);
        $data = $response->json();

        // Verificar que la fecha contiene texto en español
        $this->assertStringContainsString('de', $data['fecha']);
        $this->assertStringContainsString('junio', $data['fecha']);
    }

    /** @test */
    public function maneja_clases_con_datos_completos()
    {
        Auth::login($this->teacher);

        // ✅ CAMBIO: Crear clase con todos los campos requeridos
        $clase = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
        ]);

        $response = $this->get(route('admin.infoclase.index'));

        $response->assertStatus(200);

        $clases = $response->viewData('clases');
        $claseData = $clases[0];

        //  CAMBIO: Verificar que los datos se muestran correctamente
        $this->assertEquals('Matemáticas', $claseData['nombre']);
        $this->assertEquals('Aula 101', $claseData['aula']);
        $this->assertEquals('Profesor Test', $claseData['profesor']);
        $this->assertEquals($clase->id, $claseData['id']);
    }
    

    /** @test */
    public function test_flujo_completo_info_clase()
    {
        Auth::login($this->teacher);

        // 1. Crear clase con reservas
        $clase = ClassSession::create([
            'subject_id' => $this->subject->id,
            'user_id' => $this->teacher->id,
            'classroom_id' => $this->classroom->id,
            'time_slot_id' => $this->timeSlot->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
        ]);

        $estudiante = User::create([
            'nombre' => 'Estudiante',
            'apellido' => 'Prueba',
            'email' => 'estudiante.prueba@test.com',
            'password' => bcrypt('password'),
            'rol' => 'profesor',
            'numero_matricula' => 888888,
        ]);

        Reservation::create([
            'user_id' => $estudiante->id,
            'class_id' => $clase->id,
            'asiento' => 'B5',
            'estado' => 'Completada',
            'justificado' => false,
        ]);

        // 2. Ver lista de clases
        $response = $this->get(route('admin.infoclase.index'));
        $response->assertStatus(200);
        $clases = $response->viewData('clases');
        $this->assertCount(1, $clases);

        // 3. Ver detalles de la clase
        $response = $this->get(route('admin.infoclase.getDetalles', $clase->id));
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals(1, $data['estadisticas']['ocupados']);
    }
}